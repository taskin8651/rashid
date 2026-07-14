<?php

namespace App\Mail;

use App\Models\FranchiseBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FranchiseStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FranchiseBooking $booking)
    {
    }

    public function build()
    {
        return $this->subject('Franchise Application Update — ' . $this->booking->city)
            ->markdown('emails.franchise-status-updated', ['booking' => $this->booking]);
    }
}
