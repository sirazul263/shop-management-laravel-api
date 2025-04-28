<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $id = $this->route('productId');
        $storeId = $this->route('storeId') ?? $this->store_id;

        return [
            'category_id' => ['required'],
            'brand_id' => ['required'],
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('products', 'name')
                    ->where('store_id', $storeId)
                    ->ignore($id),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'min:10', 'max:255'],
            'image' => ['nullable'],
        ];
    }
}
