<?php

namespace App\Notifications\Request;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Project $project) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.project_approved'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.project_approved'))
            ->action(__('notification.view_project'), route('projects.show', $this->project))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.project_approved"),
            "type" => "project_approved",
            "project_id" => $this->project->id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}