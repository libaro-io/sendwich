<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->resolveOrder();

        // Only the runner (deliverer) or the buyer (order owner) may set the actual price,
        // and only for variable-price products (fixed prices stay firm).
        if ($order === null || $order->product === null || !$order->product->variable_price) {
            return false;
        }

        return $order->user_id === auth()->id() || $order->paid_by === auth()->id();
    }

    public function rules(): array
    {
        return [
            'order_id' => ['required', 'integer', Rule::exists('orders', 'id')],
            'total'    => ['required', 'numeric', 'min:0'],
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
            ->with('product:id,variable_price,price')
            ->first()
        );
    }
}
