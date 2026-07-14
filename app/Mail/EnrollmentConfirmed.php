<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enrollment $enrollment)
    {
    }

    public function build()
    {
        return $this->subject('Enrollment Confirmed — ' . $this->enrollment->course->name)
            ->markdown('emails.enrollment-confirmed', ['enrollment' => $this->enrollment]);
    }
}
