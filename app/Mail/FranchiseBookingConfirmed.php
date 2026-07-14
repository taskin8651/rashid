<?php

namespace App\Mail;

use App\Models\FranchiseBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FranchiseBookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FranchiseBooking $booking)
    {
    }

    public function build()
    {
        return $this->subject('Welcome to R-Tech Computer Franchise — ' . $this->booking->city)
            ->markdown('emails.franchise-booking-confirmed', ['booking' => $this->booking]);
    }
}
