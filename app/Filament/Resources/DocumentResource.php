<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Database\Eloquent\Collection;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document';
    protected static ?string $navigationGroup = 'Document Management';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Information')
                    ->description('Basic information about the document')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter document title'),

                        Forms\Components\Select::make('documentable_type')
                            ->label('Related To')
                            ->required()
                            ->options([
                                'App\Models\Plot' => 'Plot',
                                'App\Models\Customer' => 'Customer',
                                'App\Models\Sale' => 'Sale',
                            ])
                            ->reactive(),

                        Forms\Components\Select::make('documentable_id')
                            ->label('Select Record')
                            ->required()
                            ->options(function (callable $get) {
                                $type = $get('documentable_type');
                                if (!$type) return [];

                                $model = new $type;
                                return $model::all()->pluck('id', 'id')
                                    ->map(function ($id) use ($type) {
                                        $record = $type::find($id);
                                        return match($type) {
                                            'App\Models\Plot' => "Plot #{$record->plot_number}",
                                            'App\Models\Customer' => "{$record->first_name} {$record->last_name}",
                                            'App\Models\Sale' => "Sale #{$id}",
                                            default => "Record #{$id}"
                                        };
                                    });
                            })
                            ->searchable(),

                        Forms\Components\Select::make('file_type')
                            ->required()
                            ->options([
                                'contract' => 'Contract',
                                'deed' => 'Title Deed',
                                'id' => 'Identification',
                                'permit' => 'Permit',
                                'receipt' => 'Receipt',
                                'other' => 'Other',
                            ])
                            ->default('other'),
                    ]),

                Forms\Components\Section::make('File Upload')
                    ->description('Upload document file')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                        ->label('Document File')
                        ->required()
                        ->directory('documents')
                        ->preserveFilenames()
                        ->disk('public')
                        ->visibility('public')
                        ->downloadable()
                        ->openable()
                        ->previewable()
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'image/jpeg',
                            'image/png',
                        ])
                        ->maxSize(50 * 1024),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->placeholder('Enter document description')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('documentable_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_type')
                    ->badge()
                    ->colors([
                        'primary' => 'contract',
                        'success' => 'deed',
                        'warning' => 'id',
                        'danger' => 'permit',
                        'info' => 'receipt',
                        'secondary' => 'other',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('documentable_type')
                    ->label('Related To')
                    ->options([
                        'App\Models\Plot' => 'Plot',
                        'App\Models\Customer' => 'Customer',
                        'App\Models\Sale' => 'Sale',
                    ]),

                Tables\Filters\SelectFilter::make('file_type')
                    ->options([
                        'contract' => 'Contract',
                        'deed' => 'Title Deed',
                        'id' => 'Identification',
                        'permit' => 'Permit',
                        'receipt' => 'Receipt',
                        'other' => 'Other',
                    ]),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->label('Download')
                        ->url(fn (Document $record) => $record->download_url)
                        ->openUrlInNewTab(),
                        
                    Tables\Actions\DeleteAction::make()
                        ->before(function (Document $record) {
                            Storage::disk('public')->delete($record->file_path);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            // Delete all files before deleting records
                            $records->each(function ($record) {
                                Storage::delete($record->file_path);
                            });
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'description', 'file_type'];
    }
}