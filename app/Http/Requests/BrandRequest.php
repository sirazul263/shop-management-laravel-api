<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
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
        $id = $this->route('brandId');
        $storeId = $this->route('storeId') ?? $this->store_id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('brands', 'name')
                    ->where('store_id', $storeId)
                    ->ignore($id), // ignore current brand if updating
            ],
            'is_active' => ['required', 'boolean'],
            'image' => ['sometimes'],
        ];

    }
}
