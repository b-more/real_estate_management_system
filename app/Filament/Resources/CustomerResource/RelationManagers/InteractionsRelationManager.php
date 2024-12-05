<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';
    protected static ?string $recordTitleAttribute = 'type';
    protected static ?string $title = 'Interactions';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->required()
                ->options([
                    'call' => 'Phone Call',
                    'email' => 'Email',
                    'meeting' => 'Meeting',
                    'site_visit' => 'Site Visit',
                    'other' => 'Other',
                ]),
            Forms\Components\Textarea::make('notes')
                ->required()
                ->rows(3)
                ->maxLength(500),
            Forms\Components\DateTimePicker::make('interaction_date')
                ->required()
                ->default(now()),
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->required()
                ->searchable()
                ->preload(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('interaction_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'call',
                        'success' => 'email',
                        'warning' => 'meeting',
                        'danger' => 'site_visit',
                        'gray' => 'other',
                    ]),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->sortable(),
            ])
            ->defaultSort('interaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'call' => 'Phone Call',
                        'email' => 'Email',
                        'meeting' => 'Meeting',
                        'site_visit' => 'Site Visit',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name'),
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

