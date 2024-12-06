<?php

namespace App\Filament\Resources\AgentPerformanceResource\Pages;

use App\Filament\Resources\AgentPerformanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgentPerformance extends EditRecord
{
    protected static string $resource = AgentPerformanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
