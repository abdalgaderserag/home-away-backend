<?php

namespace App\Policies;

use App\Enum\Offer\OfferStatus;
use App\Models\Milestone;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MilestonePolicy
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
    public function view(User $user, Milestone $milestone): bool
    {
        $client = $milestone->offer->project->client;
        $designer = $milestone->offer->project->designer;
        if ($user->id === $client->id || $user->id === $designer->id) {
            return true;
        }
        return $user->hasOpenTicket($client) ||
            $user->hasOpenTicket($designer);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('designer');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Milestone $milestone): bool
    {
        $client = $milestone->offer->project->client;
        $designer = $milestone->offer->project->designer;
        if ($user->id === $designer->id && $milestone->status === OfferStatus::Pending->value) {
            return true;
        }
        return $user->hasOpenTicket($client) ||
            $user->hasOpenTicket($designer);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Milestone $milestone): bool
    {
        $client = $milestone->offer->project->client;
        $designer = $milestone->offer->project->designer;
        if ($user->id === $designer->id && $milestone->status === OfferStatus::Pending->value) {
            return true;
        }
        return $user->hasOpenTicket($client) ||
            $user->hasOpenTicket($designer);
    }
}
