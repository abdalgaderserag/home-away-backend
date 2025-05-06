<?php

namespace App\Policies;

use App\Enum\User\UserType;
use App\Models\User;
use Google\Service\Texttospeech\Turn;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
    public function view(User $user, User $model): bool
    {
        if ($user->type === UserType::Client || $user->type === UserType::Designer) {
            return true;
        }
        return false;
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
    public function update(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }
        return $user->hasOpenTicket($model) || $user->hasPermissionTo('edit users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }
        return $user->hasOpenTicket($model) || $user->hasPermissionTo('edit users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }
        return false;
    }
}
