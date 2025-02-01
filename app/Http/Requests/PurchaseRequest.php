<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'], // Ensure supplier_id is an integer and exists
            'products' => ['required', 'array'], // Validate that products is an array
            'products.*.id' => ['required', 'integer', 'exists:products,id'], // Each product must have a valid ID
            'products.*.quantity' => ['required', 'integer', 'min:1'], // Quantity must be at least 1
            'products.*.price' => ['required', 'numeric', 'min:0'], // Price must be non-negative
            'products.*.sell_price' => ['required', 'numeric', 'min:0'], // Sell price must be non-negative
            'purchase_date' => ['required', 'date_format:d-m-Y H:i:s'], // Validate date format
            'discount_type' => ['nullable', 'in:PERCENTAGE,FIXED'], // Ensure discount_type is valid or null
            'discount_amount' => ['nullable', 'numeric', 'min:0'], // Discount amount must be non-negative
            'payment_method' => ['required', 'string'], // Payment method must be a string
            'payment_status' => ['required', 'string', 'in:PENDING,PAID'], // Validate payment status
            'notes' => ['nullable', 'string'], // Notes can be null or a string
        ];

    }
}
