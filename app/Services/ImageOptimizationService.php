<?php

declare(strict_types=1);

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizationService
{
    private ImageManager $imageManager;
    
    public function __construct()
    {
        $this->imageManager = new ImageManager(new Driver());
    }

    /**
     * Resmi WebP formatına dönüştür ve optimize et
     */
    public function optimizeToWebP(UploadedFile $file, string $directory = 'products', int $quality = 85, bool $preserveBasename = false): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if ($preserveBasename) {
            // Batch optimizations may rely on same-basename detection (foo.jpg -> foo.webp)
            $webpFileName = $originalName . '.webp';
        } else {
            $webpFileName = Str::slug($originalName) . '_' . time() . '.webp';
        }
        $webpPath = $directory . '/' . $webpFileName;
        
        try {
            // Resmi oku ve optimize et
            $image = $this->imageManager->read($file->getPathname());
            
            // Boyutları kontrol et ve gerekirse küçült
            $image = $this->resizeIfNeeded($image, 1920, 1080);
            
            // WebP formatında kaydet
            $image->toWebp($quality);
            
            // Storage'a kaydet
            $fullPath = Storage::disk('public')->path($webpPath);
            $image->save($fullPath);
            
            return [
                'success' => true,
                'path' => $webpPath,
                'filename' => $webpFileName,
                'size' => filesize($fullPath),
                'format' => 'webp'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Resmi farklı boyutlarda optimize et (thumbnail, medium, large)
     */
    public function createMultipleSizes(UploadedFile $file, string $directory = 'products'): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $baseName = Str::slug($originalName) . '_' . time();
        
        $sizes = [
            'thumbnail' => ['width' => 300, 'height' => 300, 'quality' => 80],
            'medium' => ['width' => 800, 'height' => 600, 'quality' => 85],
            'large' => ['width' => 1920, 'height' => 1080, 'quality' => 90],
        ];
        
        $results = [];
        
        try {
            $image = $this->imageManager->read($file->getPathname());
            
            foreach ($sizes as $sizeName => $config) {
                $resizedImage = clone $image;
                $resizedImage = $this->resizeIfNeeded($resizedImage, $config['width'], $config['height']);
                
                $fileName = $baseName . '_' . $sizeName . '.webp';
                $filePath = $directory . '/' . $fileName;
                
                $resizedImage->toWebp($config['quality']);
                $fullPath = Storage::disk('public')->path($filePath);
                $resizedImage->save($fullPath);
                
                $results[$sizeName] = [
                    'path' => $filePath,
                    'filename' => $fileName,
                    'size' => filesize($fullPath),
                    'width' => $config['width'],
                    'height' => $config['height']
                ];
            }
            
            return [
                'success' => true,
                'sizes' => $results
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Resmi gerekirse yeniden boyutlandır
     */
    private function resizeIfNeeded($image, int $maxWidth, int $maxHeight)
    {
        $currentWidth = $image->width();
        $currentHeight = $image->height();
        
        // Eğer resim maksimum boyutlardan küçükse, boyutlandırma
        if ($currentWidth <= $maxWidth && $currentHeight <= $maxHeight) {
            return $image;
        }
        
        // Orantılı boyutlandırma
        $ratio = min($maxWidth / $currentWidth, $maxHeight / $currentHeight);
        $newWidth = (int) ($currentWidth * $ratio);
        $newHeight = (int) ($currentHeight * $ratio);
        
        return $image->resize($newWidth, $newHeight);
    }

    /**
     * Resim boyutunu küçült (kalite ayarı ile)
     */
    public function compressImage(UploadedFile $file, string $directory = 'products', int $quality = 75): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '_compressed_' . time() . '.' . $extension;
        $filePath = $directory . '/' . $fileName;
        
        try {
            $image = $this->imageManager->read($file->getPathname());
            
            // Boyutları küçült
            $image = $this->resizeIfNeeded($image, 1200, 800);
            
            // Kalite ayarı ile kaydet
            if ($extension === 'jpg' || $extension === 'jpeg') {
                $image->toJpeg($quality);
            } elseif ($extension === 'png') {
                $image->toPng();
            } else {
                $image->toWebp($quality);
            }
            
            $fullPath = Storage::disk('public')->path($filePath);
            $image->save($fullPath);
            
            return [
                'success' => true,
                'path' => $filePath,
                'filename' => $fileName,
                'size' => filesize($fullPath),
                'format' => $extension
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Dosya boyutunu insan okunabilir formata çevir
     */
    public function formatFileSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Resim istatistiklerini al
     */
    public function getImageStats(string $filePath): array
    {
        $fullPath = Storage::disk('public')->path($filePath);
        
        if (!file_exists($fullPath)) {
            return ['error' => 'Dosya bulunamadı'];
        }
        
        try {
            $image = $this->imageManager->read($fullPath);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'size' => filesize($fullPath),
                'size_formatted' => $this->formatFileSize(filesize($fullPath)),
                'format' => pathinfo($filePath, PATHINFO_EXTENSION)
            ];
            
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
