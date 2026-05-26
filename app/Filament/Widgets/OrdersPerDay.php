<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class OrdersPerDay extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Orders per day';

    protected function getData(): array
    {
        $startDate = isset($this->pageFilters['startDate']) ? Carbon::parse($this->pageFilters['startDate']) : now()->startOfMonth();
        $endDate = isset($this->pageFilters['endDate']) ? Carbon::parse($this->pageFilters['endDate']) : now()->endOfMonth();

        $data = Trend::model(Order::class)
            ->dateAlias('period')
            ->between(
                start: $startDate,
                end: $endDate,
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
