<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="OrderItem",
 *     title="Order Item",
 *     description="Order item data with tax information",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="quantity", type="integer", example=2),
 *     @OA\Property(property="price", type="number", format="float", example=50.00, description="Birim fiyat (KDV hariç)"),
 *     @OA\Property(property="discounted_price", type="number", format="float", example=45.00, description="İndirimli birim fiyat"),
 *     @OA\Property(property="total_price", type="number", format="float", example=90.00, description="Toplam fiyat (KDV hariç)"),
 *     @OA\Property(property="unit_price_excl_tax", type="number", format="float", example=50.00, description="Birim fiyat KDV hariç"),
 *     @OA\Property(property="unit_price_incl_tax", type="number", format="float", example=59.00, description="Birim fiyat KDV dahil"),
 *     @OA\Property(property="tax_rate", type="number", format="float", example=18.0, description="KDV oranı (%)"),
 *     @OA\Property(property="tax_amount", type="number", format="float", example=18.00, description="Toplam KDV tutarı"),
 *     @OA\Property(property="total_price_incl_tax", type="number", format="float", example=108.00, description="Toplam fiyat KDV dahil"),
 *     @OA\Property(property="product", type="object", description="Ürün bilgileri"),
 *     @OA\Property(property="product_variant", type="object", description="Ürün varyant bilgileri")
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
            'discounted_price' => (float) $this->price,
            'total_price' => (float) $this->total,
            'unit_price_excl_tax' => (float) $this->price,
            'unit_price_incl_tax' => (float) (($this->quantity > 0) ? ($this->price + ($this->tax_amount / $this->quantity)) : $this->price),
            'tax_rate' => (float) ($this->tax_rate ?? 0),
            'tax_amount' => (float) $this->tax_amount,
            'total_price_incl_tax' => (float) ($this->total + $this->tax_amount),
            
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