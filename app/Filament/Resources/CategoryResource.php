<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers\TicketsRelationManager;
use Coderflex\LaravelTicket\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Ticketing System';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('CategoryDetails')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basic Info')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($operation, $state, $set) {
                                        if ($operation === 'create' || $operation === 'edit') {
                                            $set('slug', Str::slug($state));
                                        }
                                    })
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Technical Support'),
                                
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->rules(['alpha_dash:ascii'])
                                    ->disabled(fn ($operation) => $operation === 'edit'),
                                
                                Forms\Components\Textarea::make('description')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->columnSpanFull(),
                            ]),
                        
                        Forms\Components\Tabs\Tab::make('Settings')
                            ->schema([
                                Forms\Components\Toggle::make('is_visible')
                                    ->label('Visible to users')
                                    ->default(true)
                                    ->helperText('Hide category from ticket creation if disabled'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->description(fn (Category $record) => Str::limit($record->description, 40)),
                
                Tables\Columns\TextColumn::make('tickets_count')
                    ->counts('tickets')
                    ->sortable()
                    ->label('Tickets')
                    ->numeric()
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean()
                    ->action(fn (Category $record, Tables\Columns\IconColumn $column) => 
                        $record->update(['is_visible' => !$record->is_visible])
                    ),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_visible')
                    ->label('Visibility'),
                
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit Category'),
                
                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete Category')
                    ->modalHeading('Delete Category')
                    ->modalDescription('Are you sure you want to delete this category? Associated tickets will NOT be deleted.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($action, $records) {
                            $records->each->update(['is_visible' => false]);
                        }),
                    
                    Tables\Actions\BulkAction::make('toggleVisibility')
                        ->icon('heroicon-o-eye')
                        ->form([
                            Forms\Components\Toggle::make('is_visible')
                                ->label('Set Visibility')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update($data);
                        }),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->reorderable('sort_order')
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}