<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     title="Cart",
 *     description="Shopping cart data",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="session_id", type="string", nullable=true),
 *     @OA\Property(property="user_id", type="integer", nullable=true),
 *     @OA\Property(property="customer_type", type="string", example="B2C"),
 *     @OA\Property(property="total_amount", type="number", format="float", example=150.00),
 *     @OA\Property(property="discounted_amount", type="number", format="float", example=135.00),
 *     @OA\Property(property="subtotal_amount", type="number", format="float", example=120.00),
 *     @OA\Property(property="coupon_code", type="string", nullable=true),
 *     @OA\Property(property="coupon_discount", type="number", format="float", example=15.00),
 *     @OA\Property(property="applied_discounts", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="item_count", type="integer", example=3),
 *     @OA\Property(property="unique_items", type="integer", example=2),
 *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/CartItem")),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class CartResource extends JsonResource
{
    private string $targetCurrency;

    public function __construct($resource, string $targetCurrency = 'TRY')
    {
        parent::__construct($resource);
        $this->targetCurrency = $targetCurrency;
    }

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'session_id' => $this->session_id,
            'user_id' => $this->user_id,
            'customer_type' => $this->customer_type,
            'total_amount' => (float) $this->total_amount,
            'discounted_amount' => (float) $this->discounted_amount,
            'subtotal_amount' => (float) $this->subtotal_amount,
            'coupon_code' => $this->coupon_code,
            'coupon_discount' => (float) ($this->coupon_discount ?? 0),
            'applied_discounts' => $this->applied_discounts ?? [],
            'pricing_calculated_at' => $this->pricing_calculated_at?->toISOString(),
            'last_pricing_update' => $this->last_pricing_update?->toISOString(),
            'item_count' => $this->items?->sum('quantity') ?? 0,
            'unique_items' => $this->items?->count() ?? 0,
            'currency' => $this->targetCurrency,
            'items' => CartItemResource::collection($this->whenLoaded('items'), $this->targetCurrency),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}