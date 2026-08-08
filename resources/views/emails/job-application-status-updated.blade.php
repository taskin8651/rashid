@component('mail::message')
# Application Update

Hi {{ $application->name }},

Your application for **{{ $application->jobPosting->title }}** at **{{ $application->jobPosting->company_name }}** has been updated:

@component('mail::table')
| | |
|:---|---:|
| Status | {{ ucfirst($application->status) }} |
| Job | {{ $application->jobPosting->title }} |
@endcomponent

@if ($application->status === 'shortlisted')
Congratulations! You've been shortlisted. Our team will reach out to you shortly with next steps.
@elseif ($application->status === 'hired')
Congratulations on your new role! We're excited for you.
@elseif ($application->status === 'rejected')
We appreciate your interest and encourage you to apply for future openings.
@endif

@if ($application->admin_notes)
**Note from our team:** {{ $application->admin_notes }}
@endif

@component('mail::button', ['url' => route('careers')])
View Open Positions
@endcomponent

{{ config('app.name') }}
@endcomponent
