<?php

namespace App\Livewire\Admin;

use App\Models\Chat;
use Livewire\Component;

class SupportChatView extends Component
{
    public ?Chat $chat = null;

    public function mount(?Chat $chat = null)
    {
        $this->chat = $chat;

        if (!$this->chat) {
            abort(404, 'Chat not found.');
        }

        if (!auth()->user()->hasPermissionTo('send messages') || auth()->user()->hasPermissionTo('super access')) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.admin.support-chat-view');
    }

    public string $newMessage = '';

    public function sendMessage()
    {
        $this->validate(['newMessage' => 'required|string|max:255']);
        $this->chat->messages()->create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->getUserId(),
            'context' => $this->newMessage,
        ]);
        $this->newMessage = '';
        $this->dispatch('chat-updated');
    }

    private function getUserId(){
        return $this->first_user_id == auth()->id()?$this->second_user_id:$this->first_user_id;
    }
}
