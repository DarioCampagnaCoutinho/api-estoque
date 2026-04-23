<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type'       => ['required', 'string', 'in:in,out,adjustment'],
            'quantity'   => ['required', 'numeric', 'min:0.001'],
            'unit_cost'  => ['nullable', 'numeric', 'min:0'],
            'reason'     => ['nullable', 'string', 'max:255'],
            'reference'  => ['nullable', 'string', 'max:255'],
            'notes'      => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.in' => 'O tipo deve ser: in (entrada), out (saída) ou adjustment (ajuste).',
        ];
    }
}
