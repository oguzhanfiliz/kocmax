<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\RateLimiter;

class ClearRateLimits extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'rate-limits:clear {--all : Tüm rate limitleri temizle} {--key= : Belirli bir key temizle}';

    /**
     * The console command description.
     */
    protected $description = 'Rate limiting cache\'lerini temizle';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            $this->clearAllRateLimits();
        } elseif ($key = $this->option('key')) {
            $this->clearSpecificRateLimit($key);
        } else {
            $this->clearDealerApplicationRateLimits();
        }

        return Command::SUCCESS;
    }

    /**
     * Tüm rate limitleri temizle
     */
    private function clearAllRateLimits(): void
    {
        $this->info('Tüm rate limitler temizleniyor...');
        
        // Bilinen rate limit key'lerini temizle
        $knownKeys = [
            'dealer-applications',
            'campaigns',
            'public',
            'api'
        ];

        foreach ($knownKeys as $key) {
            $this->clearRateLimitByPattern($key);
        }

        $this->info('✅ Tüm rate limitler temizlendi!');
    }

    /**
     * Belirli bir rate limit key'ini temizle
     */
    private function clearSpecificRateLimit(string $key): void
    {
        $this->info("Rate limit key temizleniyor: {$key}");
        
        $this->clearRateLimitByPattern($key);
        
        $this->info("✅ Rate limit key temizlendi: {$key}");
    }

    /**
     * Dealer application rate limitlerini temizle
     */
    private function clearDealerApplicationRateLimits(): void
    {
        $this->info('Dealer application rate limitleri temizleniyor...');
        
        $this->clearRateLimitByPattern('dealer_application');
        
        $this->info('✅ Dealer application rate limitleri temizlendi!');
    }

    /**
     * Pattern'e göre rate limit temizle
     */
    private function clearRateLimitByPattern(string $pattern): void
    {
        // Bilinen key'leri temizle
        $knownKeys = [
            '127.0.0.1|guest|dealer_application',
            '::1|guest|dealer_application',
            'localhost|guest|dealer_application'
        ];

        foreach ($knownKeys as $key) {
            RateLimiter::clear($key);
            $this->line("  - {$key} temizlendi");
        }
        
        // Cache driver'ını da temizle (opsiyonel)
        try {
            $cache = app('cache');
            if (method_exists($cache, 'flush')) {
                $this->warn("⚠️  Cache driver temizleniyor...");
                $cache->flush();
                $this->line("  - Cache driver temizlendi");
            }
        } catch (\Exception $e) {
            $this->warn("⚠️  Cache driver temizlenemedi: " . $e->getMessage());
        }
    }
}
