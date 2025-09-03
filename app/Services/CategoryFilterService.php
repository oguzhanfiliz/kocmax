<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryFilterService
{
    /**
     * Kategori ID'sine göre ürünleri filtrele (nested kategoriler dahil)
     */
    public function filterProductsByCategory(Builder $query, int $categoryId): Builder
    {
        $category = Category::select(['id', 'parent_id'])->find($categoryId);
        
        if (!$category) {
            return $query;
        }

        // Ana kategori ve tüm alt kategorilerin ID'lerini al
        $categoryIds = $this->getAllCategoryIds($category);
        
        return $query->whereHas('categories', function (Builder $q) use ($categoryIds) {
            $q->whereIn('categories.id', $categoryIds);
        });
    }

    /**
     * Kategoriye ait tüm alt kategorilerin ID'lerini recursive olarak al
     */
    public function getAllCategoryIds(Category $category): Collection
    {
        $cacheKey = "category_children_{$category->id}";
        
        return Cache::remember($cacheKey, 1800, function () use ($category) {
            $ids = collect([$category->id]);
            
            // Alt kategorileri recursive olarak al
            $this->addChildCategoryIds($category, $ids);
            
            return $ids;
        });
    }

    /**
     * Alt kategorilerin ID'lerini recursive olarak ekle
     */
    private function addChildCategoryIds(Category $category, Collection $ids): void
    {
        $children = $category->children()
            ->select(['id', 'parent_id'])
            ->active()
            ->get();
        
        foreach ($children as $child) {
            $ids->push($child->id);
            $this->addChildCategoryIds($child, $ids);
        }
    }

    /**
     * Hiyerarşik kategori listesini al (filtreleme için)
     */
    public function getHierarchicalCategories(): Collection
    {
        return Cache::remember('hierarchical_categories', 3600, function () {
            return Category::active()
                ->parents()
                ->select(['id', 'name', 'parent_id', 'is_active', 'sort_order'])
                ->with(['children' => function ($query) {
                    $query->select(['id', 'name', 'parent_id', 'is_active', 'sort_order'])
                          ->active()
                          ->ordered();
                }])
                ->ordered()
                ->get();
        });
    }

    /**
     * Kategoriye göre ürün sıralama
     */
    public function sortProductsByCategory(Builder $query, int $categoryId, string $sortBy = 'sort_order', string $direction = 'asc'): Builder
    {
        return $query->orderBy('sort_order', $direction)
                    ->orderBy('name', $direction);
    }

    /**
     * Cache'i temizle (admin panel'den kategori güncellendiğinde)
     */
    public function clearCache(): void
    {
        Cache::forget('hierarchical_categories');
        Cache::forget('category_filter_options');
        
        // Tüm kategori cache'lerini temizle
        $categories = Category::select(['id'])->get();
        foreach ($categories as $category) {
            Cache::forget("category_children_{$category->id}");
        }
    }
}
