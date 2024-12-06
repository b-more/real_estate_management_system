<?php

namespace App\Filament\Widgets;

use App\Models\Plot;
use App\Models\Sale;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 12;
    
    protected function getStats(): array
    {
        return [
            Stat::make('Available Plots', Plot::where('status', 'available')->count())
                ->description('Active Listings')
                ->descriptionIcon('heroicon-m-home')
                ->chart([7, 4, 6, 8, 5, 3, 8])
                ->color('success'),

            Stat::make('Total Sales', 'K' . number_format(Sale::sum('sale_price'), 2))
                ->description(Sale::whereMonth('created_at', Carbon::now()->month)->count() . ' sales this month')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->chart([4, 8, 6, 7, 9, 6, 8])
                ->color('warning'),

            Stat::make('Active Customers', Customer::count())
                ->description(Customer::whereMonth('created_at', Carbon::now()->month)->count() . ' new this month')
                ->descriptionIcon('heroicon-m-users')
                ->chart([3, 5, 7, 6, 8, 5, 7])
                ->color('success'),
        ];
    }
}