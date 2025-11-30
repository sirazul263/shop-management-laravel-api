<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $storeId = $this->route('storeId') ?? $this->store_id;

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('suppliers', 'name')
                    ->where('store_id', $storeId)
                    ->ignore($id),
            ],
           'email' => [
            'required',
            'email',
            'string',
            'min:3',
            'max:255',
            Rule::unique('suppliers', 'email')
                ->where('store_id', $storeId)
                ->ignore($id),
        ],
            'phone' => ['required', 'min:11', 'max:15'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }
}
