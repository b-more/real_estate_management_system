<?php

namespace App\Filament\Resources\AgentPerformanceResource\Pages;

use App\Filament\Resources\AgentPerformanceResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListAgentPerformance extends ListRecords
{
    protected static string $resource = AgentPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}