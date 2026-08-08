<?php

namespace App\Notifications;

use App\Models\Placement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlacementSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Placement $placement)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-briefcase-fill',
            'title' => 'New Placement Submission',
            'body' => $this->placement->user->name . ' reported a placement at ' . $this->placement->company_name . ' — awaiting review.',
            'url' => route('admin.placements.index'),
        ];
    }
}
