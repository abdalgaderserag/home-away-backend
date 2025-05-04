<?php

namespace App\Policies\User;

use App\Models\User;
use App\Models\User\Bio;
use Illuminate\Auth\Access\Response;

class BioPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('edit bio');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Bio $bio): bool
    {
        $u = $bio->user;
        return $user->id === $bio->user_id ||
            $user->hasPermissionTo('edit bio') ||
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
    public function update(User $user, Bio $bio): bool
    {
        return $user->id === $bio->user_id || $user->hasPermissionTo('edit bio');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Bio $bio): bool
    {
        return $user->hasPermissionTo('edit bio');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Bio $bio): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Bio $bio): bool
    {
        return false;
    }
}
