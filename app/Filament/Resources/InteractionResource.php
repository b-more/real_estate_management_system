<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InteractionResource\Pages;
use App\Models\Interaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InteractionResource extends Resource
{
    protected static ?string $model = Interaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Customer Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'type';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Interaction Details')
                    ->description('Record the details of the customer interaction')
                    ->schema([
                        Forms\Components\Select::make('customer_id')
                            ->relationship('customer', 'email')
                            //->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            //->searchable()
                            ->preload()
                            ->required()
                            ->label('Staff Member'),

                        Forms\Components\Select::make('type')
                            ->options([
                                'inquiry' => 'General Inquiry',
                                'viewing' => 'Property Viewing',
                                'negotiation' => 'Price Negotiation',
                                'follow_up' => 'Follow-up Call',
                                'complaint' => 'Complaint',
                                'documentation' => 'Documentation',
                                'payment' => 'Payment Discussion',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('interaction_date')
                            ->required()
                            ->default(now())
                            ->native(false),
                    ]),

                Forms\Components\Section::make('Interaction Content')
                    ->schema([
                        Forms\Components\RichEditor::make('notes')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'bulletList',
                                'orderedList',
                            ])
                            ->columnSpanFull(),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'requires_follow_up' => 'Requires Follow-up',
                            ])
                            ->default('pending')
                            ->required()
                            ->live(),

                        Forms\Components\DateTimePicker::make('follow_up_date')
                            ->label('Follow-up Date')
                            ->native(false)
                            ->required(fn (Forms\Get $get): bool => $get('status') === 'requires_follow_up')
                            ->visible(fn (Forms\Get $get): bool => $get('status') === 'requires_follow_up')
                            ->afterOrEqual('interaction_date')
                            ->default(fn (Forms\Get $get) => $get('status') === 'requires_follow_up' ? now()->addDays(1) : null)
                            ->helperText('Set the date when this interaction needs to be followed up'),
                    ]),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Select::make('priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium')
                            ->required(),

                        Forms\Components\TagsInput::make('tags')
                            ->separator(',')
                            ->suggestions([
                                'interested',
                                'hot-lead',
                                'price-sensitive',
                                'cash-buyer',
                                'mortgage',
                                'return-customer',
                                'referred',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('customer.email')
                ->label('Customer')
                ->searchable()
                ->sortable()
                ->description(fn (Interaction $record): string => 
                    $record->customer->first_name . ' ' . $record->customer->last_name
                )
                ->copyable()
                ->tooltip('Click to copy email')
                ->wrap(),

            Tables\Columns\TextColumn::make('customer.phone')
                ->label('Phone')
                ->searchable()
                ->toggleable(),

            Tables\Columns\TextColumn::make('interaction_date')
                ->label('Date')
                ->dateTime()
                ->sortable()
                ->description(fn (Interaction $record): string => 
                    $record->interaction_date->diffForHumans()
                ),

            Tables\Columns\BadgeColumn::make('type')
                ->colors([
                    'primary' => 'inquiry',
                    'success' => 'viewing',
                    'warning' => 'negotiation',
                    'danger' => 'complaint',
                    'info' => 'follow_up',
                    'secondary' => ['documentation', 'payment', 'other'],
                ])
                ->searchable()
                ->sortable(),

            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'success' => 'completed',
                    'warning' => 'in_progress',
                    'danger' => 'requires_follow_up',
                    'secondary' => 'pending',
                ])
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('notes')
                ->limit(40)
                ->tooltip(function (Interaction $record): string {
                    return strip_tags($record->notes);
                })
                ->html()
                ->searchable()
                ->wrap(),

            Tables\Columns\BadgeColumn::make('priority')
                ->colors([
                    'success' => 'low',
                    'warning' => 'medium',
                    'danger' => ['high', 'urgent'],
                ])
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('follow_up_date')
                ->dateTime()
                ->sortable()
                ->toggleable()
                ->color(fn (Interaction $record) => 
                    $record->follow_up_date && $record->follow_up_date <= now()
                        ? 'danger'
                        : 'success'
                ),

            Tables\Columns\TextColumn::make('user.name')
                ->label('Staff Member')
                ->searchable()
                ->sortable()
                ->toggleable()
                ->description(fn (Interaction $record): string => 
                    $record->user->email
                ),

            Tables\Columns\TagsColumn::make('tags')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->since()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
            ->defaultSort('interaction_date', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->filters([
                Tables\Filters\TernaryFilter::make('follow_up_required')
                    ->label('Follow-up Status')
                    ->placeholder('All Records')
                    ->trueLabel('Requires Follow-up')
                    ->falseLabel('No Follow-up Required')
                    ->queries(
                        true: fn (Builder $query) => $query->where('status', 'requires_follow_up'),
                        false: fn (Builder $query) => $query->where('status', '!=', 'requires_follow_up'),
                    ),

                Tables\Filters\SelectFilter::make('type')
                    ->multiple()
                    ->options([
                        'inquiry' => 'General Inquiry',
                        'viewing' => 'Property Viewing',
                        'negotiation' => 'Price Negotiation',
                        'follow_up' => 'Follow-up Call',
                        'complaint' => 'Complaint',
                        'documentation' => 'Documentation',
                        'payment' => 'Payment Discussion',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'requires_follow_up' => 'Requires Follow-up',
                    ]),

                Tables\Filters\SelectFilter::make('priority')
                    ->multiple()
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('from'),
                                Forms\Components\DatePicker::make('until'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('interaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('interaction_date', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Staff Member')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\Filter::make('interaction_period')
                    ->form([
                        Forms\Components\Select::make('period')
                            ->options([
                                'today' => 'Today',
                                'yesterday' => 'Yesterday',
                                'last_7_days' => 'Last 7 Days',
                                'last_30_days' => 'Last 30 Days',
                                'this_month' => 'This Month',
                                'last_month' => 'Last Month',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['period'], function (Builder $query, $period) {
                            $query->where(function (Builder $query) use ($period) {
                                match ($period) {
                                    'today' => $query->whereDate('interaction_date', Carbon::today()),
                                    'yesterday' => $query->whereDate('interaction_date', Carbon::yesterday()),
                                    'last_7_days' => $query->where('interaction_date', '>=', Carbon::now()->subDays(7)),
                                    'last_30_days' => $query->where('interaction_date', '>=', Carbon::now()->subDays(30)),
                                    'this_month' => $query->whereMonth('interaction_date', Carbon::now()->month),
                                    'last_month' => $query->whereMonth('interaction_date', Carbon::now()->subMonth()->month),
                                };
                            });
                        });
                    }),
            ])
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->form([
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\TextInput::make('customer.email')
                                        ->label('Customer Email')
                                        ->disabled(),
                                    Forms\Components\TextInput::make('customer.phone')
                                        ->label('Customer Phone')
                                        ->disabled(),
                                    Forms\Components\TextInput::make('type')
                                        ->disabled(),
                                    Forms\Components\TextInput::make('status')
                                        ->disabled(),
                                    Forms\Components\DateTimePicker::make('interaction_date')
                                        ->disabled(),
                                    Forms\Components\RichEditor::make('notes')
                                        ->disabled()
                                        ->columnSpanFull(),
                                    Forms\Components\TagsInput::make('tags')
                                        ->disabled(),
                                    Forms\Components\TextInput::make('priority')
                                        ->disabled(),
                                ]),
                        ]),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('follow_up')
                        ->icon('heroicon-o-phone')
                        ->requiresConfirmation()
                        ->action(function (Interaction $record) {
                            Interaction::create([
                                'customer_id' => $record->customer_id,
                                'user_id' => auth()->id(),
                                'type' => 'follow_up',
                                'status' => 'pending',
                                'interaction_date' => now(),
                                'notes' => "Follow-up to interaction from {$record->interaction_date->format('M d, Y')}",
                            ]);
                        })
                        ->visible(fn (Interaction $record) => $record->status === 'requires_follow_up'),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('markAsCompleted')
                        ->label('Mark as Completed')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'completed']))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-chat-bubble-left-right')
            ->emptyStateHeading('No Interactions Yet')
            ->emptyStateDescription('Start recording customer interactions by clicking the button below.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Record Interaction'),
            ])
            ->striped()
            ->poll();
    }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInteractions::route('/'),
            'create' => Pages\CreateInteraction::route('/create'),
            'edit' => Pages\EditInteraction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'requires_follow_up')
            ->whereDate('follow_up_date', '<=', now())
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getNavigationBadge() ? 'warning' : null;
    }
}