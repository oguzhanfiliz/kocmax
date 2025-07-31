<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
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
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}