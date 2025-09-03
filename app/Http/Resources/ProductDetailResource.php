<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MultiCurrencyPricingService;
use App\Services\Pricing\CustomerTypeDetectorService;

/**
 * @OA\Schema(
 *     schema="ProductDetail",
 *     type="object",
 *     title="Product Detail",
 *     description="Detailed product resource with variants and reviews",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Product")
 *     },
 *     @OA\Property(property="variants", type="array", @OA\Items(
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="color", type="string", example="Siyah"),
 *         @OA\Property(property="size", type="string", example="42"),
 *         @OA\Property(property="stock", type="integer", example=25),
 *         @OA\Property(property="price", type="object",
 *             @OA\Property(property="original", type="number", format="float", example=150.00),
 *             @OA\Property(property="converted", type="number", format="float", example=150.00),
 *             @OA\Property(property="currency", type="string", example="TRY"),
 *             @OA\Property(property="formatted", type="string", example="150,00 ₺")
 *         ),
 *         @OA\Property(property="images", type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="image_url", type="string", example="https://example.com/variant.jpg"),
 *             @OA\Property(property="alt_text", type="string", example="Variant image")
 *         ))
 *     )),
 *     @OA\Property(property="reviews", type="object",
 *         @OA\Property(property="average_rating", type="number", format="float", example=4.5),
 *         @OA\Property(property="total_reviews", type="integer", example=23),
 *         @OA\Property(property="rating_distribution", type="object",
 *             @OA\Property(property="5", type="integer", example=15),
 *             @OA\Property(property="4", type="integer", example=5),
 *             @OA\Property(property="3", type="integer", example=2),
 *             @OA\Property(property="2", type="integer", example=1),
 *             @OA\Property(property="1", type="integer", example=0)
 *         ),
 *         @OA\Property(property="recent_reviews", type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="rating", type="integer", example=5),
 *             @OA\Property(property="comment", type="string", example="Excellent product!"),
 *             @OA\Property(property="user_name", type="string", example="John D."),
 *             @OA\Property(property="created_at", type="string", format="date-time")
 *         ))
 *     ),
 *     @OA\Property(property="related_products", type="array", @OA\Items(ref="#/components/schemas/Product")),
 *     @OA\Property(property="specifications", type="object"),
 *     @OA\Property(property="meta", type="object",
 *         @OA\Property(property="total_stock", type="integer", example=125),
 *         @OA\Property(property="lowest_price", type="number", format="float", example=140.00),
 *         @OA\Property(property="highest_price", type="number", format="float", example=160.00)
 *     )
 * )
 */
