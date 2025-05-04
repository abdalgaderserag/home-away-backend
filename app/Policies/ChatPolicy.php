<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ChatPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('super access');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Chat $chat): bool
    {
        $first = $chat->firstUser;
        $second = $chat->secondUser;
        if ($first->id === $user->id || $second->id === $user->id) {
            return true;
        }
        return $user->hasOpenTicket($first) || $user->hasOpenTicket($second);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Chat $chat): bool
    {
        $first = $chat->firstUser;
        $second = $chat->secondUser;
        if ($first->id === $user->id || $second->id === $user->id) {
            return true;
        }
        return $user->hasOpenTicket($first) || $user->hasOpenTicket($second);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Chat $chat): bool
    {
        $first = $chat->firstUser;
        $second = $chat->secondUser;
        return $user->hasOpenTicket($first) || $user->hasOpenTicket($second);
    }
}
