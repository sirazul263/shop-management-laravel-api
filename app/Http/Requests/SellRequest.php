<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

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
            'products.*.imei' => ['required', 'array'], // imeis array per product
            'products.*.imei.*' => ['required', 'string'], // Each IMEI must be a string
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $products = $this->input('products', []);

            foreach ($products as $index => $product) {
                $quantity = $product['quantity'] ?? 0;
                $imeis = $product['imei'] ?? [];
                $productId = $product['id'] ?? null;

                // 1. Quantity must match IMEI count
                if (count($imeis) !== $quantity) {
                    $validator->errors()->add("products.$index.imei", "The number of IMEIs must match the quantity for product at index $index.");
                }

                // 2. Validate IMEIs exist in DB and belong to correct product
                foreach ($imeis as $imei) {
                    $exists = DB::table('imei_numbers')
                        ->where('imei', $imei)
                        ->where('product_id', $productId)
                        ->exists();

                    if (! $exists) {
                        $validator->errors()->add(
                            "products.$index.imei",
                            "IMEI {$imei} does not exist for the given product."
                        );
                    }
                }
            }
        });
    }
}
