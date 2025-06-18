<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\Project\ProjectSentForApproval;
use Illuminate\Support\Facades\Auth;

trait TicketInitTrait
{
    private function projectApprovalTicket(User $client, Project $project)
    {
        $category = Category::where('slug', 'project-approval')->first();
        $ticket = $client->tickets()->create([
            'title' => $project->title,
            'model_id' => $project->id,
            'category_id' => $category->id,
            'status' => 'open',
            'priority' => 'low',
        ]);
        $client->notify(new ProjectSentForApproval($project));
    }

    private function createVerificationTicket(Verification $verification)
    {
        $user = Auth::user();
        $type= $verification->type;
        $category = Category::where('slug', "{$type}-verification")->first();

        $user->tickets()->create([
            'title' => "{$verification->type} Verification request",
            'model_id' => $verification->id,
            'category_id' => $category->id,
            'status' => 'open',
            'priority' => 'medium',
        ]);
    }
}
