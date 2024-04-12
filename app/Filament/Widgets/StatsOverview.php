<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Users', User::query()->count()),
            Stat::make('Orders', Order::query()->count()),
            Stat::make('Products', Product::query()->count()),
            Stat::make('Total turnover', Number::currency(Order::query()->sum('total'), 'EUR')),
        ];
    }
}
