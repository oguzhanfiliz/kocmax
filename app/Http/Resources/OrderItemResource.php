<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderItem",
 *     title="Order Item",
 *     description="Order item data",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="float", example=50.00),
 *     @OA\Property(property="discounted_price", type="number", format="float", example=45.00),
 *     @OA\Property(property="total_price", type="number", format="float", example=90.00),
 *     @OA\Property(property="product", type="object"),
 *     @OA\Property(property="product_variant", type="object")
 * )
 */
class OrderItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price' => (float) $this->price,
            'discounted_price' => (float) $this->discounted_price,
            'total_price' => (float) ($this->discounted_price * $this->quantity),
            
            // Product Information
            'product' => [
                'id' => $this->product_id,
                'name' => $this->product?->name,
                'sku' => $this->product?->sku,
                'slug' => $this->product?->slug,
                'image' => $this->product?->images?->first()?->url
            ],
            
            // Product Variant Information (if applicable)
            'product_variant' => $this->when(
                $this->product_variant_id,
                fn() => [
                    'id' => $this->product_variant_id,
                    'sku' => $this->productVariant?->sku,
                    'color' => $this->productVariant?->color,
                    'size' => $this->productVariant?->size,
                    'attributes' => $this->productVariant?->attributes ?? []
                ]
            ),
            
            // Snapshot data (captured at order time)
            'product_name' => $this->product_name,
            'product_sku' => $this->product_sku,
            'variant_attributes' => $this->variant_attributes,
            
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}