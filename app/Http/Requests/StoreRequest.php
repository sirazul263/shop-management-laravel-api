<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        $id = $this->route('storeId');

        return [
            'name' => ['required', 'unique:stores,name,'.$id, 'string', 'min:3', 'max:255'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'phone' => ['required', 'min:11', 'max:15'],
            'description' => ['nullable', 'string', 'min:10', 'max:255'],
            'image' => ['sometimes'],
        ];
    }
}
