@extends('layouts.site')

@section('title', 'Careers')

@section('content')
  <section class="sec" style="padding-bottom:0">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Careers</div>
        <h2 class="sec-h">Open <em>Job Openings</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">Roles hiring partners have shared with us for our students &mdash; apply directly with your resume.</p>
      </div>
    </div>
  </section>

  <section class="sec" style="padding-top:0">
    <div class="container" style="max-width:820px">
      @if ($jobs->isEmpty())
        <div class="text-center" style="padding:60px 24px">
          <i class="bi bi-briefcase" style="font-size:32px;color:var(--muted);margin-bottom:12px;display:inline-block"></i>
          <p style="font-size:14px;color:var(--muted)">No openings right now. Check back soon!</p>
        </div>
      @else
        <div class="d-flex flex-column gap-3">
          @foreach ($jobs as $job)
            <a href="{{ route('careers.show', $job) }}" class="cbox rv" style="display:block;text-decoration:none;color:inherit">
              <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                  <div style="font-size:16px;font-weight:700;color:var(--text)">{{ $job->title }}</div>
                  <div style="font-size:13px;color:var(--orange);font-weight:600;margin-top:2px">{{ $job->company_name }}</div>
                </div>
                @if ($job->package)
                  <div style="font-size:13px;color:var(--muted);white-space:nowrap"><i class="bi bi-cash-stack me-1"></i>{{ $job->package }}</div>
                @endif
              </div>
              <div class="d-flex flex-wrap gap-2 mt-3" style="font-size:11px;color:var(--muted)">
                @if ($job->job_type)<span><i class="bi bi-briefcase-fill me-1"></i>{{ $job->job_type }}</span>@endif
                @if ($job->work_mode)<span><i class="bi bi-laptop me-1"></i>{{ $job->work_mode }}</span>@endif
                @if ($job->location)<span><i class="bi bi-geo-alt-fill me-1"></i>{{ $job->location }}</span>@endif
                @if ($job->apply_by)<span><i class="bi bi-calendar-event me-1"></i>Apply by {{ $job->apply_by->format('d M Y') }}</span>@endif
              </div>
            </a>
          @endforeach
        </div>
      @endif
    </div>
  </section>
@endsection