class ProductDetailResource extends JsonResource
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
        $currency = 'TRY';

        // Base product data
        $baseData = (new ProductResource($this->resource))->toArray($request);

        // Add detailed information
        return array_merge($baseData, [
            'seo' => [
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
            ],
            'variants' => $this->whenLoaded('variants', fn() => 
                $this->variants->map(fn($variant) => [
                    'id' => $variant->id,
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'stock' => $variant->stock,
                    'price' => [
                        'original' => (float) $variant->source_price ?? (float) $variant->price,
                        'converted' => app(\App\Services\CurrencyConversionService::class)->convertPrice(
                            (float) ($variant->source_price ?? $variant->price),
                            $variant->source_currency ?? ($variant->currency_code ?? 'TRY'),
                            'TRY'
                        ),
                        'currency' => 'TRY',
                        'formatted' => $this->formatPrice(
                            app(\App\Services\CurrencyConversionService::class)->convertPrice(
                                (float) ($variant->source_price ?? $variant->price),
                                $variant->source_currency ?? ($variant->currency_code ?? 'TRY'),
                                'TRY'
                            ),
                            'TRY'
                        ),
                    ],
                    'images' => $variant->relationLoaded('images') ? 
                        $variant->images->map(fn($image) => [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                            'is_primary' => (bool) $image->is_primary,
                            'sort_order' => $image->sort_order,
                        ]) : [],
                    // 🔥 Varyant için de pricing rules uygula
                    'pricing' => $this->calculateVariantPricing($variant),
                    // 📦 Varyant paket boyutları (inheritance ile)
                    'package_dimensions' => $variant->getPackageDimensionsWithIcons(),
                    // 🎨 Varyant türleri ve seçenekleri (Frontend için optimize edilmiş)
                    'variant_types' => $variant->relationLoaded('variantOptions') ? 
                        $this->getVariantTypesForFrontend($variant) : [],
                ])
            ),
            'reviews' => $this->whenLoaded('reviews', function () {
                $reviews = $this->reviews;
                $totalReviews = $reviews->count();
                $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;
                
                // Rating distribution
                $ratingDistribution = [
                    '5' => $reviews->where('rating', 5)->count(),
                    '4' => $reviews->where('rating', 4)->count(),
                    '3' => $reviews->where('rating', 3)->count(),
                    '2' => $reviews->where('rating', 2)->count(),
                    '1' => $reviews->where('rating', 1)->count(),
                ];

                return [
                    'average_rating' => round($averageRating, 1),
                    'total_reviews' => $totalReviews,
                    'rating_distribution' => $ratingDistribution,
                    'recent_reviews' => $reviews->take(5)->map(fn($review) => [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'comment' => $review->comment,
                        'user_name' => $review->user ? 
                            substr($review->user->name, 0, strpos($review->user->name, ' ') ?: strlen($review->user->name)) . ' ' . 
                            substr($review->user->name, (int) strpos($review->user->name, ' ') + 1, 1) . '.'
                            : 'Anonymous',
                        'created_at' => $review->created_at?->toISOString(),
                    ]),
                ];
            }),
            'certificates' => $this->whenLoaded('activeCertificates', fn() => 
                $this->activeCertificates->map(fn($certificate) => [
                    'id' => $certificate->id,
                    'name' => $certificate->name,
                    'description' => $certificate->description,
                    'file_name' => $certificate->file_name,
                    'file_type' => $certificate->file_type,
                    'file_size_human' => $certificate->file_size_human,
                    'file_url' => $certificate->file_url,
                    'sort_order' => $certificate->sort_order,
                ])
            ),
            'specifications' => $this->specifications ?? (object) [],
            'meta' => $this->whenLoaded('variants', function () use ($currency) {
                $variants = $this->variants;
                $totalStock = $variants->sum('stock');
                
                if ($variants->count() > 0) {
                    $prices = $variants->map(fn($variant) => 
                        app(\App\Services\CurrencyConversionService::class)->convertPrice(
                            (float) ($variant->source_price ?? $variant->price),
                            $variant->source_currency ?? ($variant->currency_code ?? 'TRY'),
                            'TRY'
                        )
                    );
                    
                    return [
                        'total_stock' => $totalStock,
                        'lowest_price' => $prices->min(),
                        'highest_price' => $prices->max(),
                    ];
                }
                
                return [
                    'total_stock' => $totalStock,
                    'lowest_price' => null,
                    'highest_price' => null,
                ];
            }),
        ]);
    }

    /**
     * 🎯 Varyant için smart pricing calculation
     */
    private function calculateVariantPricing($variant): array
    {
        $basePrice = (float) ($variant->source_price ?? $variant->price);
        
        // Customer info'yu al
        $customerInfo = app()->bound('api_customer_info') ? app('api_customer_info') : [
            'type' => 'guest', 'user' => null, 'is_authenticated' => false, 'is_dealer' => false
        ];
        $smartPricingEnabled = app()->bound('api_smart_pricing_enabled') ? app('api_smart_pricing_enabled') : true;
        
        $user = $customerInfo['user'];
        
        // Quantity parametresini al (default: 1)
        $quantity = (int) request()->get('quantity', 1);
        
        // Base currency conversion
        $sourceCurrency = $variant->source_currency ?? ($variant->currency_code ?? 'TRY');
        $basePriceConverted = app(\App\Services\CurrencyConversionService::class)->convertPrice(
            $basePrice, 
            $sourceCurrency, 
            'TRY'
        );
        
        if (!$smartPricingEnabled) {
            // Legacy mode - just return base price
            return [
                'base_price' => $basePriceConverted,
                'your_price' => $basePriceConverted,
                'your_price_formatted' => $this->formatPrice($basePriceConverted, 'TRY'),
                'currency' => 'TRY',
                'price_type' => 'Liste Fiyatı',
                'discount_percentage' => 0.0,
                'discount_amount' => 0.0,
                'savings_amount' => 0.0,
                'smart_pricing_enabled' => false,
            ];
        }
        
        // 🔥 Smart Pricing Logic - PricingRule'lardan indirim al
        $discountPercentage = $this->customerTypeDetector->getDiscountPercentage($user, $quantity);
        $discountAmount = $basePriceConverted * ($discountPercentage / 100);
        $yourPrice = $basePriceConverted - $discountAmount;
        $priceType = $this->customerTypeDetector->getTypeLabel($customerInfo['type']);
        
        return [
            'base_price' => $basePriceConverted,
            'your_price' => $yourPrice,
            'your_price_formatted' => $this->formatPrice($yourPrice, 'TRY'),
            'base_price_formatted' => $this->formatPrice($basePriceConverted, 'TRY'),
            'currency' => 'TRY',
            'price_type' => $priceType,
            'customer_type' => $customerInfo['type'],
            'discount_percentage' => $discountPercentage,
            'discount_amount' => $discountAmount,
            'savings_amount' => $discountAmount,
            'smart_pricing_enabled' => true,
            'is_dealer_price' => $customerInfo['is_dealer'],
            'pricing_tier' => $user?->pricingTier?->name,
            'quantity' => $quantity,
        ];
    }

    /**
     * 🎨 Frontend için varyant türlerini optimize edilmiş formatta döndür
     */
    private function getVariantTypesForFrontend($variant): array
    {
        if (!$variant->relationLoaded('variantOptions')) {
            return [];
        }

        $variantTypes = [];
        
        foreach ($variant->variantOptions as $option) {
            if (!$option->relationLoaded('variantType')) {
                continue;
            }
            
            $type = $option->variantType;
            
            if (!isset($variantTypes[$type->slug])) {
                $variantTypes[$type->slug] = [
                    'id' => $type->id,
                    'name' => $type->name,
                    'display_name' => $type->display_name,
                    'slug' => $type->slug,
                    'input_type' => $type->input_type,
                    'is_required' => (bool) $type->is_required,
                    'options' => []
                ];
            }
            
            $variantTypes[$type->slug]['options'][] = [
                'id' => $option->id,
                'name' => $option->name,
                'value' => $option->value,
                'display_value' => $option->display_value,
                'slug' => $option->slug,
                'hex_color' => $option->hex_color,
                'image_url' => $option->image_url,
                'sort_order' => $option->sort_order,
                'is_selected' => true, // Bu varyant için seçili
            ];
        }
        
        // Sort by sort_order
        foreach ($variantTypes as &$type) {
            usort($type['options'], function ($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
        }
        
        return array_values($variantTypes);
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