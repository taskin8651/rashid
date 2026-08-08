<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JobApplicationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(protected JobApplication $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-file-earmark-person-fill',
            'title' => 'New Job Application',
            'body' => $this->application->name . ' applied for ' . $this->application->jobPosting->title . '.',
            'url' => route('admin.job-applications.index', ['job' => $this->application->job_posting_id]),
        ];
    }
}
