<?php

namespace App\Http\Requests\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;

class DeleteOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');

        return $order instanceof Order && $order->company_id === auth()->user()->company->id;
    }

    public function rules(): array
    {
        return [];
    }
}
