<?php

namespace App\Filament\Resources\PlotResource\Pages;

use App\Filament\Resources\PlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlot extends EditRecord
{
    protected static string $resource = PlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
