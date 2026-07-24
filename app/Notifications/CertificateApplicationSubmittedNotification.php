<?php

namespace App\Notifications;

use App\Models\CertificateApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CertificateApplicationSubmittedNotification extends Notification
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
            'icon' => 'bi-patch-question-fill',
            'title' => 'New Certificate Application',
            'body' => $this->application->user->name . ' applied for a certificate — ' . $this->application->course->name . '.',
            'url' => route('admin.certificate-applications.index'),
        ];
    }
}
