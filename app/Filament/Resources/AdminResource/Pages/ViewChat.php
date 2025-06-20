<?php

namespace App\Filament\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\ChatResource; // Import the ChatResource

class ViewChat extends ViewRecord
{
    protected static string $resource = ChatResource::class;

    protected static string $view = 'filament.pages.view-chat';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $slug = 'chats/{record}/view';

    protected function afterMount(): void
    {
        // dd('Chat record loaded:', $this->record->id);
    }

    protected function getHeaderWidgets(): array
    {
        return [
        ];
    }

    public function getHeading(): string
    {
        return 'Chat: ' . ($this->record ? ($this->record->name ?? 'ID ' . $this->record->id) : 'Loading...');
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }
}