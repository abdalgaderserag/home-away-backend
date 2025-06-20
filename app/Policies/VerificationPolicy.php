<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Auth\Access\Response;

class VerificationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('super access') || $user->hasPermissionTo('verify edit');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Verification $verification): bool
    {
        $u = $verification->user;
        return $user->hasOpenTicket($u) || $user->hasPermissionTo('verify edit');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Verification $verification): bool
    {
        $u = $verification->user;
        return $user->hasOpenTicket($u) || $user->hasPermissionTo('verify edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Verification $verification): bool
    {
        $u = $verification->user;
        return $user->hasOpenTicket($u) || $user->hasPermissionTo('verify edit');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Verification $verification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Verification $verification): bool
    {
        return false;
    }
}
