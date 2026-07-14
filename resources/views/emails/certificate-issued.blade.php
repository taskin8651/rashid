@component('mail::message')
# Congratulations, {{ $certificate->user->name }}! 🏆

You've successfully completed **{{ $certificate->course->name }}** and your certificate is ready.

@component('mail::table')
| | |
|:---|---:|
| Certificate No. | {{ $certificate->cert_code }} |
| Issued On | {{ optional($certificate->issued_date)->format('d M Y') }} |
@endcomponent

@component('mail::button', ['url' => route('student.certificates.download', $certificate)])
Download Certificate
@endcomponent

Great work — keep learning!

{{ config('app.name') }}
@endcomponent
