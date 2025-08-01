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
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'integer', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.price' => ['required', 'numeric', 'min:0'],
            'products.*.sell_price' => ['required', 'numeric', 'min:0'],
            'products.*.imeis' => ['required', 'array'], // <-- New imeis array per product
            'products.*.imeis.*' => ['required', 'string'], // Each IMEI must be a string
            'purchase_date' => ['required', 'date_format:d-m-Y H:i:s'],
            'discount_type' => ['nullable', 'in:PERCENTAGE,FIXED'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string'],
            'payment_status' => ['required', 'string', 'in:PENDING,PAID'],
            'notes' => ['nullable', 'string'],
        ];

    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $products = $this->input('products', []);

            foreach ($products as $index => $product) {
                $quantity = $product['quantity'] ?? 0;
                $imeis = $product['imeis'] ?? [];

                if (count($imeis) !== $quantity) {
                    $validator->errors()->add("products.$index.imeis", "The number of IMEIs must match the quantity for product at index $index.");
                }
            }
        });
    }
}
