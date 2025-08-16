<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MultiCurrencyPricingService;
use App\Services\Pricing\CustomerTypeDetectorService;

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
    private CustomerTypeDetectorService $customerTypeDetector;

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->pricingService = app(MultiCurrencyPricingService::class);
        $this->customerTypeDetector = app(CustomerTypeDetectorService::class);
    }

    public function toArray($request): array
    {
        // Get context from app container
        $currency = app()->bound('api_currency') ? app('api_currency') : 'TRY';
        $customerInfo = app()->bound('api_customer_info') ? app('api_customer_info') : [
            'type' => 'guest', 'user' => null, 'is_authenticated' => false, 'is_dealer' => false
        ];
        $smartPricingEnabled = app()->bound('api_smart_pricing_enabled') ? app('api_smart_pricing_enabled') : false;

        // 🎯 Smart Pricing Calculation
        $pricingData = $this->calculateSmartPricing($currency, $customerInfo, $smartPricingEnabled);

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
            
            // 🔥 Enhanced pricing information
            'pricing' => $pricingData,
            
            // Legacy compatibility (will use "your_price" when smart pricing enabled)
            'price' => [
                'original' => $pricingData['base_price'],
                'converted' => $pricingData['your_price'],
                'currency' => $currency,
                'formatted' => $pricingData['your_price_formatted'],
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

    /**
     * 🎯 Smart pricing calculation based on customer type
     */
    private function calculateSmartPricing(string $currency, array $customerInfo, bool $smartPricingEnabled): array
    {
        $basePrice = (float) $this->base_price;
        $user = $customerInfo['user'];
        
        // Base currency conversion
        $basePriceConverted = $this->pricingService->convertPrice($basePrice, 'TRY', $currency);
        
        if (!$smartPricingEnabled) {
            // Legacy mode - just return base price
            return [
                'base_price' => $basePriceConverted,
                'your_price' => $basePriceConverted,
                'your_price_formatted' => $this->formatPrice($basePriceConverted, $currency),
                'currency' => $currency,
                'price_type' => 'Liste Fiyatı',
                'discount_percentage' => 0.0,
                'discount_amount' => 0.0,
                'savings_amount' => 0.0,
                'smart_pricing_enabled' => false,
            ];
        }
        
        // 🔥 Smart Pricing Logic
        $discountPercentage = $this->customerTypeDetector->getDiscountPercentage($user);
        $discountAmount = $basePriceConverted * ($discountPercentage / 100);
        $yourPrice = $basePriceConverted - $discountAmount;
        $priceType = $this->customerTypeDetector->getTypeLabel($customerInfo['type']);
        
        return [
            'base_price' => $basePriceConverted,
            'your_price' => $yourPrice,
            'your_price_formatted' => $this->formatPrice($yourPrice, $currency),
            'base_price_formatted' => $this->formatPrice($basePriceConverted, $currency),
            'currency' => $currency,
            'price_type' => $priceType,
            'customer_type' => $customerInfo['type'],
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'savings_amount' => $discountAmount,
            'smart_pricing_enabled' => true,
            'is_dealer_price' => $customerInfo['is_dealer'],
            'pricing_tier' => $user?->pricingTier?->name,
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