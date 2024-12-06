<?php

namespace App\Filament\Resources\AgentPerformanceResource\Pages;

use App\Filament\Resources\AgentPerformanceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViewAgentPerformance extends ViewRecord
{
    protected static string $resource = AgentPerformanceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        // Get additional statistics for the agent
        $stats = DB::table('sales')
            ->where('agent_id', $this->record->id)
            ->select([
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(sale_price) as total_revenue'),
                DB::raw('AVG(sale_price) as average_sale'),
                DB::raw('MAX(sale_price) as highest_sale'),
                DB::raw('COUNT(CASE WHEN created_at >= ? THEN 1 END) as sales_this_month', [Carbon::now()->startOfMonth()]),
                DB::raw('SUM(CASE WHEN created_at >= ? THEN sale_price END) as revenue_this_month', [Carbon::now()->startOfMonth()]),
            ])
            ->first();

        return $infolist
            ->schema([
                Section::make('Agent Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Agent Name'),
                                TextEntry::make('email')
                                    ->label('Email Address'),
                                TextEntry::make('role')
                                    ->label('Role')
                                    ->badge(),
                            ]),
                    ]),

                Section::make('Overall Performance')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('total_sales')
                                    ->label('Total Sales Made')
                                    ->state($stats->total_sales ?? 0),

                                TextEntry::make('total_revenue')
                                    ->label('Total Revenue Generated')
                                    ->money('ZMW')
                                    ->state($stats->total_revenue ?? 0),

                                TextEntry::make('average_sale')
                                    ->label('Average Sale Amount')
                                    ->money('ZMW')
                                    ->state($stats->average_sale ?? 0),

                                TextEntry::make('highest_sale')
                                    ->label('Highest Sale Amount')
                                    ->money('ZMW')
                                    ->state($stats->highest_sale ?? 0),
                            ]),
                    ]),

                Section::make('Current Month Performance')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('sales_this_month')
                                    ->label('Sales This Month')
                                    ->state($stats->sales_this_month ?? 0),

                                TextEntry::make('revenue_this_month')
                                    ->label('Revenue This Month')
                                    ->money('ZMW')
                                    ->state($stats->revenue_this_month ?? 0),
                            ]),
                    ]),
            ]);
    }
}