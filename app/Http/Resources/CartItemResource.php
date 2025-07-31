<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_id' => $this->cart_id,
            'product_id' => $this->product_id,
            'product_variant_id' => $this->product_variant_id,
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'discounted_price' => (float) ($this->discounted_price ?? $this->price),
            'base_price' => (float) ($this->base_price ?? $this->price),
            'calculated_price' => (float) ($this->calculated_price ?? $this->price),
            'unit_discount' => (float) ($this->unit_discount ?? 0),
            'total_discount' => (float) ($this->total_discount ?? 0),
            'subtotal' => (float) (($this->calculated_price ?? $this->price) * $this->quantity),
            'applied_discounts' => $this->applied_discounts ?? [],
            'price_calculated_at' => $this->price_calculated_at?->toISOString(),
            'product' => new ProductResource($this->whenLoaded('product')),
            'product_variant' => new ProductVariantResource($this->whenLoaded('productVariant')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}