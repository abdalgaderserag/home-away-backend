<?php

namespace App\Filament\Resources;

use App\Enum\Project\Status;
use App\Enum\VerificationType;
use App\Filament\Resources\TicketResource\Pages;
use App\Models\Category;
use App\Models\Ticket;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Support';


    public static function form(Form $form): Form
    {
        $categorySlugs = Category::pluck('slug', 'id')->toArray();

        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(255),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn() => null),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'open' => 'Open',
                        'closed' => 'Closed',
                        'archived' => 'Archived',
                    ])
                    ->required()
                    ->default('open'),

                Select::make('priority')
                    ->label('Priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                    ])
                    ->required()
                    ->default('medium'),

                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                Section::make('Project Details')
                    ->relationship('project')
                    ->schema([
                        Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->disabled(),
                        Select::make('status')
                            ->options(Status::class)
                            ->enum(Status::class)->disabled(),
                        TextInput::make('title')
                            ->maxLength(255)
                            ->default(null)
                            ->disabled(),
                        Textarea::make('description')
                            ->columnSpanFull()
                            ->disabled(),
                        TextInput::make('space')
                            ->numeric()
                            ->default(null)
                            ->disabled(),
                        Select::make('location_id')
                            ->relationship('location', 'city')
                            ->searchable()
                            ->disabled(),
                        DateTimePicker::make('deadline')
                            ->disabled(),
                        TextInput::make('min_price')
                            ->numeric()
                            ->prefix("$")
                            ->default(null)
                            ->disabled(),
                        TextInput::make('max_price')
                            ->numeric()
                            ->prefix("$")
                            ->default(null)
                            ->disabled(),
                        Select::make('skill_id')
                            ->relationship('skill', 'name')
                            ->searchable()
                            ->disabled(),
                        Select::make('unit_type_id')
                            ->relationship('unit', 'type')
                            ->searchable()
                            ->disabled(),
                        DateTimePicker::make('published_at')
                            ->disabled(),
                        Toggle::make('resources')
                            ->disabled(),

                    ])->visible(function (callable $get) use ($categorySlugs) {
                        $categoryId = $get('category_id');

                        if (!$categoryId) {
                            return false;
                        }

                        return ($categorySlugs[$categoryId] ?? null) === 'project-approval';
                    }),
                Section::make('Verification')
                    ->relationship('verification')->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required(),
                        Select::make('type')
                            ->options(VerificationType::class)
                            ->enum(VerificationType::class)
                            ->disabled(true),
                        Toggle::make('verified')
                            ->required(),
                    ])->visible(function (callable $get) use ($categorySlugs) {
                        $categoryId = $get('category_id');

                        if (!$categoryId)
                            return false;

                        $hide = false;
                        switch ($categorySlugs[$categoryId]) {
                            case 'user-verification':
                            case 'company-verification':
                            case 'address-verification':
                                $hide = true;
                                break;
                        }
                        return $hide;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'open',
                        'danger'  => 'closed',
                        'warning' => 'archived',
                    ])
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger'  => 'high',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::all()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open'     => 'Open',
                        'closed'   => 'Closed',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn(Ticket $record): bool => $record->assigned_to === Auth::id()),

                Tables\Actions\Action::make('assign')
                    ->label('Take Ticket')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->action(fn(Ticket $record) => $record->update(['assigned_to' => Auth::id()]))
                    ->visible(fn(Ticket $record): bool => is_null($record->assigned_to) || $record->assigned_to !== Auth::id()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
