<?php

declare(strict_types=1);

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddItemRequest extends FormRequest
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
            'product_variant_id' => [
                'required',
                'integer',
                Rule::exists('product_variants', 'id')->where(function ($query) {
                    $query->whereHas('product', function ($q) {
                        $q->where('is_active', true);
                    });
                }),
            ],
            'quantity' => [
                'sometimes',
                'integer',
                'min:1',
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
            'product_variant_id.required' => 'Product variant is required',
            'product_variant_id.exists' => 'Selected product variant is not available',
            'quantity.min' => 'Quantity must be at least 1',
            'quantity.max' => 'Maximum quantity per item is 999',
            'quantity.integer' => 'Quantity must be a valid number'
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_variant_id' => 'product variant',
            'quantity' => 'quantity'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set default quantity if not provided
        if (!$this->has('quantity')) {
            $this->merge(['quantity' => 1]);
        }
    }
}