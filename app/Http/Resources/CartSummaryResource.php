<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'subtotal' => $this->resource->getSubtotal(),
            'discount' => $this->resource->getDiscount(),
            'total' => $this->resource->getTotal(),
            'item_count' => $this->resource->getItemCount(),
            'is_empty' => $this->resource->isEmpty(),
            'has_discount' => $this->resource->hasDiscount(),
            'discount_percentage' => $this->resource->getDiscountPercentage(),
            'savings' => $this->resource->getSavings(),
            'applied_discounts' => $this->resource->getAppliedDiscounts(),
            'discounts_by_type' => $this->resource->getDiscountsByType(),
            'item_details' => $this->resource->getItemDetails(),
        ];
    }
}