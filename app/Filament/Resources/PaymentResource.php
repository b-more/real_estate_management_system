<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financial Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'receipt_number';
    protected static ?string $modelLabel = 'Payment Record';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Sale Information')
                            ->description('Link this payment to a sale')
                            ->schema([
                                Forms\Components\Select::make('sale_id')
                                    ->label('Select Plot Sale')
                                    ->options(function () {
                                        return Sale::query()
                                            ->with(['plot', 'customer'])
                                            ->get()
                                            ->mapWithKeys(function ($sale) {
                                                return [
                                                    $sale->id => "Plot {$sale->plot->plot_number} - {$sale->customer->name} (ZMW {$sale->sale_price})"
                                                ];
                                            });
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $sale = Sale::find($state);
                                            if ($sale) {
                                                $payments = $sale->payments()->sum('amount');
                                                $remaining = $sale->sale_price - $payments;
                                                $set('remaining_balance', $remaining);
                                            }
                                        }
                                    })
                                    ->helperText('Search by plot number or customer name'),

                                Forms\Components\TextInput::make('remaining_balance')
                                    ->label('Remaining Balance')
                                    ->prefix('ZMW')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Outstanding amount for this sale'),

                                Forms\Components\TextInput::make('amount')
                                    ->label('Payment Amount')
                                    ->required()
                                    ->numeric()
                                    ->prefix('ZMW')
                                    ->minValue(0)
                                    ->helperText('Enter the amount being paid')
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, \Closure $fail) {
                                                $saleId = request()->input('data.sale_id');
                                                if ($saleId) {
                                                    $sale = Sale::find($saleId);
                                                    $payments = $sale->payments()->sum('amount');
                                                    $remaining = $sale->sale_price - $payments;
                                                    
                                                    if ($value > $remaining) {
                                                        $fail("Payment amount cannot exceed remaining balance of ZMW {$remaining}");
                                                    }
                                                }
                                            };
                                        },
                                    ]),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Payment Details')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->options([
                                        'deposit' => 'Initial Deposit',
                                        'installment' => 'Regular Installment',
                                        'final_payment' => 'Final Payment',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->helperText('Select the type of payment being made'),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Payment Pending',
                                        'completed' => 'Payment Completed',
                                        'failed' => 'Payment Failed',
                                        'refunded' => 'Payment Refunded',
                                    ])
                                    ->required()
                                    ->default('completed')
                                    ->native(false)
                                    ->helperText('Current status of this payment'),

                                Forms\Components\TextInput::make('receipt_number')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->prefixIcon('heroicon-o-receipt-percent')
                                    ->helperText('Unique receipt number for this payment'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Payment Schedule')
                            ->schema([
                                Forms\Components\DateTimePicker::make('due_date')
                                    ->required()
                                    ->label('Payment Due Date')
                                    ->native(false)
                                    ->helperText('When payment is/was due'),

                                Forms\Components\DateTimePicker::make('payment_date')
                                    ->label('Actual Payment Date')
                                    ->native(false)
                                    ->helperText('When payment was received'),
                            ]),

                        Forms\Components\Section::make('Additional Information')
                            ->schema([
                                Forms\Components\Textarea::make('notes')
                                    ->rows(4)
                                    ->helperText('Any additional notes or comments about this payment'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Receipt #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Receipt number copied')
                    ->copyMessageDuration(1500),

                Tables\Columns\TextColumn::make('sale.customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sale.plot.plot_number')
                    ->label('Plot #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('ZMW')
                    ->sortable()
                    ->alignEnd(),

                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'warning' => 'deposit',
                        'success' => 'installment', 
                        'primary' => 'final_payment',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'deposit' => 'Initial Deposit',
                        'installment' => 'Installment',
                        'final_payment' => 'Final Payment',
                        default => $state,
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'info' => 'refunded',
                    ]),

                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->multiple()
                    ->options([
                        'deposit' => 'Initial Deposit',
                        'installment' => 'Installment',
                        'final_payment' => 'Final Payment',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),

                Tables\Filters\Filter::make('overdue')
                    ->label('Show Overdue')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNull('payment_date')
                        ->where('due_date', '<', now())
                    )
                    ->toggle(),

                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('due_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }
}