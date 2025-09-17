<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProductImage;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProductImageObserver
{
    private ImageOptimizationService $imageOptimizationService;

    public function __construct(ImageOptimizationService $imageOptimizationService)
    {
        $this->imageOptimizationService = $imageOptimizationService;
    }

    /**
     * Resim yüklendiğinde otomatik optimize et
     */
    public function creating(ProductImage $productImage): void
    {
        if ($productImage->image && is_string($productImage->image)) {
            $this->optimizeImage($productImage);
        }
    }

    /**
     * Resim güncellendiğinde optimize et
     */
    public function updating(ProductImage $productImage): void
    {
        if ($productImage->isDirty('image') && $productImage->image) {
            $this->optimizeImage($productImage);
        }
    }

    /**
     * Resmi optimize et
     */
    private function optimizeImage(ProductImage $productImage): void
    {
        try {
            $imagePath = storage_path('app/public/' . $productImage->image);
            
            if (!file_exists($imagePath)) {
                Log::warning("Resim dosyası bulunamadı: {$imagePath}");
                return;
            }

            // Resim istatistiklerini al
            $stats = $this->imageOptimizationService->getImageStats($productImage->image);
            
            if (isset($stats['error'])) {
                Log::error("Resim istatistikleri alınamadı: {$stats['error']}");
                return;
            }

            // JPG/PNG dosyalarını otomatik WebP'ye dönüştür
            $shouldConvert = false;
            $reason = '';
            
            // WebP değilse ve JPG/PNG ise dönüştür
            if (!in_array($stats['format'], ['webp'])) {
                $shouldConvert = true;
                $reason = "Format dönüşümü: {$stats['format']} -> WebP";
            }
            // Veya çok büyükse optimize et
            elseif ($stats['size'] > 500 * 1024) {
                $shouldConvert = true;
                $reason = "Boyut optimizasyonu: {$stats['size_formatted']}";
            }
            
            if ($shouldConvert) {
                Log::info("Resim optimize ediliyor: {$productImage->image} ({$stats['size_formatted']}) - {$reason}");
                
                // WebP formatına dönüştür
                $optimizedResult = $this->imageOptimizationService->optimizeToWebP(
                    new \Illuminate\Http\UploadedFile(
                        $imagePath,
                        basename($imagePath),
                        mime_content_type($imagePath),
                        null,
                        true
                    ),
                    'products',
                    85
                );

                if ($optimizedResult['success']) {
                    // Eski dosyayı sil
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    
                    // Yeni dosya yolunu güncelle
                    $productImage->image = $optimizedResult['path'];
                    
                    // İlgili cache'leri temizle
                    Cache::forget('products.index');
                    Cache::forget('products.show.' . $productImage->product_id);
                    
                    Log::info("Resim optimize edildi: {$optimizedResult['filename']} ({$this->imageOptimizationService->formatFileSize($optimizedResult['size'])}) - {$reason}");
                } else {
                    Log::error("Resim optimizasyonu başarısız: {$optimizedResult['error']}");
                }
            } else {
                Log::info("Resim zaten optimize edilmiş: {$productImage->image} ({$stats['format']}, {$stats['size_formatted']})");
            }
            
        } catch (\Exception $e) {
            Log::error("Resim optimizasyonu hatası: {$e->getMessage()}");
        }
    }
}
