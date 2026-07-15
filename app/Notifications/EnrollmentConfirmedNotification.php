<?php

namespace App\Notifications;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EnrollmentConfirmedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Enrollment $enrollment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-mortarboard-fill',
            'title' => 'Enrollment Confirmed',
            'body' => 'You are now enrolled in "' . $this->enrollment->course->name . '". Start learning!',
            'url' => route('student.courses.learn', $this->enrollment->course_id),
        ];
    }
}
