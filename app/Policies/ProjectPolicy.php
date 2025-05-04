<?php

namespace App\Policies;

use App\Enum\Project\Status;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
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
    public function view(User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        $client = $project->client;
        $designer = $project->designer;
        if ($user->id === $project->client_id && ($project->status === Status::Draft->value)) {
            return true;
        }
        return $user->hasOpenTicket($client) ||
            $user->hasOpenTicket($designer);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        $client = $project->client;
        $designer = $project->designer;
        if ($user->id === $client->id && $project->status === Status::Draft->value) {
            return true;
        }
        return $user->hasOpenTicket($client) ||
            $user->hasOpenTicket($designer);
    }
}
