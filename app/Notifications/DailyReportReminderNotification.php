<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DailyReportReminderNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-journal-text',
            'title' => 'Daily Report Reminder',
            'body' => "You haven't submitted today's work report yet.",
            'url' => route($notifiable->hasRole('teacher') ? 'teacher.reports.create' : 'staff.reports.create'),
        ];
    }
}
