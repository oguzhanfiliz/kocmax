<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopularSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'query',
        'count',
    ];

    protected $casts = [
        'count' => 'integer',
    ];

    /**
     * Arama terimini kaydet veya sayacını artır
     */
    public static function recordSearch(string $query): void
    {
        // Boş veya çok kısa terimler için kaydetme
        if (strlen(trim($query)) < 2) {
            return;
        }

        // Türkçe karakterleri düzelten ve normalize eden fonksiyon
        $normalizedQuery = self::normalizeQuery($query);

        // Arama terimini kaydet veya sayacını artır
        $search = self::firstOrCreate(['query' => $normalizedQuery], ['count' => 1]);
        
        if (!$search->wasRecentlyCreated) {
            $search->increment('count');
        }
    }

    /**
     * En popüler arama terimlerini getir
     */
    public static function getPopular(int $limit = 10): array
    {
        return self::orderByDesc('count')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->pluck('query')
            ->toArray();
    }

    /**
     * Arama terimini normalize et (Türkçe karakter desteği)
     */
    private static function normalizeQuery(string $query): string
    {
        $query = trim($query);
        $query = mb_strtolower($query, 'UTF-8');
        
        // Çoklu boşlukları tek boşluğa çevir
        $query = preg_replace('/\s+/', ' ', $query);
        
        return $query;
    }

    /**
     * Arama önerilerini getir
     */
    public static function getSuggestions(string $query, int $limit = 5): array
    {
        $normalizedQuery = self::normalizeQuery($query);
        
        return self::where('query', 'LIKE', "%{$normalizedQuery}%")
            ->where('query', '!=', $normalizedQuery)
            ->orderByDesc('count')
            ->limit($limit)
            ->pluck('query')
            ->toArray();
    }
}