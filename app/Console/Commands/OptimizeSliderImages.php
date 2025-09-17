<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;
use App\Models\Slider;
use Illuminate\Support\Facades\Storage;

class OptimizeSliderImages extends Command
{
    protected $signature = 'sliders:optimize {--force : Zorla optimize et} {--limit=50 : İşlenecek resim sayısı}';
    protected $description = 'Mevcut slider resimlerini WebP formatına dönüştür ve optimize et';

    public function handle(): int
    {
        $this->info('🖼️ Slider Resimleri Optimize Ediliyor...');
        
        $imageOptimizationService = app(ImageOptimizationService::class);
        $limit = (int) $this->option('limit');
        $force = $this->option('force');
        
        // Slider'ları al
        $sliders = Slider::whereNotNull('image_url')
            ->limit($limit)
            ->get();
        
        if ($sliders->isEmpty()) {
            $this->warn('❌ Optimize edilecek slider resmi bulunamadı');
            return 0;
        }
        
        $this->info("📊 {$sliders->count()} slider resmi bulundu");
        
        $progressBar = $this->output->createProgressBar($sliders->count());
        $progressBar->start();
        
        $optimized = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($sliders as $slider) {
            try {
                $imagePath = $slider->image_url;
                
                if (!Storage::disk('public')->exists($imagePath)) {
                    $this->newLine();
                    $this->warn("⚠️ Slider resmi bulunamadı: {$imagePath}");
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Resim istatistiklerini al
                $stats = $imageOptimizationService->getImageStats($imagePath);
                
                if (isset($stats['error'])) {
                    $this->newLine();
                    $this->error("❌ Hata: {$stats['error']}");
                    $errors++;
                    $progressBar->advance();
                    continue;
                }
                
                // Eğer zaten WebP formatındaysa ve force değilse atla
                if (!$force && $stats['format'] === 'webp' && $stats['size'] < 500 * 1024) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }
                
                // Optimize et
                $fullPath = Storage::disk('public')->path($imagePath);
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($imagePath),
                    mime_content_type($fullPath),
                    null,
                    true
                );
                
                $result = $imageOptimizationService->optimizeToWebP($uploadedFile, 'sliders', 85);
                
                if ($result['success']) {
                    // Eski dosyayı sil
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                    
                    // Yeni dosya yolunu güncelle
                    $slider->update(['image_url' => $result['path']]);
                    
                    $optimized++;
                } else {
                    $this->newLine();
                    $this->error("❌ Optimizasyon hatası: {$result['error']}");
                    $errors++;
                }
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Genel hata: {$e->getMessage()}");
                $errors++;
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        // Sonuçları göster
        $this->info('📊 Slider Optimizasyon Sonuçları:');
        $this->line("  ✅ Optimize edilen: {$optimized}");
        $this->line("  ⏭️ Atlanan: {$skipped}");
        $this->line("  ❌ Hata: {$errors}");
        
        if ($optimized > 0) {
            $this->info('🎉 Slider optimizasyonu tamamlandı!');
        }
        
        return 0;
    }
}
