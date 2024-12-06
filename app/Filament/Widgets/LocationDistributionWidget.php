<?php

namespace App\Filament\Widgets;

use App\Models\Plot;
use Filament\Widgets\ChartWidget;

class LocationDistributionWidget extends ChartWidget
{
    protected static ?string $heading = 'Plots by Location';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 2;
    protected int $contentHeight = 20;

    protected function getData(): array
    {
        $data = Plot::selectRaw('location, COUNT(*) as count')
            ->groupBy('location')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => [
                        '#198754', '#FF8C00', '#CE1126', '#3CB371', 
                        '#D67A00', '#145f3b', '#ff7000', '#9a0e1e'
                    ],
                ],
            ],
            'labels' => $data->pluck('location')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}