<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlotResource\Pages;
use App\Filament\Resources\PlotResource\RelationManagers;
use App\Models\Plot;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlotResource extends Resource
{
    protected static ?string $model = Plot::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Property Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'plot_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Enter the basic plot details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('plot_number')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Enter plot number'),

                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options([
                                        'available' => 'Available',
                                        'reserved' => 'Reserved',
                                        'sold' => 'Sold',
                                    ])
                                    ->default('available')
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Size & Price')
                    ->description('Specify plot dimensions and pricing')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->columnSpan(1)
                                    ->schema([
                                        Forms\Components\TextInput::make('length')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('Length')
                                            ->minValue(1)
                                            ->suffix('m'),
                                        
                                        Forms\Components\TextInput::make('width')
                                            ->required()
                                            ->numeric()
                                            ->placeholder('Width')
                                            ->minValue(1)
                                            ->suffix('m'),
                                    ])
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (isset($state['length']) && isset($state['width'])) {
                                            $set('size', $state['length'] . ' x ' . $state['width']);
                                            $set('total_area', $state['length'] * $state['width']);
                                        }
                                    }),

                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('ZMW')
                                    ->minValue(0)
                                    ->placeholder('Enter price'),
                            ]),
                            
                        Forms\Components\TextInput::make('size')
                            ->required()
                            ->disabled()
                            ->dehydrated(true)  // Change this to true
                            ->placeholder('Calculated size')
                            ->helperText('Size will be calculated automatically'),

                        Forms\Components\Select::make('size_unit')  // Add this field
                            ->required()
                            ->options([
                                'sq_m' => 'Square Meters',
                                'sq_ft' => 'Square Feet',
                            ])
                            ->default('sq_m')
                            ->native(false),

                        Forms\Components\TextInput::make('total_area')
                            ->disabled()
                            ->dehydrated(false)
                            ->suffix('square meters')
                            ->placeholder('Total area'),
                    ]),

                Forms\Components\Section::make('Location Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('location')
                                    ->required()
                                    ->options([
                                        'Chilenje' => 'Chilenje',
                                        'Kabulonga' => 'Kabulonga',
                                        'Woodlands' => 'Woodlands',
                                        'Chalala' => 'Chalala',
                                        'Matero' => 'Matero',
                                        'Kalingalinga' => 'Kalingalinga',
                                        'Avondale' => 'Avondale',
                                        'Roma' => 'Roma',
                                    ])
                                    ->searchable()
                                    ->native(false)
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                    ]),

                                Forms\Components\Select::make('legal_status')
                                    ->required()
                                    ->options([
                                        'titled' => 'Titled Land',
                                        'traditional' => 'Traditional Land',
                                    ])
                                    ->reactive()
                                    ->native(false),
                            ]),

                        Forms\Components\Textarea::make('address')
                            ->required()
                            ->placeholder('Enter complete address'),
                    ]),

                Forms\Components\Section::make('Legal Documentation')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\FileUpload::make('site_plan')
                                    ->required()
                                    ->directory('plots/site-plans')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->helperText('Upload the site plan (PDF or Image)'),
                            ]),

                        // Conditional fields based on legal status
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\FileUpload::make('title_deed')
                                    ->required()
                                    ->visible(fn (callable $get) => $get('legal_status') === 'titled')
                                    ->directory('plots/title-deeds')
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->helperText('Upload the title deed (PDF only)'),

                                Forms\Components\Repeater::make('legal_practitioners')
                                    ->visible(fn (callable $get) => $get('legal_status') === 'titled')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->placeholder('Practitioner name'),
                                        Forms\Components\TextInput::make('license_number')
                                            ->required()
                                            ->placeholder('License number'),
                                        Forms\Components\TextInput::make('firm')
                                            ->required()
                                            ->placeholder('Law firm'),
                                    ])
                                    ->columnSpanFull(),

                                Forms\Components\FileUpload::make('chief_letter')
                                    ->required()
                                    ->visible(fn (callable $get) => $get('legal_status') === 'traditional')
                                    ->directory('plots/chief-letters')
                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                    ->helperText('Upload letter from Chief/Headman'),

                                Forms\Components\TextInput::make('chief_name')
                                    ->visible(fn (callable $get) => $get('legal_status') === 'traditional')
                                    ->required()
                                    ->placeholder('Name of Chief/Headman'),
                            ]),
                    ]),

                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\Select::make('amenities')
                            ->multiple()
                            ->options([
                                'water' => 'Water Connection',
                                'electricity' => 'Electricity',
                                'road_access' => 'Road Access',
                                'borehole' => 'Borehole',
                                'corner' => 'Corner Plot',
                                'commercial' => 'Commercial Zone',
                            ])
                            ->searchable()
                            ->native(false),

                        Forms\Components\Textarea::make('description')
                            ->placeholder('Enter plot description')
                            ->rows(3),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('coordinates.lat')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->placeholder('Enter latitude'),

                                Forms\Components\TextInput::make('coordinates.lng')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->placeholder('Enter longitude'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('plot_number')
                ->label('Plot #')
                ->searchable()
                ->sortable()
                ->copyable()
                ->tooltip('Click to copy')
                ->weight('bold'),

            Tables\Columns\TextColumn::make('location')
                ->searchable()
                ->sortable()
                ->icon('heroicon-o-map-pin'),

            Tables\Columns\TextColumn::make('dimensions')
                ->label('Dimensions')
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn ($record) => "{$record->length}m × {$record->width}m")
                ->description(fn ($record) => "{$record->size} {$record->size_unit}"),

            Tables\Columns\TextColumn::make('price')
                ->money('ZMW')
                ->sortable()
                ->alignEnd(),

            Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'success' => 'available',
                    'warning' => 'reserved',
                    'danger' => 'sold',
                ])
                ->icons([
                    'heroicon-o-check-circle' => 'available',
                    'heroicon-o-clock' => 'reserved',
                    'heroicon-o-no-symbol' => 'sold',
                ]),

            Tables\Columns\BadgeColumn::make('legal_status')
                ->colors([
                    'primary' => 'titled',
                    'warning' => 'traditional',
                ]),

            Tables\Columns\IconColumn::make('amenities')
                ->label('Amenities')
                ->icons([
                    'heroicon-o-bolt' => fn ($state) => in_array('electricity', $state ?? []),
                    'heroicon-o-water-drop' => fn ($state) => in_array('water', $state ?? []),
                    'heroicon-o-truck' => fn ($state) => in_array('road_access', $state ?? []),
                    'heroicon-o-wrench-screwdriver' => fn ($state) => in_array('borehole', $state ?? []),
                ])
                ->tooltip(function ($record) {
                    return implode(', ', array_map('ucfirst', $record->amenities ?? []));
                }),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Listed On')
                ->date('j M Y')
                ->sortable()
                ->toggleable(),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('Last Updated')
                ->since()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'available' => 'Available',
                    'reserved' => 'Reserved',
                    'sold' => 'Sold',
                ])
                ->multiple()
                ->label('Status'),
            
            Tables\Filters\SelectFilter::make('location')
                ->options([
                    'Chilenje' => 'Chilenje',
                    'Kabulonga' => 'Kabulonga',
                    'Woodlands' => 'Woodlands',
                    'Chalala' => 'Chalala',
                    'Matero' => 'Matero',
                    'Kalingalinga' => 'Kalingalinga',
                    'Avondale' => 'Avondale',
                    'Roma' => 'Roma',
                ])
                ->multiple()
                ->searchable(),

            Tables\Filters\SelectFilter::make('legal_status')
                ->options([
                    'titled' => 'Titled Land',
                    'traditional' => 'Traditional Land',
                ])
                ->multiple(),

            Tables\Filters\Filter::make('price_range')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('price_from')
                                ->numeric()
                                ->label('Min Price (ZMW)')
                                ->placeholder('0'),
                            Forms\Components\TextInput::make('price_to')
                                ->numeric()
                                ->label('Max Price (ZMW)')
                                ->placeholder('Any'),
                        ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['price_from'],
                            fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                        )
                        ->when(
                            $data['price_to'],
                            fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                        );
                }),

            Tables\Filters\Filter::make('size_range')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('size_from')
                                ->numeric()
                                ->label('Min Size (sq m)')
                                ->placeholder('0'),
                            Forms\Components\TextInput::make('size_to')
                                ->numeric()
                                ->label('Max Size (sq m)')
                                ->placeholder('Any'),
                        ]),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['size_from'],
                            fn (Builder $query, $size): Builder => $query->where('size', '>=', $size),
                        )
                        ->when(
                            $data['size_to'],
                            fn (Builder $query, $size): Builder => $query->where('size', '<=', $size),
                        );
                }),

            Tables\Filters\SelectFilter::make('amenities')
                ->multiple()
                ->options([
                    'water' => 'Water Connection',
                    'electricity' => 'Electricity',
                    'road_access' => 'Road Access',
                    'borehole' => 'Borehole',
                    'corner' => 'Corner Plot',
                    'commercial' => 'Commercial Zone',
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->when($data['values'], function (Builder $query, $values) {
                        foreach ($values as $value) {
                            $query->whereJsonContains('amenities', $value);
                        }
                    });
                }),

            Tables\Filters\TrashedFilter::make(),
        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('download_documents')
                ->label('Documents')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn ($record) => route('plots.documents.download', $record))
                ->openUrlInNewTab()
                ->visible(fn ($record) => 
                    $record->site_plan || 
                    ($record->legal_status === 'titled' && $record->title_deed) || 
                    ($record->legal_status === 'traditional' && $record->chief_letter)
            ),
                
                Tables\Actions\DeleteAction::make(),
            ]),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('export')
                    ->label('Export Selected')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn (Collection $records) => static::export($records)),
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
            RelationManagers\DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlots::route('/'),
            'create' => Pages\CreatePlot::route('/create'),
            'edit' => Pages\EditPlot::route('/{record}/edit'),
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