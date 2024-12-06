<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;
use App\Filament\Resources\AgentPerformanceResource\Pages;

class AgentPerformanceResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Performance Analytics';
    protected static ?string $navigationLabel = 'Agent Performance';
    protected static ?string $modelLabel = 'Agent Performance';
    protected static ?string $slug = 'agent-performance';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(sales.id) as plots_sold'),
                DB::raw('SUM(sales.sale_price) as total_amount'),
                DB::raw('AVG(sales.sale_price) as average_sale'),
                DB::raw('MAX(sales.sale_price) as highest_sale'),
                DB::raw('MIN(sales.created_at) as first_sale'),
                DB::raw('MAX(sales.created_at) as last_sale'),
            ])
            ->leftJoin('sales', 'users.id', '=', 'sales.agent_id')
            ->where('users.role', 'agent')
            ->groupBy('users.id', 'users.name', 'users.email');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Agent Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('plots_sold')
                    ->label('Plots Sold')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Plots')
                    ]),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total Sales')
                    ->money('ZMW')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total Revenue')
                            ->money('ZMW')
                    ]),

                Tables\Columns\TextColumn::make('average_sale')
                    ->label('Average Sale')
                    ->money('ZMW')
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Average::make()
                            ->label('Overall Average')
                            ->money('ZMW')
                    ]),

                Tables\Columns\TextColumn::make('highest_sale')
                    ->label('Highest Sale')
                    ->money('ZMW')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('first_sale')
                    ->label('First Sale')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_sale')
                    ->label('Last Sale')
                    ->date()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('plots_sold', 'desc')
            ->filters([
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

                SelectFilter::make('performance_tier')
                    ->label('Performance Tier')
                    ->options([
                        'top' => 'Top Performers (Top 10%)',
                        'high' => 'High Performers (Top 25%)',
                        'medium' => 'Medium Performers (Middle 50%)',
                        'low' => 'Low Performers (Bottom 25%)',
                    ])
                    ->query(function (Builder $query, $state) {
                        if (!$state) {
                            return $query;
                        }

                        // First get the total count of agents
                        $totalAgents = $query->count();
                        
                        return match ($state) {
                            'top' => $query->orderBy('plots_sold', 'desc')->limit((int) ($totalAgents * 0.1)),
                            'high' => $query->orderBy('plots_sold', 'desc')->limit((int) ($totalAgents * 0.25)),
                            'medium' => $query->orderBy('plots_sold', 'desc')
                                ->skip((int) ($totalAgents * 0.25))
                                ->limit((int) ($totalAgents * 0.5)),
                            'low' => $query->orderBy('plots_sold', 'asc')->limit((int) ($totalAgents * 0.25)),
                            default => $query,
                        };
                    }),
            ])
            ->actions([])  // Removed all actions
            ->bulkActions([])
            ->poll('60s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgentPerformance::route('/'),  // Only keeping the index page
        ];
    }
}