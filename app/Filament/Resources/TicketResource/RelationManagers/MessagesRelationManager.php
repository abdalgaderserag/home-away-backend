<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    public function form(Form $form): Form
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
                    ->label('New Message')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('user.name')
                            ->weight('bold')
                            ->color(fn($record) => $record->user->is_agent ? 'primary' : 'gray'),

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
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Send Message')
                    ->icon('heroicon-o-paper-airplane')
                    ->modalWidth('3xl'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'asc');
    }

    protected function getTableContentGrid(): ?array
    {
        return [
            'md' => 1,
            'xl' => 1,
        ];
    }
}
