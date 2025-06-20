<?php

namespace App\Filament\Resources;

use App\Enum\Project\Status;
use App\Enum\Project\UnitType;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\Resources\ProjectResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\Traits\PermissionsTrait;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    use PermissionsTrait;
    protected static function getPermissionType(): string
    {
        return 'edit projects';
    }
    
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationGroup = 'Users Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('designer_id')
                    ->relationship('designer', 'name')
                    ->searchable()
                    ->default(null),
                Forms\Components\Select::make('status')
                    ->enum(Status::class)
                    ->options(Status::class)
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('space')
                    ->numeric()
                    ->default(null),
                Forms\Components\Select::make('location_id')
                    ->relationship('location', 'city')
                    ->searchable()
                    ->required(),
                Forms\Components\DateTimePicker::make('deadline'),
                Forms\Components\TextInput::make('min_price')
                    ->numeric()
                    ->prefix("$")
                    ->default(null),
                Forms\Components\TextInput::make('max_price')
                    ->numeric()
                    ->prefix("$")
                    ->default(null),
                Forms\Components\Select::make('skill_id')
                    ->relationship('skill', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('unit_type_id')
                    ->relationship('unit', 'type')
                    ->searchable()
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\Toggle::make('resources')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('designer.name')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location.city')
                    ->sortable(),
                Tables\Columns\TextColumn::make('skill.name'),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime('F j, Y')
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
                Tables\Actions\EditAction::make(),
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
            // Add the new AttachmentsRelationManager here
            AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
