<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="WishlistResource",
 *     title="Wishlist Resource",
 *     description="Wishlist item resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="product", ref="#/components/schemas/ProductResource"),
 *     @OA\Property(property="product_variant", ref="#/components/schemas/ProductVariantResource", nullable=true),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Need this for the office"),
 *     @OA\Property(property="priority", type="integer", example=2, description="1=Low, 2=Medium, 3=High, 4=Urgent"),
 *     @OA\Property(property="priority_label", type="string", example="Medium"),
 *     @OA\Property(property="is_favorite", type="boolean", example=true),
 *     @OA\Property(property="is_available", type="boolean", example=true, description="Whether item is in stock"),
 *     @OA\Property(property="current_price", type="number", format="float", example=125.50),
 *     @OA\Property(property="added_at", type="string", format="datetime"),
 *     @OA\Property(property="notification_sent_at", type="string", format="datetime", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */
class WishlistResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'product_variant' => $this->when(
                $this->product_variant_id && $this->relationLoaded('productVariant'),
                new ProductVariantResource($this->productVariant)
            ),
            'notes' => $this->notes,
            'priority' => $this->priority,
            'priority_label' => $this->priority_label,
            'is_favorite' => $this->is_favorite,
            'is_available' => $this->isAvailable(),
            'current_price' => $this->getCurrentPrice(),
            'added_at' => $this->added_at?->toISOString(),
            'notification_sent_at' => $this->notification_sent_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

/**
 * @OA\Schema(
 *     schema="ProductVariantResource",
 *     title="Product Variant Resource (Simplified)",
 *     description="Simplified product variant resource for wishlist",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="color", type="string", nullable=true, example="Red"),
 *     @OA\Property(property="size", type="string", nullable=true, example="L"),
 *     @OA\Property(property="price", type="number", format="float", example=125.50),
 *     @OA\Property(property="stock", type="integer", example=15),
 *     @OA\Property(property="sku", type="string", example="ISG-BOOT-PRO-001-L-RED")
 * )
 */
class ProductVariantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'color' => $this->color,
            'size' => $this->size,
            'price' => (float) $this->price,
            'stock' => $this->stock,
            'sku' => $this->sku,
        ];
    }
}