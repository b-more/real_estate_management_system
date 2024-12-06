<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationPerformanceResource\Pages;
use App\Models\Plot;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LocationPerformanceResource extends Resource
{
    protected static ?string $model = Plot::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationGroup = 'Performance Analytics';
    protected static ?string $navigationLabel = 'Location Analytics';
    protected static ?string $modelLabel = 'Location Performance';
    protected static ?string $slug = 'location-analytics';
    protected static ?int $navigationSort = 3;
    

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select([
                'location',
                DB::raw('MIN(plots.id) as id'), // Using MIN to get a consistent ID for each group
                DB::raw('COUNT(*) as total_plots'),
                DB::raw('COUNT(CASE WHEN plots.status = "sold" THEN 1 END) as plots_sold'),
                DB::raw('COUNT(CASE WHEN plots.status = "available" THEN 1 END) as plots_available'),
                DB::raw('AVG(plots.size) as average_size'),
                DB::raw('AVG(plots.price) as average_price'),
                DB::raw('SUM(CASE WHEN sales.id IS NOT NULL THEN sales.sale_price ELSE 0 END) as total_revenue'),
                DB::raw('AVG(CASE WHEN sales.id IS NOT NULL THEN sales.sale_price ELSE NULL END) as average_sale_price')
            ])
            ->leftJoin('sales', 'plots.id', '=', 'sales.plot_id')
            ->groupBy('location'); // Only group by location
    }

    public static function getRecordTitle(?Model $record): ?string
    {
        return $record?->location;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_plots')
                    ->label('Total Plots')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total'),
                    ]),

                Tables\Columns\TextColumn::make('plots_sold')
                    ->label('Plots Sold')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Sold'),
                    ]),

                Tables\Columns\TextColumn::make('plots_available')
                    ->label('Available Plots')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Available'),
                    ]),

                Tables\Columns\TextColumn::make('average_size')
                    ->label('Avg. Size (m²)')
                    ->numeric(2)
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Average::make()
                            ->label('Overall Avg'),
                    ]),

                Tables\Columns\TextColumn::make('average_price')
                    ->label('Avg. List Price')
                    ->money('ZMW')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Average::make()
                            ->label('Overall Avg')
                            ->money('ZMW'),
                    ]),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Revenue')
                    ->money('ZMW')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Grand Total')
                            ->money('ZMW'),
                    ]),

                Tables\Columns\TextColumn::make('average_sale_price')
                    ->label('Avg. Sale Price')
                    ->money('ZMW')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Average::make()
                            ->label('Overall Avg')
                            ->money('ZMW'),
                    ]),
            ])
            ->defaultSort('total_revenue', 'desc')
            ->filters([
                Filter::make('size_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('min_size')
                                    ->label('Minimum Size (m²)')
                                    ->numeric()
                                    ->placeholder('Min size'),
                                Forms\Components\TextInput::make('max_size')
                                    ->label('Maximum Size (m²)')
                                    ->numeric()
                                    ->placeholder('Max size'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_size'],
                                fn (Builder $query, $size): Builder => $query->where('plots.size', '>=', $size),
                            )
                            ->when(
                                $data['max_size'],
                                fn (Builder $query, $size): Builder => $query->where('plots.size', '<=', $size),
                            );
                    }),

                SelectFilter::make('amenities')
                    ->multiple()
                    ->label('Amenities Available')
                    ->options([
                        'water' => 'Water Connection',
                        'electricity' => 'Electricity',
                        'road_access' => 'Road Access',
                        'borehole' => 'Borehole',
                        'corner' => 'Corner Plot',
                        'commercial' => 'Commercial Zone',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data, function (Builder $query, $amenities) {
                            foreach ($amenities as $amenity) {
                                $query->whereJsonContains('plots.amenities', $amenity);
                            }
                            return $query;
                        });
                    }),

                SelectFilter::make('legal_status')
                    ->label('Legal Status')
                    ->multiple()
                    ->options([
                        'titled' => 'Titled Land',
                        'traditional' => 'Traditional Land',
                    ]),

                SelectFilter::make('period')
                    ->label('Time Period')
                    ->options([
                        'today' => 'Today',
                        'week' => 'This Week',
                        'month' => 'This Month',
                        'quarter' => 'This Quarter',
                        'year' => 'This Year',
                        'all' => 'All Time',
                    ])
                    ->default('month')
                    ->query(function (Builder $query, $state) {
                        if (!$state || $state === 'all') {
                            return $query;
                        }
                        
                        return match ($state) {
                            'today' => $query->whereDate('sales.created_at', Carbon::today()),
                            'week' => $query->whereBetween('sales.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
                            'month' => $query->whereMonth('sales.created_at', Carbon::now()->month),
                            'quarter' => $query->whereBetween('sales.created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()]),
                            'year' => $query->whereYear('sales.created_at', Carbon::now()->year),
                            default => $query,
                        };
                    }),
            ])
            ->actions([])
            ->bulkActions([])
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLocationPerformances::route('/'),
        ];
    }
}