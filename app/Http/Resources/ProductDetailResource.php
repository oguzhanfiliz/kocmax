<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\MultiCurrencyPricingService;

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

    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->pricingService = app(MultiCurrencyPricingService::class);
    }

    public function toArray($request): array
    {
        $currency = app()->bound('api_currency') ? app('api_currency') : 'TRY';

        // Base product data
        $baseData = (new ProductResource($this->resource))->toArray($request);

        // Add detailed information
        return array_merge($baseData, [
            'variants' => $this->whenLoaded('variants', fn() => 
                $this->variants->map(fn($variant) => [
                    'id' => $variant->id,
                    'color' => $variant->color,
                    'size' => $variant->size,
                    'stock' => $variant->stock,
                    'price' => [
                        'original' => (float) $variant->price,
                        'converted' => $this->pricingService->convertPrice(
                            (float) $variant->price, 
                            $variant->currency_code ?? 'TRY', 
                            $currency
                        ),
                        'currency' => $currency,
                        'formatted' => $this->formatPrice(
                            $this->pricingService->convertPrice(
                                (float) $variant->price, 
                                $variant->currency_code ?? 'TRY', 
                                $currency
                            ), 
                            $currency
                        ),
                    ],
                    'images' => $variant->whenLoaded('images', fn() => 
                        $variant->images->map(fn($image) => [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'alt_text' => $image->alt_text,
                        ])
                    ),
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
            'specifications' => $this->specifications ?? (object) [],
            'meta' => $this->whenLoaded('variants', function () use ($currency) {
                $variants = $this->variants;
                $totalStock = $variants->sum('stock');
                
                if ($variants->count() > 0) {
                    $prices = $variants->map(fn($variant) => 
                        $this->pricingService->convertPrice(
                            (float) $variant->price, 
                            $variant->currency_code ?? 'TRY', 
                            $currency
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