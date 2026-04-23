<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'sku'          => ['required', 'string', 'max:100', 'unique:products,sku'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'numeric', 'min:0'],
            'cost'         => ['nullable', 'numeric', 'min:0'],
            'unit'         => ['nullable', 'string', 'max:10'],
            'active'       => ['nullable', 'boolean'],
            'min_quantity' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
