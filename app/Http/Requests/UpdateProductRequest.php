<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name'        => ['sometimes', 'string', 'max:255'],
            'sku'         => ['sometimes', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'description' => ['nullable', 'string'],
            'price'       => ['sometimes', 'numeric', 'min:0'],
            'cost'        => ['nullable', 'numeric', 'min:0'],
            'unit'        => ['nullable', 'string', 'max:10'],
            'active'      => ['nullable', 'boolean'],
        ];
    }
}
