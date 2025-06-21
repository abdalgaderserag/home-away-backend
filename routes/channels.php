<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel for user notifications
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channel for general notifications (optional)
Broadcast::channel('notifications', function ($user) {
    return true; // Anyone can listen to general notifications
});

// Channel for project updates
Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    // Check if user has access to this project
    $project = \App\Models\Project::find($projectId);
    return $project && ($project->client_id === $user->id || $project->designer_id === $user->id);
});

// Channel for chat messages
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // Check if user is part of this chat
    $chat = \App\Models\Chat::find($chatId);
    return $chat && ($chat->user1_id === $user->id || $chat->user2_id === $user->id);
}); 