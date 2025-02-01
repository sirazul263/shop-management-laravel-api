<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'max:15'],
            'address' => ['required', 'string', 'max:255'],
            'total_paid' => ['required', 'numeric'],
            'payment_method' => ['required'],
            'payment_status' => ['required'],
            'discount_type' => ['nullable'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'], // Discount amount must be non-negative or null
            'notes' => ['nullable', 'string'],
            'products.*.id' => ['required', 'integer', 'exists:products,id'], // Each product must have a valid ID
            'products.*.quantity' => ['required', 'integer', 'min:1'], // Quantity must be at least 1
            'products.*.unit_amount' => ['required', 'numeric', 'min:0'], // Price must be non-negative
        ];
    }
}
