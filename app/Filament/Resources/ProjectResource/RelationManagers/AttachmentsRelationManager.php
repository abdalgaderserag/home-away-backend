<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Filament\Tables\Actions\Action; // Import Action class

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('project_id')
                    ->default($this->ownerRecord->id),

                Forms\Components\Hidden::make('owner_id')
                    ->default(fn($livewire) => $livewire->ownerRecord->client_id),

                Forms\Components\FileUpload::make('url')
                    ->label('Image')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                    ->disk('s3')
                    ->getUploadedFileUrlUsing(fn(string $path): string => Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5)))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('url')
            ->columns([
                Tables\Columns\ImageColumn::make('url')
                    ->label('Preview')
                    ->disk('s3')
                    ->url(fn(object $record): ?string => Storage::disk('s3')->temporaryUrl($record->url, now()->addMinutes(5)))
                    ->square()
                    ->size(80),
                Tables\Columns\TextColumn::make('url')
                    ->label('File Name')
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Uploaded By')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Uploaded At')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn(object $record): string => Storage::disk('s3')->temporaryUrl($record->url, now()->addMinutes(5), [
                        'ResponseContentType' => Storage::disk('s3')->mimeType($record->url),
                        'ResponseContentDisposition' => 'attachment; filename="' . basename($record->url) . '"',
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
            ]);
    }
}
