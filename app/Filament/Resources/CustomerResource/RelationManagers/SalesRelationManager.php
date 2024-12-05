<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SalesRelationManager extends RelationManager
{
    protected static string $relationship = 'sales';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'Sales';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('plot_id')
                ->relationship('plot', 'plot_number')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\Select::make('agent_id')
                ->relationship('agent', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Forms\Components\TextInput::make('sale_price')
                ->required()
                ->numeric()
                ->prefix('ZMW'),
            Forms\Components\Select::make('status')
                ->required()
                ->options([
                    'pending' => 'Pending',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ]),
            Forms\Components\DatePicker::make('sale_date')
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->rows(3)
                ->maxLength(500),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plot.plot_number')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->money('ZMW')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('sale_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('agent.name')
                    ->sortable(),
            ])
            ->defaultSort('sale_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('agent')
                    ->relationship('agent', 'name'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}