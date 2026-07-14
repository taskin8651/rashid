@component('mail::message')
# Franchise Application Update

Hi {{ $booking->name }},

Your R-Tech Computer franchise application for **{{ $booking->city }}** has moved to a new stage:

@component('mail::table')
| | |
|:---|---:|
| Current Stage | {{ $booking->stageLabel() }} |
| Booking No. | {{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }} |
@endcomponent

@component('mail::button', ['url' => route('franchise.dashboard')])
View Your Dashboard
@endcomponent

We'll keep you posted as things progress.

{{ config('app.name') }}
@endcomponent
