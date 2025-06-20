<?php

namespace App\Filament\Resources;

use App\Filament\Pages\ViewChat;
use App\Filament\Resources\ChatResource\Pages;
use App\Models\Chat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChatResource extends Resource
{
    protected static ?string $model = Chat::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    protected static ?string $navigationGroup = 'Support';
    protected static ?int $navigationSort = 4;
    protected static ?string $label = 'Messages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('first_user_id')
                    ->relationship('firstUser', 'name') // Display user's name
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('second_user_id')
                    ->relationship('secondUser', 'name')
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('last_message_id')
                    ->relationship('lastMessage', 'content')
                    ->nullable(),
                Forms\Components\Toggle::make('is_read')
                    ->label('Is Read?')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_id')
                    ->label('Ticket ID')
                    ->sortable()
                    ->action(
                        Tables\Actions\Action::make('view_ticket')
                            ->label('View Ticket')
                            ->icon('heroicon-o-ticket')
                            ->url(fn(Chat $record): string => '/admin/tickets/' . $record->ticket_id)
                            ->openUrlInNewTab()
                            ->visible(fn(Chat $record): bool => !empty($record->ticket_id) && class_exists(\App\Filament\Resources\TicketResource::class)), // Only show if TicketResource exists
                    ),
                Tables\Columns\TextColumn::make('firstUser.name')
                    ->label('First User')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastMessage.content')
                    ->label('Last Message Content')
                    ->limit(50)
                    ->tooltip(fn(Chat $record): ?string => $record->lastMessage?->content)
                    ->placeholder('No messages yet')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read Status')
                    ->boolean()
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
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->placeholder('All Chats')
                    ->trueLabel('Read Chats')
                    ->falseLabel('Unread Chats'),
                Tables\Filters\SelectFilter::make('first_user_id')
                    ->relationship('firstUser', 'name')
                    ->label('Filter by First User'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_chat_detail')
                    ->label('Open Chat')
                    ->icon('heroicon-o-arrow-right')
                    ->url(fn(Chat $record): string => ViewChat::getUrl(['record' => $record->id])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListChats::route('/'),
            'create' => Pages\CreateChat::route('/create'),
            // 'edit' => Pages\EditChat::route('/{record}/edit'),
            'view-chat' => ViewChat::route('/{record}/view'),
        ];
    }
}
