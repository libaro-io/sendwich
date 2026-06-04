<?php

namespace App\Http\Requests\Order;

use App\Actions\DeliverySchedule;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;

class AnalyzeReceiptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->getUndeliveredOrders()->isNotEmpty();
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:10240'],
        ];
    }

    /**
     * The orders the current runner still has to deliver for the active delivery date.
     *
     * @return Collection<int, Order>
     */
    public function getUndeliveredOrders(): Collection
    {
        return once(function () {
            $deliveryDate = new DeliverySchedule()->deliveryDate();

                ->with('product:id,name,price,variable_price,store_id')
        });
    }
}