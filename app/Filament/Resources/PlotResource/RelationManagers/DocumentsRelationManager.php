<?php

namespace App\Filament\Resources\PlotResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $title = 'Plot Documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Document Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter document title'),

                        Forms\Components\Select::make('file_type')
                            ->required()
                            ->options([
                                'site_plan' => 'Site Plan',
                                'title_deed' => 'Title Deed',
                                'survey_report' => 'Survey Report',
                                'approval' => 'Council Approval',
                                'other' => 'Other Document',
                            ])
                            ->default('other')
                            ->native(false),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('Document File')
                            ->required()
                            ->directory('plot-documents')
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
                    ])
                    ->columns(2),
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
                        'primary' => 'site_plan',
                        'success' => 'title_deed',
                        'warning' => 'survey_report',
                        'danger' => 'approval',
                        'secondary' => 'other',
                    ]),

                Tables\Columns\TextColumn::make('description')
                    ->limit(30)
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('file_type')
                    ->options([
                        'site_plan' => 'Site Plan',
                        'title_deed' => 'Title Deed',
                        'survey_report' => 'Survey Report',
                        'approval' => 'Council Approval',
                        'other' => 'Other Document',
                    ])
                    ->multiple(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['documentable_type'] = 'App\Models\Plot';
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->label('Download')
                        ->url(fn ($record) => '/storage/' . $record->file_path)
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
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-document')
            ->emptyStateHeading('No Documents Yet')
            ->emptyStateDescription('Start by uploading your first document.')
            ->poll();
    }
}