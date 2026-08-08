<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobApplicationStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public JobApplication $application)
    {
    }

    public function build()
    {
        return $this->subject('Application Update — ' . $this->application->jobPosting->title)
            ->markdown('emails.job-application-status-updated', ['application' => $this->application]);
    }
}
