<?php

namespace App\Policies;

use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttachmentPolicy
{

    private function hasAccess(User $user, Attachment $attachment)
    {
        if ($attachment->owner_id == $user->id) {
            # code...
        }
        if ($attachment->user_id) {
            return true;
        }

        if ($attachment->message->sender_id === $user->id || $attachment->message->receiver_id === $user->id) {
            return true;
        }

        if ($attachment->project->client_id == $user->id || $attachment->message->designer_id === $user->id) {
            return true;
        }

        return (
            $user->hasOpenTicket($attachment->user_id)
            || $user->hasOpenTicket($attachment->message->sender_id)
            || $user->hasOpenTicket($attachment->project->client_id)
            || $user->hasOpenTicket($attachment->verification->user_id)
        );
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Attachment $attachment): bool
    {
        return $this->hasAccess($user, $attachment);
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
    public function update(User $user, Attachment $attachment): bool
    {
        return $attachment->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Attachment $attachment): bool
    {
        return $attachment->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Attachment $attachment): bool
    {
        return $attachment->owner_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Attachment $attachment): bool
    {
        return $attachment->owner_id === $user->id;
    }
}
