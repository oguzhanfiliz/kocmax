<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MultiCurrencyPricingService;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     title="Product",
 *     description="Product resource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Güvenlik Ayakkabısı"),
 *     @OA\Property(property="slug", type="string", example="guvenlik-ayakkabisi"),
 *     @OA\Property(property="description", type="string", example="Yüksek kaliteli güvenlik ayakkabısı"),
 *     @OA\Property(property="sku", type="string", example="GA-001"),
 *     @OA\Property(property="brand", type="string", example="3M"),
 *     @OA\Property(property="gender", type="string", enum={"male", "female", "unisex"}, example="unisex"),
 *     @OA\Property(property="safety_standard", type="string", example="EN ISO 20345:2011"),
 *     @OA\Property(property="is_featured", type="boolean", example=true),
 *     @OA\Property(property="is_bestseller", type="boolean", example=false),
 *     @OA\Property(property="sort_order", type="integer", example=1),
 *     @OA\Property(property="price", type="object",
 *         @OA\Property(property="original", type="number", format="float", example=150.00),
 *         @OA\Property(property="converted", type="number", format="float", example=150.00),
 *         @OA\Property(property="currency", type="string", example="TRY"),
 *         @OA\Property(property="formatted", type="string", example="150,00 ₺")
 *     ),
 *     @OA\Property(property="images", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="image_url", type="string", example="https://example.com/image.jpg"),
 *         @OA\Property(property="alt_text", type="string", example="Product image"),
 *         @OA\Property(property="is_primary", type="boolean", example=true)
 *     )),
 *     @OA\Property(property="categories", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="Güvenlik Ekipmanları"),
 *         @OA\Property(property="slug", type="string", example="guvenlik-ekipmanlari")
 *     )),
 *     @OA\Property(property="variants_count", type="integer", example=5),
 *     @OA\Property(property="in_stock", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-08T10:30:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-08T10:30:00Z")
 * )
 */
class ProductResource extends JsonResource
{
    private MultiCurrencyPricingService $pricingService;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->pricingService = app(MultiCurrencyPricingService::class);
    }

    public function toArray($request): array
    {
        $currency = app()->bound('api_currency') ? app('api_currency') : 'TRY';

        // Calculate price in requested currency
        $originalPrice = (float) $this->base_price;
        $convertedPrice = $this->pricingService->convertPrice($originalPrice, 'TRY', $currency);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'sku' => $this->sku,
            'brand' => $this->brand,
            'gender' => $this->gender,
            'safety_standard' => $this->safety_standard,
            'is_featured' => (bool) $this->is_featured,
            'is_bestseller' => (bool) $this->is_bestseller,
            'sort_order' => $this->sort_order,
            'price' => [
                'original' => $originalPrice,
                'converted' => $convertedPrice,
                'currency' => $currency,
                'formatted' => $this->formatPrice($convertedPrice, $currency),
            ],
            'images' => $this->whenLoaded('images', fn() => 
                $this->images->map(fn($image) => [
                    'id' => $image->id,
                    'image_url' => $image->image_url,
                    'alt_text' => $image->alt_text,
                    'is_primary' => (bool) $image->is_primary,
                ])
            ),
            'categories' => $this->whenLoaded('categories', fn() => 
                $this->categories->map(fn($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                ])
            ),
            'variants_count' => $this->whenCounted('variants'),
            'in_stock' => $this->whenLoaded('variants', fn() => 
                $this->variants->sum('stock') > 0
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    private function formatPrice(float $price, string $currency): string
    {
        return match ($currency) {
            'TRY' => number_format($price, 2, ',', '.') . ' ₺',
            'USD' => '$' . number_format($price, 2, '.', ','),
            'EUR' => number_format($price, 2, ',', '.') . ' €',
            default => number_format($price, 2, '.', ',') . ' ' . $currency,
        };
    }
}