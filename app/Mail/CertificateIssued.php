<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CertificateIssued extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Certificate $certificate)
    {
    }

    public function build()
    {
        return $this->subject('Your Certificate Is Ready — ' . $this->certificate->course->name)
            ->markdown('emails.certificate-issued', ['certificate' => $this->certificate]);
    }
}
