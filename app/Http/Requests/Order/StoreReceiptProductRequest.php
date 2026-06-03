<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReceiptProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'store_id'       => [
                'required',
                'integer',
                Rule::exists('stores', 'id')->where('company_id', auth()->user()->company->id),
            ],
            'name'           => ['required', 'string', 'max:255'],
            'price'          => ['required', 'numeric', 'min:0'],
            'variable_price' => ['sometimes', 'boolean'],
        ];
    }
}
