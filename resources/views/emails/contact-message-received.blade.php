@component('mail::message')
# New Contact Message

@component('mail::table')
| | |
|:---|:---|
| Name | {{ $message->name }} |
| Email | {{ $message->email }} |
| Phone | {{ $message->phone ?? '—' }} |
| Interested In | {{ $message->interested_course ?? '—' }} |
@endcomponent

**Message:**

{{ $message->message ?? '—' }}

{{ config('app.name') }}
@endcomponent
