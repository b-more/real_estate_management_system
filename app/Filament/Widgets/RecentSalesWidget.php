<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentSalesWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Sales';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 12;

    protected function getTableQuery(): Builder
    {
        return Sale::query()
            ->with(['plot', 'customer', 'agent'])
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('plot.plot_number')
                ->label('Plot')
                ->searchable(),
            TextColumn::make('customer.name')
                ->label('Customer')
                ->searchable(),
            TextColumn::make('sale_price')
                ->money('ZMW')
                ->sortable(),
            TextColumn::make('sale_date')
                ->date()
                ->sortable(),
            BadgeColumn::make('status')
                ->colors([
                    'success' => 'completed',
                    'warning' => 'pending',
                    'danger' => 'cancelled',
                ]),
        ];
    }
}