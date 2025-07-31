<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via Gate
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(OrderStatus::class)],
            'notes' => 'nullable|string|max:1000',
            'tracking_number' => 'nullable|string|max:255',
            'shipping_carrier' => 'nullable|string|max:255',
            'estimated_delivery_at' => 'nullable|date|after:today',
            'notify_customer' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Order status is required',
            'notes.max' => 'Notes cannot exceed 1000 characters',
            'tracking_number.max' => 'Tracking number cannot exceed 255 characters',
            'shipping_carrier.max' => 'Shipping carrier cannot exceed 255 characters',
            'estimated_delivery_at.date' => 'Estimated delivery date must be a valid date',
            'estimated_delivery_at.after' => 'Estimated delivery date must be in the future'
        ];
    }
}