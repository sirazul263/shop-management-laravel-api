<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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

        return [
            'name' => ['required', 'unique:brands,name,'.$id, 'string', 'min:3', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'image' => ['sometimes'],
        ];

    }
}
