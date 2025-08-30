<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\ProductCertificate;
use Illuminate\Support\Facades\Storage;

class ProductCertificateObserver
{
    /**
     * Handle the ProductCertificate "creating" event.
     */
    public function creating(ProductCertificate $productCertificate): void
    {
        $this->processFileData($productCertificate);
    }

    /**
     * Handle the ProductCertificate "updating" event.
     */
    public function updating(ProductCertificate $productCertificate): void
    {
        // Sadece file_path değiştiyse dosya bilgilerini güncelle
        if ($productCertificate->isDirty('file_path')) {
            $this->processFileData($productCertificate);
        }
    }

    /**
     * Handle the ProductCertificate "deleted" event.
     */
    public function deleted(ProductCertificate $productCertificate): void
    {
        //
    }

    /**
     * Handle the ProductCertificate "restored" event.
     */
    public function restored(ProductCertificate $productCertificate): void
    {
        //
    }

    /**
     * Handle the ProductCertificate "force deleted" event.
     */
    public function forceDeleted(ProductCertificate $productCertificate): void
    {
        //
    }

    /**
     * Process file data and set file_name, file_type, file_size
     */
    private function processFileData(ProductCertificate $productCertificate): void
    {
        if (empty($productCertificate->file_path)) {
            return;
        }

        try {
            // Dosya yolundan dosya adını al
            $fileName = basename($productCertificate->file_path);
            
            // Eğer file_name boşsa, sertifika adından oluştur
            if (empty($productCertificate->file_name)) {
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $productCertificate->file_name = ProductCertificate::generateFileName(
                    $productCertificate->name,
                    $extension
                );
            }

            // Dosya türünü belirle
            if (empty($productCertificate->file_type)) {
                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                $productCertificate->file_type = $this->getMimeType($extension);
            }

            // Dosya boyutunu al
            if (empty($productCertificate->file_size) || $productCertificate->file_size === 0) {
                if (Storage::exists($productCertificate->file_path)) {
                    $productCertificate->file_size = Storage::size($productCertificate->file_path);
                } else {
                    $productCertificate->file_size = 0;
                }
            }
        } catch (\Exception $e) {
            // Hata durumunda varsayılan değerler
            $productCertificate->file_size = $productCertificate->file_size ?? 0;
            $productCertificate->file_type = $productCertificate->file_type ?? 'application/octet-stream';
        }
    }

    /**
     * Get MIME type from file extension
     */
    private function getMimeType(string $extension): string
    {
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
