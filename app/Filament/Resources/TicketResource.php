<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers\MessagesRelationManager;
use App\Models\User;
use Coderflex\LaravelTicket\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('TicketDetails')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Ticket Information')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\RichEditor::make('message')
                                    ->required()
                                    ->columnSpanFull()
                                    ->disableToolbarButtons([
                                        'attachFiles',
                                        'codeBlock',
                                    ]),

                                Forms\Components\Select::make('user_id')
                                    ->label('Created By')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->disabled(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Management')
                            ->schema([
                                Forms\Components\Select::make('priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                        'critical' => 'Critical',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'open' => 'Open',
                                        'in_progress' => 'In Progress',
                                        'on_hold' => 'On Hold',
                                        'closed' => 'Closed',
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\Toggle::make('is_resolved')
                                    ->label('Mark as Resolved')
                                    ->inline(false),

                                Forms\Components\Toggle::make('is_locked')
                                    ->label('Lock Ticket')
                                    ->inline(false)
                                    ->helperText('Prevent further comments'),

                                Forms\Components\Select::make('assigned_to')
                                    ->label('Assign to Agent')
                                    // ->options(User::where('is_agent', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn(Ticket $record) => $record->title),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('priority')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => 'high',
                        'primary' => 'critical',
                    ])
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'closed',
                        'warning' => 'on_hold',
                        'primary' => 'open',
                        'info' => 'in_progress',
                    ])
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_resolved')
                    ->boolean()
                    ->sortable()
                    ->label('Resolved'),

                Tables\Columns\IconColumn::make('is_locked')
                    ->boolean()
                    ->sortable()
                    ->label('Locked'),

                Tables\Columns\TextColumn::make('assignedTo.name')
                    ->label('Assigned Agent')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('priority')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'critical' => 'Critical',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'in_progress' => 'In Progress',
                        'on_hold' => 'On Hold',
                        'closed' => 'Closed',
                    ])
                    ->default('open'),

                Tables\Filters\TernaryFilter::make('is_resolved'),
                Tables\Filters\TernaryFilter::make('is_locked'),

                // Tables\Filters\SelectFilter::make('assigned_to')
                //     ->label('Assigned Agent')
                //     ->options(User::where('is_agent', true)->pluck('name', 'id')),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('message')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->form([
                        Forms\Components\RichEditor::make('message')
                            ->required()
                            ->disableToolbarButtons(['attachFiles', 'codeBlock'])
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $record->messages()->create([
                            'user_id' => auth()->id(),
                            'message' => $data['message']
                        ]);
                    }),

                Tables\Actions\Action::make('close')
                    ->icon('heroicon-o-lock-closed')
                    ->color('success')
                    ->form([
                        Forms\Components\RichEditor::make('message')
                            ->label('Closing Message')
                            ->disableToolbarButtons(['attachFiles', 'codeBlock'])
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $record->update([
                            'status' => 'closed',
                            'is_resolved' => true
                        ]);

                        if (!empty($data['message'])) {
                            $record->messages()->create([
                                'user_id' => auth()->id(),
                                'message' => $data['message']
                            ]);
                        }
                    })
                    ->visible(fn(Ticket $record) => $record->status !== 'closed'),
                Tables\Actions\Action::make('view')
                    ->url(fn(Ticket $record): string => TicketResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-eye'),

                Tables\Actions\Action::make('assignToMe')
                    ->label('Assign to Me')
                    ->icon('heroicon-o-user-plus')
                    ->action(function (Ticket $record) {
                        $record->update(['assigned_to' => auth()->id()]);
                    }),
                // ->visible(fn () => auth()->user()->is_agent),

                Tables\Actions\Action::make('closeTicket')
                    ->label('Close')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Ticket $record) {
                        $record->update(['status' => 'closed', 'is_resolved' => true]);
                    })
                    ->visible(fn(Ticket $record) => $record->status !== 'closed'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->icon('heroicon-o-viewfinder-circle')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options([
                                    'open' => 'Open',
                                    'in_progress' => 'In Progress',
                                    'on_hold' => 'On Hold',
                                    'closed' => 'Closed',
                                ])
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update($data);
                        }),

                    Tables\Actions\BulkAction::make('assignAgents')
                        ->icon('heroicon-o-users')
                        ->form([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Assign to Agent')
                                // ->options(User::where('is_agent', true)->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each->update($data);
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            MessagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', '!=', 'closed')->count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'assignedTo'])
            ->where('status', 'open')
            ->when(!auth()->user()->is_admin, function ($query) {
                $query->where('assigned_to', auth()->id());
            });
    }
}
