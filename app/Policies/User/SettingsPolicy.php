<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\User\Settings;
use Illuminate\Auth\Access\Response;

class SettingsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('edit settings');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Settings $settings): bool
    {
        $u = $settings->user;
        return $user->id === $settings->user_id ||
            $user->hasPermissionTo('edit settings') ||
            $user->hasOpenTicket($u);
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
    public function update(User $user, Settings $settings): bool
    {
        return $user->id === $settings->user_id || $user->hasPermissionTo('edit settings');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Settings $settings): bool
    {
        return $user->hasPermissionTo('edit settings');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Settings $settings): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Settings $settings): bool
    {
        return false;
    }
}
