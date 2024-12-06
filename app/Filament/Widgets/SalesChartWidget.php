<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Sales Performance';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 6;
    protected int $contentHeight = 200;

    protected function getData(): array
    {
        $data = Sale::selectRaw('MONTH(created_at) as month, COUNT(*) as count, SUM(sale_price) as revenue')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Sales Revenue (K)',
                    'data' => $data->pluck('revenue')->toArray(),
                    'backgroundColor' => '#FF8C00',
                    'borderColor' => '#FF8C00',
                ],
                [
                    'label' => 'Number of Sales',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => '#198754',
                    'borderColor' => '#198754',
                ],
            ],
            'labels' => $data->pluck('month')->map(function ($month) {
                return Carbon::create()->month($month)->format('M');
            })->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
