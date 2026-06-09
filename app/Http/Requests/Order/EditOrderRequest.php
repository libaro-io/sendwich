<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EditOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Anyone in the company may edit a history item of their company.
        return $this->resolveOrder() !== null;
    }

    public function rules(): array
    {
        $companyId = auth()->user()->company->id;

        return [
            'order_id'         => ['required', 'integer', Rule::exists('orders', 'id')],
            'product_id'       => ['nullable', 'integer', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'label'            => ['required_without:product_id', 'nullable', 'string', 'max:255'],
            'store_id'         => ['nullable', 'integer', Rule::exists('stores', 'id')->where('company_id', $companyId)],
            'total'            => ['required', 'numeric', 'min:0'],
            'add_to_catalogue' => ['sometimes', 'boolean'],
            'catalogue_price'  => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function getOrder(): Order
    {
        return $this->resolveOrder() ?? abort(404);
    }

    private function resolveOrder(): ?Order
    {
        return once(fn () => Order::query()
            ->where('id', '=', $this->input('order_id'))
            ->where('company_id', '=', auth()->user()->company->id)
            ->first()
        );
    }
}
