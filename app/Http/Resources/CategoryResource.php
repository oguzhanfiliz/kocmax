<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Category",
 *     title="Category",
 *     description="Category data",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="İş Güvenliği Ekipmanları"),
 *     @OA\Property(property="slug", type="string", example="is-guvenligi-ekipmanlari"),
 *     @OA\Property(property="description", type="string", nullable=true, example="İş güvenliği için gerekli ekipmanlar"),
 *     @OA\Property(property="image_url", type="string", nullable=true, example="https://example.com/category-image.jpg"),
 *     @OA\Property(property="icon", type="string", nullable=true, example="heroicon-o-shield-check"),
 *     @OA\Property(property="sort_order", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="is_in_menu", type="boolean", example=true),
 *     @OA\Property(property="parent_id", type="integer", nullable=true, example=null),
 *     @OA\Property(property="level", type="integer", example=0),
 *     @OA\Property(property="products_count", type="integer", example=25),
 *     @OA\Property(property="children_count", type="integer", example=3),
 *     @OA\Property(property="parent", type="object", nullable=true, ref="#/components/schemas/CategoryParent"),
 *     @OA\Property(property="children", type="array", nullable=true, @OA\Items(ref="#/components/schemas/CategoryChild")),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-08T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-08T10:30:00Z")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryParent",
 *     title="Category Parent",
 *     description="Parent category data",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ana Kategori"),
 *     @OA\Property(property="slug", type="string", example="ana-kategori")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryChild",
 *     title="Category Child",
 *     description="Child category data",
 *     @OA\Property(property="id", type="integer", example=2),
 *     @OA\Property(property="name", type="string", example="Alt Kategori"),
 *     @OA\Property(property="slug", type="string", example="alt-kategori"),
 *     @OA\Property(property="products_count", type="integer", example=10),
 *     @OA\Property(property="icon", type="string", nullable=true, example="heroicon-o-shield-check"),
 *     @OA\Property(property="is_featured", type="boolean", example=false),
 *     @OA\Property(property="is_in_menu", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="CategoryTree",
 *     title="Category Tree",
 *     description="Hierarchical category tree structure",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Ana Kategori"),
 *     @OA\Property(property="slug", type="string", example="ana-kategori"),
 *     @OA\Property(property="children", type="array", @OA\Items(ref="#/components/schemas/CategoryChild"))
 * )
 *
 * @OA\Schema(
 *     schema="CategoryBreadcrumb",
 *     title="Category Breadcrumb",
 *     description="Category breadcrumb item",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Kategori Adı"),
 *     @OA\Property(property="slug", type="string", example="kategori-adi")
 * )
 */
class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image_url,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'is_active' => (bool) $this->is_active,
            'is_featured' => (bool) $this->is_featured,
            'is_in_menu' => (bool) $this->is_in_menu,
            'parent_id' => $this->parent_id,
            'level' => $this->calculateLevel(),
            'products_count' => $this->whenCounted('products'),
            'children_count' => $this->whenCounted('children'),
            
            // 🔍 SEO Information
            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
            ],
            
            // Parent category information
            'parent' => $this->whenLoaded('parent', function () {
                return $this->parent ? [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name,
                    'slug' => $this->parent->slug,
                ] : null;
            }),
            
            // Children categories
            'children' => $this->whenLoaded('children', function () {
                return $this->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                        'products_count' => $child->products_count ?? 0,
                        'icon' => $child->icon,
                        'is_featured' => (bool) $child->is_featured,
                        'is_in_menu' => (bool) $child->is_in_menu,
                    ];
                });
            }),
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Calculate category level based on parent hierarchy
     */
    private function calculateLevel(): int
    {
        $level = 0;
        $current = $this->resource;
        
        while ($current && $current->parent_id) {
            $level++;
            $current = $current->parent;
            
            // Prevent infinite loops
            if ($level > 10) {
                break;
            }
        }
        
        return $level;
    }
}
