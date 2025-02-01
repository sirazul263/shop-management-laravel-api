<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
        $id = $this->route('supplierId');

        return [
            'name' => ['required', 'unique:suppliers,name,'.$id, 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'unique:suppliers,email,'.$id, 'string', 'min:3'],
            'phone' => ['required', 'min:11', 'max:15'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }
}
