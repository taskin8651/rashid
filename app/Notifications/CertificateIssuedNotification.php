<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Certificate $certificate)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-award-fill',
            'title' => 'Certificate Issued',
            'body' => 'Your certificate for "' . $this->certificate->course->name . '" is ready to download.',
            'url' => route('student.certificates.index'),
        ];
    }
}
