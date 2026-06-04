<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddCustomOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $companyId = auth()->user()->company->id;

        return [
            'date'     => ['required', 'date_format:Ymd'],
            'user_id'  => ['required', 'integer', Rule::exists('users', 'id')->where('company_id', $companyId)],
            'paid_by'  => ['nullable', 'integer', Rule::exists('users', 'id')->where('company_id', $companyId)],
            'store_id' => ['nullable', 'integer', Rule::exists('stores', 'id')->where('company_id', $companyId)],
            'label'            => ['required', 'string', 'max:255'],
            'total'            => ['required', 'numeric', 'min:0'],
            'add_to_catalogue' => ['sometimes', 'boolean'],
            'catalogue_price'  => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
