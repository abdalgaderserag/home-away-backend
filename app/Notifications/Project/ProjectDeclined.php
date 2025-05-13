<?php

namespace App\Notifications\Project;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeclined extends Notification implements ShouldQueue
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
            ->subject(__('notification.project_declined'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.project_declined'))
            ->action(__('notification.contact_support'), route('support.create'))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.project_declined"),
            "type" => "project_declined",
            "project_id" => $this->project->id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}