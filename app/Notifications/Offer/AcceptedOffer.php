<?php

namespace App\Notifications\Offer;

use App\Models\Offer;
use App\Models\Project;
use App\Notifications\NotificationMain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AcceptedOffer extends NotificationMain implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Offer $offer,
        private Project $project
    ) {}

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notification.offer_accepted', ['project_title' => $this->project->title]))
            ->greeting(__('notification.hello', ['name' => $notifiable->name]))
            ->line(__('notification.offer_accepted', ['project_title' => $this->project->title]))
            ->line(__('notification.thank_you'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            "message" => __("notification.offer_accepted", ["project_title" => $this->project->title]),
            "type" => "offer_accepted",
            "project_id" => $this->project->id,
            "offer_id" => $this->offer->id,
            "client_id" => $this->project->client_id,
            "timestamp" => now()->toDateTimeString(),
        ];
    }
}