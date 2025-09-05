<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImageOptimizationService;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class OptimizeExistingImages extends Command
{
    protected $signature = 'images:optimize {--force : Zorla optimize et} {--limit=50 : İşlenecek resim sayısı}';
    protected $description = 'Mevcut resimleri WebP formatına dönüştür ve optimize et';

    public function handle(): int
    {
        $this->info('🖼️ Mevcut Resimler Optimize Ediliyor...');
        
        $imageOptimizationService = app(ImageOptimizationService::class);
        $limit = (int) $this->option('limit');
        $force = $this->option('force');
        
        // ProductImage'ları al
        $productImages = ProductImage::whereNotNull('image')
            ->limit($limit)
            ->get();
        
        if ($productImages->isEmpty()) {
            $this->warn('❌ Optimize edilecek resim bulunamadı');
            return 0;
        }
        
        $this->info("📊 {$productImages->count()} resim bulundu");
        
        $progressBar = $this->output->createProgressBar($productImages->count());
        $progressBar->start();
        
        $optimized = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($productImages as $productImage) {
            try {
                $imagePath = $productImage->image;
                
                if (!Storage::disk('public')->exists($imagePath)) {
                    $this->newLine();
                    $this->warn("⚠️ Resim bulunamadı: {$imagePath}");
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
                
                $result = $imageOptimizationService->optimizeToWebP($uploadedFile, 'products', 85);
                
                if ($result['success']) {
                    // Eski dosyayı sil
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                    
                    // Yeni dosya yolunu güncelle
                    $productImage->update(['image' => $result['path']]);
                    
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
        $this->info('📊 Optimizasyon Sonuçları:');
        $this->line("  ✅ Optimize edilen: {$optimized}");
        $this->line("  ⏭️ Atlanan: {$skipped}");
        $this->line("  ❌ Hata: {$errors}");
        
        if ($optimized > 0) {
            $this->info('🎉 Optimizasyon tamamlandı!');
        }
        
        return 0;
    }
}
