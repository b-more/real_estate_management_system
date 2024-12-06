<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use App\Models\Sale;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RevenueStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    
    protected function getStats(): array
    {
        $currentDate = Carbon::now();
        
        // Collected Revenue (Completed Payments)
        $collectedRevenue = Payment::where('status', 'paid')
            ->sum('amount');
            
        // Pending Payments (Due within next 30 days)
        $pendingPayments = Payment::where('status', 'pending')
            ->where('due_date', '>', $currentDate)
            ->where('due_date', '<=', $currentDate->copy()->addDays(30))
            ->sum('amount');
            
        // Overdue Payments
        $overduePayments = Payment::where('status', 'pending')
            ->where('due_date', '<', $currentDate)
            ->sum('amount');

        return [
            Stat::make('Collected Revenue', 'ZMW ' . number_format($collectedRevenue, 2))
                ->description('Total collected payments')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'ring-2 ring-success/30 bg-success/10',
                ]),
                
            Stat::make('Pending Payments', 'ZMW ' . number_format($pendingPayments, 2))
                ->description('Due in next 30 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary')
                ->chart([3, 5, 3, 4, 3, 5, 4, 3])
                ->extraAttributes([
                    'class' => 'ring-2 ring-primary/30 bg-primary/10',
                ]),
                
            Stat::make('Overdue Payments', 'ZMW ' . number_format($overduePayments, 2))
                ->description('Past due date')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart([3, 2, 6, 5, 4, 3, 5, 4])
                ->extraAttributes([
                    'class' => 'ring-2 ring-danger/30 bg-danger/10',
                ]),
        ];
    }

    protected static function getCardBackground(): string
    {
        // Add a subtle gradient background to the entire widget
        return 'bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900';
    }
}