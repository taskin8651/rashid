@component('mail::message')
# Welcome Aboard, {{ $booking->name }}! 🎉

Your franchise booking for **{{ $booking->city }}** is confirmed and payment has been received.

@component('mail::table')
| | |
|:---|---:|
| Booking No. | {{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }} |
| City | {{ $booking->city }} |
| Amount Paid | ₹{{ number_format($booking->amount, 0) }} |
@endcomponent

Our franchise team will reach out shortly to schedule your discussion call and get things moving.

@component('mail::button', ['url' => route('franchise.dashboard')])
View Your Dashboard
@endcomponent

Welcome to the R-Tech Computer family!

{{ config('app.name') }}
@endcomponent
