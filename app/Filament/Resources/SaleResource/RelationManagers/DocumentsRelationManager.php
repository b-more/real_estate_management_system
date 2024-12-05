<?php

namespace App\Filament\Resources\SaleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->directory('sales/documents'),

                Forms\Components\Select::make('file_type')
                    ->options([
                        'contract' => 'Sales Contract',
                        'agreement' => 'Sale Agreement',
                        'receipt' => 'Payment Receipt',
                        'transfer' => 'Transfer Document',
                        'other' => 'Other Document',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('file_type')
                    ->badge()
                    ->colors([
                        'primary' => 'contract',
                        'success' => 'agreement',
                        'warning' => 'receipt',
                        'danger' => 'transfer',
                        'gray' => 'other',
                    ]),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('file_type')
                    ->options([
                        'contract' => 'Sales Contract',
                        'agreement' => 'Sale Agreement',
                        'receipt' => 'Payment Receipt',
                        'transfer' => 'Transfer Document',
                        'other' => 'Other Document',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}