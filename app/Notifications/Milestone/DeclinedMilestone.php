<?php

namespace App\Notifications\Milestone;

use App\Models\Milestone;
use App\Notifications\NotificationMain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DeclinedMilestone extends NotificationMain implements ShouldQueue
{
    use Queueable;

    public function __construct(private Milestone $milestone) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.milestone_declined_subject'))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.milestone_declined', [
                'project_title' => $this->milestone->project->title
            ]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.milestone_declined", [
                "project_title" => $this->milestone->project->title
            ]),
            "type" => "milestone_declined",
            "milestone_id" => $this->milestone->id,
            "project_id" => $this->milestone->project_id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}