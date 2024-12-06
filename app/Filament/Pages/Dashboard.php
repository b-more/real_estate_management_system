<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static bool $isLazy = false;

    public function getWidgets(): array
    {
        return [
          //  \App\Filament\Widgets\DashboardOverviewWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 1;
    }
}