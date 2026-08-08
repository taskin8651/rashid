<?php

namespace App\Notifications;

use App\Models\Placement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlacementApprovedNotification extends Notification
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
            'icon' => 'bi-patch-check-fill',
            'title' => 'Placement Approved',
            'body' => 'Your placement at ' . $this->placement->company_name . ' as ' . $this->placement->job_title . ' is now live on the website. Congratulations!',
            'url' => route('placements'),
        ];
    }
}
