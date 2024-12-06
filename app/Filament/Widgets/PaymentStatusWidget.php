<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class PaymentStatusWidget extends ChartWidget
{
    protected static ?string $heading = 'Payment Overview';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $payments = Payment::selectRaw('status, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('status')
            ->get();

        return [
            'datasets' => [
                [
                    'data' => $payments->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#198754', // Success/Completed
                        '#FF8C00', // Pending
                        '#CE1126', // Overdue
                    ],
                ],
            ],
            'labels' => $payments->pluck('status')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}