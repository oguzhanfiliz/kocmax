<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProductCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'file_size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Dosya silindiğinde fiziksel dosyayı da sil
        static::deleted(function ($certificate) {
            if (Storage::exists($certificate->file_path)) {
                Storage::delete($certificate->file_path);
            }
        });
    }

    /**
     * Product relationship
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute(): string
    {
        return url(Storage::url($this->file_path));
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        
        // Eğer bytes 0 ise veya null ise
        if ($bytes <= 0) {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }



    /**
     * Active certificates scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }



    /**
     * Ordered certificates scope
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Search certificates by name or description
     */
    public function scopeSearch($query, $term)
    {
        if (empty(trim($term))) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Generate unique file name from certificate name
     */
    public static function generateFileName(string $certificateName, string $originalExtension): string
    {
        // Türkçe karakterleri ve özel karakterleri temizle
        $cleanName = self::cleanFileName($certificateName);
        
        // Uzantıyı ekle
        $fileName = $cleanName . '.' . $originalExtension;
        
        // Aynı isimde dosya varsa sayı ekle
        $counter = 1;
        $originalFileName = $fileName;
        
        while (self::where('file_name', $fileName)->exists()) {
            $fileName = $cleanName . '_' . $counter . '.' . $originalExtension;
            $counter++;
        }
        
        return $fileName;
    }

    /**
     * Clean file name for safe storage
     */
    public static function cleanFileName(string $name): string
    {
        // Dosya uzantısını ayır
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $nameWithoutExtension = pathinfo($name, PATHINFO_FILENAME);
        
        // Türkçe karakterleri değiştir
        $turkishChars = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü'];
        $englishChars = ['c', 'g', 'i', 'o', 's', 'u', 'C', 'G', 'I', 'I', 'O', 'S', 'U'];
        
        $nameWithoutExtension = str_replace($turkishChars, $englishChars, $nameWithoutExtension);
        
        // Sadece harf, rakam, tire ve alt çizgi bırak
        $nameWithoutExtension = preg_replace('/[^a-zA-Z0-9\-\_]/', '_', $nameWithoutExtension);
        
        // Birden fazla alt çizgiyi tek alt çizgiye çevir
        $nameWithoutExtension = preg_replace('/_+/', '_', $nameWithoutExtension);
        
        // Başındaki ve sonundaki alt çizgileri kaldır
        $nameWithoutExtension = trim($nameWithoutExtension, '_');
        
        // Boşsa default isim ver
        if (empty($nameWithoutExtension)) {
            $nameWithoutExtension = 'sertifika';
        }
        
        // Uzantıyı geri ekle
        if (!empty($extension)) {
            return $nameWithoutExtension . '.' . $extension;
        }
        
        return $nameWithoutExtension;
    }
}
