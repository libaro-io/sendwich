<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Carbon;

class PopularItemsChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Popular products';

    protected function getData(): array
    {
        $startDate = isset($this->pageFilters['startDate']) ? Carbon::parse($this->pageFilters['startDate']) : now()->startOfMonth();
        $endDate = isset($this->pageFilters['endDate']) ? Carbon::parse($this->pageFilters['endDate']) : now()->endOfMonth();

        $popularItems = Order::query()
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->selectRaw('products.name as product_name, COUNT(*) AS total_sales')
            ->whereBetween('orders.created_at',
                [
                    $startDate,
                    $endDate
                ])
            ->groupBy('products.id')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Amount of orders',
                    'data' => $popularItems->pluck('total_sales')->toArray(),
                ],
            ],
            'labels' => $popularItems->pluck('product_name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
