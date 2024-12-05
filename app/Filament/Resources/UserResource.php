<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Details')
                    ->description('Manage user personal information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Enter full name'),
                                
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Enter email address'),
                            ]),
                    ]),

                Forms\Components\Section::make('Authentication')
                    ->description('Manage user authentication details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255)
                                    ->placeholder('Enter password'),

                                Forms\Components\Select::make('role')
                                    ->required()
                                    ->options([
                                        'admin' => 'Administrator',
                                        'agent' => 'Sales Agent',
                                        'staff' => 'Staff Member',
                                    ])
                                    ->default('staff')
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Status & Settings')
                    ->description('Manage user status and system settings')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->required()
                                    ->default(true)
                                    ->helperText('Inactive users cannot access the system'),

                                Forms\Components\DateTimePicker::make('email_verified_at')
                                    ->label('Email Verified At')
                                    ->placeholder('Not verified')
                                    ->native(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (User $record): string => $record->email),

                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'danger' => 'admin',
                        'success' => 'agent',
                        'warning' => 'staff',
                    ])
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable()
                    ->label('Active'),

                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->label('Verified'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Administrator',
                        'agent' => 'Sales Agent',
                        'staff' => 'Staff Member',
                    ])
                    ->multiple(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean()
                    ->trueLabel('Active Users')
                    ->falseLabel('Inactive Users')
                    ->placeholder('All Users'),

                Tables\Filters\TrashedFilter::make(),
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

    public static function getRelations(): array
    {
        return [
            // RelationManagers\SalesRelationManager::class,
            // RelationManagers\InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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