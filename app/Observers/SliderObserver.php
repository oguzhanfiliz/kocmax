<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Slider;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SliderObserver
{
    private ImageOptimizationService $imageOptimizationService;

    public function __construct(ImageOptimizationService $imageOptimizationService)
    {
        $this->imageOptimizationService = $imageOptimizationService;
    }

    /**
     * Slider resmi yüklendiğinde otomatik optimize et
     */
    public function creating(Slider $slider): void
    {
        if ($slider->image_url && is_string($slider->image_url)) {
            $this->optimizeImage($slider);
        }
    }

    /**
     * Slider resmi güncellendiğinde optimize et
     */
    public function updating(Slider $slider): void
    {
        if ($slider->isDirty('image_url') && $slider->image_url) {
            $this->optimizeImage($slider);
        }
    }

    /**
     * Slider resmini optimize et
     */
    private function optimizeImage(Slider $slider): void
    {
        try {
            $imagePath = storage_path('app/public/' . $slider->image_url);
            
            if (!file_exists($imagePath)) {
                Log::warning("Slider resim dosyası bulunamadı: {$imagePath}");
                return;
            }

            // Resim istatistiklerini al
            $stats = $this->imageOptimizationService->getImageStats($slider->image_url);
            
            if (isset($stats['error'])) {
                Log::error("Slider resim istatistikleri alınamadı: {$stats['error']}");
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
                Log::info("Slider resmi optimize ediliyor: {$slider->image_url} ({$stats['size_formatted']}) - {$reason}");
                
                // WebP formatına dönüştür
                $optimizedResult = $this->imageOptimizationService->optimizeToWebP(
                    new \Illuminate\Http\UploadedFile(
                        $imagePath,
                        basename($imagePath),
                        mime_content_type($imagePath),
                        null,
                        true
                    ),
                    'sliders',
                    85
                );

                if ($optimizedResult['success']) {
                    // Eski dosyayı sil
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    
                    // Yeni dosya yolunu güncelle
                    $slider->image_url = $optimizedResult['path'];
                    
                    // API cache'ini temizle
                    Cache::forget('sliders.index');
                    
                    Log::info("Slider resmi optimize edildi: {$optimizedResult['filename']} ({$this->imageOptimizationService->formatFileSize($optimizedResult['size'])}) - {$reason}");
                } else {
                    Log::error("Slider resim optimizasyonu başarısız: {$optimizedResult['error']}");
                }
            } else {
                Log::info("Slider resmi zaten optimize edilmiş: {$slider->image_url} ({$stats['format']}, {$stats['size_formatted']})");
            }
            
        } catch (\Exception $e) {
            Log::error("Slider resim optimizasyonu hatası: {$e->getMessage()}");
        }
    }
}
