<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\NewUsersPerMonth;
use App\Filament\Widgets\OrdersPerDay;
use App\Filament\Widgets\OrdersPerMonth;
use App\Filament\Widgets\PopularItemsChart;
use App\Filament\Widgets\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Forms\Components\DatePicker;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            OrdersPerMonth::class,
            NewUsersPerMonth::class,
            OrdersPerDay::class,
            PopularItemsChart::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    DatePicker::make('startDate'),
                    DatePicker::make('endDate'),
                ]),
        ];
    }
}
