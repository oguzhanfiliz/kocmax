<?php

declare(strict_types=1);

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuantityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'integer',
                'min:0',
                'max:999'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Quantity is required',
            'quantity.integer' => 'Quantity must be a valid number',
            'quantity.min' => 'Quantity cannot be negative',
            'quantity.max' => 'Maximum quantity per item is 999'
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'quantity' => 'quantity'
        ];
    }
}