<?php

namespace App\Http\Requests\Order;

use App\Actions\DeliverySchedule;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $orderIds = collect($this->input('prices', []))
            ->pluck('order_id')
            ->filter()
            ->unique()
            ->values();

        if ($orderIds->isEmpty()) {
            return false;
        }

        $deliveryDate = $this->deliveryDate();

        $owned = once(fn () => Order::query()
            ->whereIn('id', $orderIds->all())
            ->where('company_id', '=', auth()->user()->company->id)
            ->where('paid_by', '=', auth()->id())
            ->whereNull('delivered_at')
            ->whereBetween('date', [
                $deliveryDate->copy()->startOfDay(),
                $deliveryDate->copy()->endOfDay(),
            ])
            ->count());

        return $owned === $orderIds->count();
    }

    public function rules(): array
    {
        $companyId = auth()->user()->company->id;

        return [
            'prices'            => ['required', 'array'],
            'prices.*.order_id' => ['required', 'integer', Rule::exists('orders', 'id')],
            'prices.*.total'    => ['required', 'numeric', 'min:0'],

            'extra_items'            => ['present', 'array'],
            'extra_items.*.label'    => ['required', 'string', 'max:255'],
            'extra_items.*.total'    => ['required', 'numeric', 'min:0'],
            'extra_items.*.user_id'  => ['required', 'integer', Rule::exists('users', 'id')->where('company_id', $companyId)],
            'extra_items.*.store_id' => ['nullable', 'integer', Rule::exists('stores', 'id')->where('company_id', $companyId)],

            'new_products'            => ['sometimes', 'array'],
            'new_products.*.name'     => ['required', 'string', 'max:255'],
            'new_products.*.price'    => ['required', 'numeric', 'min:0'],
            'new_products.*.store_id' => ['required', 'integer', Rule::exists('stores', 'id')->where('company_id', $companyId)],

            'price_updates'              => ['sometimes', 'array'],
            'price_updates.*.product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('company_id', $companyId)],
            'price_updates.*.price'      => ['required', 'numeric', 'min:0'],
        ];
    }

    public function deliveryDate(): Carbon
    {
        return once(fn () => new DeliverySchedule()->deliveryDate());
    }

    /**
     * @return array<int, array{order_id: int, total: float}>
     */
    public function getPrices(): array
    {
        return $this->validated('prices');
    }

    /**
     * @return array<int, array{label: string, total: float, user_id: int, store_id: int|null}>
     */
    public function getExtraItems(): array
    {
        return $this->validated('extra_items') ?? [];
    }

    /**
     * @return array<int, array{name: string, price: float, store_id: int}>
     */
    public function getNewProducts(): array
    {
        return $this->validated('new_products') ?? [];
    }

    /**
     * @return array<int, array{product_id: int, price: float}>
     */
    public function getPriceUpdates(): array
    {
        return $this->validated('price_updates') ?? [];
    }
}
