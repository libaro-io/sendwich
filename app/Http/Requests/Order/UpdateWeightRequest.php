<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWeightRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = once(fn () => Order::query()
            ->where('id', '=', $this->input('order_id'))
            ->where('company_id', '=', auth()->user()->company->id)
            ->first()
        );

        return $order !== null;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')],
            'weight'   => ['required', 'numeric', 'min:0'],
        ];
    }

    public function getOrder(): Order
    {
        return once(fn () => Order::query()
            ->where('id', '=', $this->input('order_id'))
            ->where('company_id', '=', auth()->user()->company->id)
            ->firstOrFail()
        );
    }
}