<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentGradedNotification extends Notification
{
    use Queueable;

    public function __construct(protected AssignmentSubmission $submission)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-clipboard-check-fill',
            'title' => 'Assignment Graded',
            'body' => '"' . $this->submission->assignment->title . '" scored ' . $this->submission->score . '/' . $this->submission->assignment->max_score . '.',
            'url' => route('student.courses.assignments.index', $this->submission->assignment->course_id),
        ];
    }
}
