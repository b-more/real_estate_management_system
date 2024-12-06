<?php

namespace App\Filament\Widgets;

use App\Models\Plot;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlotStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected function getStats(): array
    {
        // Available Plots
        $availablePlots = Plot::where('status', 'available')->count();
        $totalPlots = Plot::count();
        $availablePercentage = $totalPlots > 0 ? ($availablePlots / $totalPlots) * 100 : 0;
        
        // Reserved Plots
        $reservedPlots = Plot::where('status', 'reserved')->count();
        $reservedPercentage = $totalPlots > 0 ? ($reservedPlots / $totalPlots) * 100 : 0;
        
        // Sold Plots
        $soldPlots = Plot::where('status', 'sold')->count();
        $soldPercentage = $totalPlots > 0 ? ($soldPlots / $totalPlots) * 100 : 0;

        return [
            Stat::make('Available Plots', $availablePlots)
                ->description(number_format($availablePercentage, 1) . '% of total plots')
                ->descriptionIcon('heroicon-m-home')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'ring-2 ring-success/30 bg-success/10',
                ]),
                
            Stat::make('Reserved Plots', $reservedPlots)
                ->description(number_format($reservedPercentage, 1) . '% of total plots')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([3, 5, 3, 4, 3, 5, 4, 3])
                ->extraAttributes([
                    'class' => 'ring-2 ring-warning/30 bg-warning/10',
                ]),
                
            Stat::make('Sold Plots', $soldPlots)
                ->description(number_format($soldPercentage, 1) . '% of total plots')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('primary')
                ->chart([3, 2, 6, 5, 4, 3, 5, 4])
                ->extraAttributes([
                    'class' => 'ring-2 ring-primary/30 bg-primary/10',
                ]),
        ];
    }

    protected static function getCardBackground(): string
    {
        // Add a subtle gradient background to the entire widget
       // return 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900';
       return 'bg-primary/5';
    }
}