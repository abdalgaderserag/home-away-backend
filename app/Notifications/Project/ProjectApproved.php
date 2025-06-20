<?php

namespace App\Notifications\Project;

use App\Models\Project;
use App\Notifications\NotificationMain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectApproved extends NotificationMain implements ShouldQueue
{
    use Queueable;

    public function __construct(private Project $project) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.project_approved'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.project_approved'))
            ->action(__('notification.view_project'), route('projects.show', $this->project))
            ->line(__('notification.thank_you'));
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable)
    {
        $app_name = config('app.name');
        $project_title = $this->project->title;

        return [
            'phone' => $notifiable->phone,
            'message' => "Great news! Your project '{$project_title}' has been approved on {$app_name}. Check your dashboard for details."
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.project_approved"),
            "type" => "project_approved",
            "project_id" => $this->project->id,
            "project_title" => $this->project->title,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}