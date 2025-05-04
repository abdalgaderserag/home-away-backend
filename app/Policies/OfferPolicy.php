<?php

namespace App\Policies;

use App\Enum\Project\Status;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OfferPolicy
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
    public function view(User $user, Offer $offer): bool
    {
        $client = $offer->project->client;
        $designer = $offer->project->designer;
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
    public function update(User $user, Offer $offer): bool
    {
        $client = $offer->project->client;
        $designer = $offer->project->designer;
        if ($user->id === $offer->user_id && ($offer->project->status === Status::Published->value ||
            $offer->project->status === Status::InProgress->value)) {
            return true;
        }
        return $user->hasOpenTicket($client) ||
            $user->hasOpenTicket($designer);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Offer $offer): bool
    {
        if ($user->id === $offer->user_id && $offer->project->status === Status::Published->value) {
            return true;
        }
        return false;
    }
}
