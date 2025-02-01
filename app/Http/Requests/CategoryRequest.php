<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $id = $this->route('categoryId');

        return [
            'store_id' => ['required', 'exists:stores,id'],
            'name' => ['required', 'unique:categories,name,'.$id, 'string', 'min:3', 'max:255'],
            'is_active' => ['required', 'boolean'],
            'image' => ['sometimes'],
        ];
    }
}
