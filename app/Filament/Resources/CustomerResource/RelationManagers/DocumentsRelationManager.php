<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $title = 'Documents';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Document Information')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Enter document title'),

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
                        ->placeholder('Enter document description'),
                ])->columns(1),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

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

                    Tables\Columns\TextColumn::make('formatted_file_size')
                    ->label('Size')
                    ->toggleable()
                    ->getStateUsing(function ($record) {
                        if (!$record->file_size) {
                            return '0 B';
                        }
                        return $record->formatted_file_size;
                    }),

                Tables\Columns\TextColumn::make('uploader.name')
                    ->label('Uploaded By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data, string $model): mixed {
                        // Get file info
                        if (isset($data['file_path'])) {
                            $data['file_size'] = Storage::disk('public')->size($data['file_path']);
                            $data['file_extension'] = pathinfo($data['file_path'], PATHINFO_EXTENSION);
                            
                            // If uploaded by is not set, set it
                            if (!isset($data['uploaded_by']) && auth()->check()) {
                                $data['uploaded_by'] = auth()->id();
                            }
                        }
                        
                        return $model::create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->label('Download')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
                        
                    Tables\Actions\DeleteAction::make()
                        ->before(function ($record) {
                            Storage::disk('public')->delete($record->file_path);
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (Collection $records) {
                            $records->each(function ($record) {
                                Storage::disk('public')->delete($record->file_path);
                            });
                        }),
                ]),
            ]);
    }
}