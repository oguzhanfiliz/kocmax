<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProductImage;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Log;

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

            // Eğer resim çok büyükse optimize et
            if ($stats['size'] > 500 * 1024) { // 500KB'den büyükse
                Log::info("Resim optimize ediliyor: {$productImage->image} ({$stats['size_formatted']})");
                
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
                    
                    Log::info("Resim optimize edildi: {$optimizedResult['filename']} ({$this->imageOptimizationService->formatFileSize($optimizedResult['size'])})");
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Resim optimizasyonu hatası: {$e->getMessage()}");
        }
    }
}
