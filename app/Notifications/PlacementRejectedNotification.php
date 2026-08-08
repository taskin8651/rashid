<?php

namespace App\Notifications;

use App\Models\Placement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PlacementRejectedNotification extends Notification
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
            'icon' => 'bi-x-circle-fill',
            'title' => 'Placement Submission Rejected',
            'body' => 'Your placement submission for "' . $this->placement->company_name . '" was not approved.' . ($this->placement->admin_notes ? ' Reason: ' . $this->placement->admin_notes : ''),
            'url' => route('student.placements.index'),
        ];
    }
}
