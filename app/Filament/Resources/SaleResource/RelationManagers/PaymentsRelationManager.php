<?php

namespace App\Filament\Resources\SaleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('ZMW'),

                Forms\Components\Select::make('type')
                    ->options([
                        'deposit' => 'Deposit',
                        'installment' => 'Installment',
                        'final' => 'Final Payment',
                    ])
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->required(),

                Forms\Components\DatePicker::make('due_date')
                    ->required(),

                Forms\Components\DatePicker::make('payment_date'),

                Forms\Components\TextInput::make('receipt_number'),

                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'deposit',
                        'warning' => 'installment',
                        'success' => 'final',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('receipt_number')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
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