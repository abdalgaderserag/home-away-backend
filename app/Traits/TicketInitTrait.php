<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use App\Notifications\Project\ProjectSentForApproval;

trait TicketInitTrait
{
    public function projectApprovalTicket(User $client, $title, Project $project)
    {
        $category = Category::where('slug', 'project-approval')->first();
        $ticket = $client->tickets()->create([
            'title' => $title,
            'model_id' => $project->id,
            'category_id' => $category->id,
            'status' => 'open',
            'priority' => 'low',
        ]);
        $client->notify(new ProjectSentForApproval($project));
    }
}
