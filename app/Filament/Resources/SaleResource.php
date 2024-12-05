<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Filament\Resources\SaleResource\RelationManagers;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Sale Information')
                    ->schema([
                        Forms\Components\Select::make('plot_id')
                            ->relationship('plot', 'plot_number')
                            //->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'first_name')
                            //->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('agent_id')
                            ->relationship('agent', 'name')
                            //->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('sale_price')
                            ->required()
                            ->numeric()
                            ->prefix('ZMW'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'negotiation' => 'Negotiation',
                                'agreement' => 'Agreement',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('negotiation'),

                        Forms\Components\DatePicker::make('sale_date')
                            ->required()
                            ->default(now()),

                        Forms\Components\Textarea::make('notes')
                            ->columnSpanFull()
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('plot.plot_number')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->formatStateUsing(fn ($record) => $record->customer->first_name . ' ' . $record->customer->last_name)
                    ->searchable(['first_name', 'last_name']),

                Tables\Columns\TextColumn::make('agent.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sale_price')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'negotiation',
                        'primary' => 'agreement',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                Tables\Columns\TextColumn::make('sale_date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'negotiation' => 'Negotiation',
                        'agreement' => 'Agreement',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),

                Tables\Filters\Filter::make('sale_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sale_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('sale_date', '<=', $date),
                            );
                    }),

                Tables\Filters\TrashedFilter::make(),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}