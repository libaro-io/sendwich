<?php

namespace App\Http\Requests\Order;

use App\Actions\DeliverySchedule;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class RemoveRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->getOrder();

        return $order !== null && $order->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
            ],
        ];
    }

    public function getOrder(): ?Order
    {
        return once(function () {
            $product = Product::query()->find($this->input('product_id'));

            if ($product === null) {
                return null;
            }

            $schedule = new DeliverySchedule();
            $deliveryDate = $schedule->deliveryDate();

            return Order::query()
                ->where('user_id', '=', auth()->id())
                ->where('product_id', '=', $product->id)
                ->where('date', '>=', $deliveryDate->copy()->startOfDay())
                ->where('date', '<=', $deliveryDate->copy()->endOfDay())
                ->first();
        });
    }
}