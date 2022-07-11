<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                Rule::exists('products', 'id'),
            ],
            'options' => [
                'present',
                'array',
            ],
            'options.*' => [
                'integer',
                Rule::exists('product_options', 'id'),
            ],
        ];
    }
}
