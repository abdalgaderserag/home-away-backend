<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\User;
use Coderflex\LaravelTicket\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationGroup = 'Ticketing System';
    protected static ?int $navigationSort = 4;
    // protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\RichEditor::make('message')
                    ->required()
                    ->disableToolbarButtons([
                        'attachFiles',
                        'codeBlock',
                        'insertTable',
                    ])
                    ->label('')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('user.name')
                            ->weight('bold')
                            ->color(fn (Message $record) => $record->user->is_agent ? 'primary' : 'gray'),
                        
                        Tables\Columns\TextColumn::make('message')
                            ->html()
                            ->wrap()
                            ->color('gray'),
                    ])->space(1),
                    
                    Tables\Columns\TextColumn::make('created_at')
                        ->since()
                        ->color('gray')
                        ->size('sm'),
                ]),
            ])
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('quickReply')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->form([
                        Forms\Components\RichEditor::make('message')
                            ->required()
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ]),
                    ])
                    ->action(function (Message $record, array $data) {
                        $record->ticket->messages()->create([
                            'user_id' => auth()->id(),
                            'message' => $data['message']
                        ]);
                    }),
            ])
            ->bulkActions([
                //
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'ticket'])
            ->when(!auth()->user()->is_admin, function ($query) {
                $query->whereHas('ticket', function ($q) {
                    $q->where('assigned_to', auth()->id());
                });
            });
    }
}