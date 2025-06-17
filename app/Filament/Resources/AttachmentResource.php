<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttachmentResource\Pages;
use App\Filament\Resources\AttachmentResource\RelationManagers;
use App\Models\Attachment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\Action;


class AttachmentResource extends Resource
{
    protected static ?string $model = Attachment::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Content Management';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('owner.avatar'),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Uploaded by')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Attached To')
                    ->badge() // Display as a badge
                    ->getStateUsing(function (Attachment $record): ?string {
                        if ($record->project_id) {
                            return 'Project';
                        }
                        if ($record->message_id) {
                            return 'Message';
                        }
                        if ($record->milestone_id) {
                            return 'Milestone';
                        }
                        if ($record->verification_id) {
                            return 'Verification';
                        }
                        if ($record->user_id) { // Consider if user_id implies attachment to user profile
                            return 'User Profile';
                        }
                        return 'N/A';
                    })
                    ->color(function (Attachment $record): string {
                        if ($record->project_id) {
                            return 'primary'; // Or any Filament color: success, warning, info, danger, primary, gray
                        }
                        if ($record->message_id) {
                            return 'info';
                        }
                        if ($record->milestone_id) {
                            return 'success';
                        }
                        if ($record->verification_id) {
                            return 'warning';
                        }
                        if ($record->user_id) {
                            return 'danger';
                        }
                        return 'gray'; // Default color
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAttachments::route('/'),
        ];
    }
}
