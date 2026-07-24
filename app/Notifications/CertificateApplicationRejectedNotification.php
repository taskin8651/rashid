<?php

namespace App\Notifications;

use App\Models\CertificateApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CertificateApplicationRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(protected CertificateApplication $application)
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
            'title' => 'Certificate Application Rejected',
            'body' => 'Your application for "' . $this->application->course->name . '" was not approved.' . ($this->application->admin_notes ? ' Reason: ' . $this->application->admin_notes : ''),
            'url' => route('student.certificates.index'),
        ];
    }
}
