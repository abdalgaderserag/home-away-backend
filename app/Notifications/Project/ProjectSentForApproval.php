<?php

namespace App\Notifications\Project;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectSentForApproval extends Notification implements ShouldQueue
{
    use Queueable;

    private Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.project_sent_for_approval'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.project_sent_for_approval'))
            ->action(__('notification.view_project'), route('projects.show', $this->project))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.project_sent_for_approval"),
            "type" => "project_sent_for_approval",
            "project_id" => $this->project->id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}
