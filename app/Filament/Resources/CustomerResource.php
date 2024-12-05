<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Customer Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'full_name';
    protected static int $globalSearchResultsLimit = 20;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'email', 'phone', 'company_name'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Basic Information')
                            ->schema([
                                Forms\Components\Select::make('title')
                                    ->options([
                                        'Mr.' => 'Mr.',
                                        'Mrs.' => 'Mrs.',
                                        'Ms.' => 'Ms.',
                                        'Dr.' => 'Dr.',
                                        'Prof.' => 'Prof.',
                                    ])
                                    ->nullable(),
                                
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->required()
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('alternate_phone')
                                    ->tel()
                                    ->maxLength(255),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Address Details')
                            ->schema([
                                Forms\Components\Textarea::make('address')
                                    ->rows(3)
                                    ->columnSpanFull(),
                                
                                Forms\Components\TextInput::make('city')
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('state')
                                    ->maxLength(255),
                                
                                Forms\Components\TextInput::make('postal_code')
                                    ->maxLength(255),
                                
                                Forms\Components\Select::make('country')
                                    ->searchable()
                                    ->options([
                                        'ZM' => 'Zambia',
                                        'ZW' => 'Zimbabwe',
                                        'BW' => 'Botswana',
                                        'NA' => 'Namibia',
                                        'MZ' => 'Mozambique',
                                    ])
                                    ->default('ZM'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status & Classification')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'individual' => 'Individual',
                                        'corporate' => 'Corporate',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('individual'),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'blocked' => 'Blocked',
                                    ])
                                    ->required()
                                    ->default('active'),

                                Forms\Components\Select::make('source')
                                    ->options([
                                        'referral' => 'Referral',
                                        'website' => 'Website',
                                        'agent' => 'Sales Agent',
                                        'advertisement' => 'Advertisement',
                                        'other' => 'Other',
                                    ]),

                                Forms\Components\Select::make('assigned_agent_id')
                                    ->relationship('assignedAgent', 'name')
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\TagsInput::make('tags')
                                    ->separator(','),
                            ]),

                        Forms\Components\Section::make('Additional Details')
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created Date')
                                    ->content(fn (?Customer $record): string => $record ? $record->created_at->format('d/m/Y H:i') : '-'),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last Modified')
                                    ->content(fn (?Customer $record): string => $record ? $record->updated_at->format('d/m/Y H:i') : '-'),
                                
                                Forms\Components\Placeholder::make('last_purchase_date')
                                    ->label('Last Purchase')
                                    ->content(fn (?Customer $record): string => $record?->last_purchase_date ? $record->last_purchase_date->format('d/m/Y H:i') : 'No purchases yet'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),

                Forms\Components\Section::make('Business Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'corporate'),

                        Forms\Components\TextInput::make('occupation')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'individual'),

                        Forms\Components\DatePicker::make('date_of_birth')
                            ->maxDate(now())
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'individual'),
                        
                        Forms\Components\TextInput::make('nationality')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'individual'),

                        Forms\Components\Select::make('id_type')
                            ->options([
                                'national_id' => 'National ID',
                                'passport' => 'Passport',
                                'drivers_license' => 'Driver\'s License',
                            ])
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'individual'),

                        Forms\Components\TextInput::make('id_number')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get): bool => $get('type') === 'individual'),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make('Financial Information')
                    ->schema([
                        Forms\Components\TextInput::make('credit_limit')
                            ->numeric()
                            ->prefix('ZMW')
                            ->maxValue(1000000)
                            ->default(0),

                        Forms\Components\TextInput::make('total_purchases')
                            ->numeric()
                            ->prefix('ZMW')
                            ->disabled(),
                    ])
                    ->columns(2)
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Section::make('Notes & Preferences')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\KeyValue::make('preferences')
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(['lg' => 2]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Customer Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name'])
                    ->weight(FontWeight::Bold)
                    ->description(fn (Customer $record): string => $record->email),

                    Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->toggleable()
                    ->description(fn (Customer $record): ?string => 
                        $record->type === 'corporate' ? null : 'N/A'),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'individual',
                        'success' => 'corporate',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'blocked',
                    ]),

                Tables\Columns\TextColumn::make('total_purchases')
                    ->money('ZMW')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_purchase_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('assignedAgent.name')
                    ->label('Agent')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'individual' => 'Individual',
                        'corporate' => 'Corporate',
                    ])
                    ->multiple(),

                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'blocked' => 'Blocked',
                    ])
                    ->multiple(),

                SelectFilter::make('assigned_agent')
                    ->relationship('assignedAgent', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Filter::make('has_purchases')
                    ->query(fn (Builder $query): Builder => $query->where('total_purchases', '>', 0)),

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
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SalesRelationManager::class,
            RelationManagers\InteractionsRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            //'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
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