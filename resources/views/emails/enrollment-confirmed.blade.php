@component('mail::message')
# You're Enrolled! 🎉

Hi {{ $enrollment->user->name }},

Your enrollment in **{{ $enrollment->course->name }}** is confirmed. Welcome aboard!

@component('mail::table')
| | |
|:---|---:|
| Course | {{ $enrollment->course->name }} |
| Amount Paid | ₹{{ number_format($enrollment->final_amount, 0) }} |
| Enrolled On | {{ optional($enrollment->enrolled_at)->format('d M Y') }} |
@endcomponent

@component('mail::button', ['url' => route('student.courses.learn', $enrollment->course)])
Start Learning
@endcomponent

Thanks for choosing R-Tech Computer.

{{ config('app.name') }}
@endcomponent
