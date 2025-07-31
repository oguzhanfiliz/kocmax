<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->getLabel(),
                'color' => $this->status->getColor(),
                'icon' => $this->status->getIcon()
            ],
            'customer_type' => $this->customer_type,
            
            // Financial Information
            'subtotal_amount' => (float) $this->subtotal_amount,
            'discount_amount' => (float) $this->discount_amount,
            'tax_amount' => (float) $this->tax_amount,
            'shipping_cost' => (float) $this->shipping_cost,
            'total_amount' => (float) $this->total_amount,
            'currency' => $this->currency ?? 'TRY',
            
            // Payment Information
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'payment_reference' => $this->payment_reference,
            'paid_at' => $this->paid_at,
            
            // Shipping Information
            'shipping_method' => $this->shipping_method,
            'shipping_carrier' => $this->shipping_carrier,
            'tracking_number' => $this->tracking_number,
            'estimated_delivery_at' => $this->estimated_delivery_at,
            'shipped_at' => $this->shipped_at,
            'delivered_at' => $this->delivered_at,
            
            // Address Information
            'shipping_address' => [
                'name' => $this->shipping_name,
                'email' => $this->shipping_email,
                'phone' => $this->shipping_phone,
                'address' => $this->shipping_address,
                'city' => $this->shipping_city,
                'state' => $this->shipping_state,
                'postal_code' => $this->shipping_postal_code,
                'country' => $this->shipping_country
            ],
            'billing_address' => [
                'name' => $this->billing_name,
                'email' => $this->billing_email,
                'phone' => $this->billing_phone,
                'address' => $this->billing_address,
                'city' => $this->billing_city,
                'state' => $this->billing_state,
                'postal_code' => $this->billing_postal_code,
                'country' => $this->billing_country,
                'tax_number' => $this->billing_tax_number,
                'tax_office' => $this->billing_tax_office
            ],
            
            // Order Items
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            
            // Status History
            'status_history' => $this->when(
                $this->relationLoaded('statusHistory'),
                fn() => $this->statusHistory->map(fn($history) => [
                    'status' => $history->status,
                    'status_label' => \App\Enums\OrderStatus::from($history->status)->getLabel(),
                    'notes' => $history->notes,
                    'changed_by' => $history->user?->name,
                    'changed_at' => $history->created_at
                ])
            ),
            
            // Metadata
            'notes' => $this->notes,
            'internal_notes' => $this->when(
                $request->user()?->hasRole(['admin', 'manager']),
                $this->internal_notes
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Conditional fields for different user roles
            'user' => $this->when(
                $request->user()?->hasRole(['admin', 'manager']),
                fn() => [
                    'id' => $this->user_id,
                    'name' => $this->user?->name,
                    'email' => $this->user?->email
                ]
            ),
        ];
    }
}