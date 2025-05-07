<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Filament\Resources\FileResource\RelationManagers;
use App\Models\Attachment;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class FileResource extends Resource
{
    protected static ?string $model = Attachment::class;
    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('File Upload')
                    ->schema([
                        Forms\Components\FileUpload::make('url')
                            ->label('File')
                            ->required()
                            ->preserveFilenames()
                            ->directory('files')
                            ->acceptedFileTypes([
                                'image/*',
                                'application/pdf',
                                'text/plain',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->maxSize(10240),
                    ]),
                
                Forms\Components\Section::make('File Associations')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        
                        Forms\Components\Select::make('association_type')
                            ->label('Linked To')
                            ->options([
                                'project' => 'Project',
                                'message' => 'Message',
                                'milestone' => 'Milestone',
                                'verification' => 'Verification',
                            ])
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('project_id', null))
                            ->required(),
                        
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'name')
                            ->visible(fn (Forms\Get $get) => $get('association_type') === 'project'),
                        
                        Forms\Components\Select::make('message_id')
                            ->relationship('message', 'id')
                            ->visible(fn (Forms\Get $get) => $get('association_type') === 'message'),
                        
                        Forms\Components\Select::make('milestone_id')
                            ->relationship('milestone', 'name')
                            ->visible(fn (Forms\Get $get) => $get('association_type') === 'milestone'),
                        
                        Forms\Components\Select::make('verification_id')
                            ->relationship('verification', 'id')
                            ->visible(fn (Forms\Get $get) => $get('association_type') === 'verification'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('url')
                    ->label('Preview')
                    ->size(40)
                    ->circular()
                    ->getStateUsing(fn ($record) => Str::contains($record->url, ['png', 'jpg', 'jpeg']) ? $record->url : null),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('association_type')
                    ->label('Linked To')
                    ->colors([
                        'primary' => 'project',
                        'success' => 'milestone',
                        'warning' => 'message',
                        'danger' => 'verification',
                    ])
                    ->getStateUsing(fn ($record) => $record->association_type),
                
                Tables\Columns\TextColumn::make('linkedEntity.name')
                    ->label('Entity Name')
                    ->getStateUsing(function ($record) {
                        return match($record->association_type) {
                            'project' => $record->project->name ?? 'N/A',
                            'message' => 'Message #'.$record->message_id,
                            'milestone' => $record->milestone->name ?? 'N/A',
                            'verification' => 'Verification #'.$record->verification_id,
                            default => 'N/A',
                        };
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('association_type')
                    ->options([
                        'project' => 'Project',
                        'message' => 'Message',
                        'milestone' => 'Milestone',
                        'verification' => 'Verification',
                    ]),
                
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name'),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn ($record) => response()->download(storage_path('app/'.$record->url))),
                
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('change_owner')
                        ->icon('heroicon-o-user')
                        ->form([
                            Forms\Components\Select::make('user_id')
                                ->relationship('user', 'name')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update($data);
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'project', 'milestone']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}