@extends('layouts.site')

@section('title', $job->title)

@section('content')
  <section class="sec">
    <div class="container" style="max-width:720px">
      <a href="{{ route('careers') }}" style="font-size:12px;color:var(--muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:16px"><i class="bi bi-arrow-left"></i>Back to Careers</a>

      <div class="cbox rv mb-4">
        <div style="font-size:20px;font-weight:800;color:var(--text)">{{ $job->title }}</div>
        <div style="font-size:14px;color:var(--orange);font-weight:600;margin-top:2px">{{ $job->company_name }}</div>

        <div class="d-flex flex-wrap gap-2 mt-3" style="font-size:12px;color:var(--muted)">
          @if ($job->job_type)<span class="cc-badge bw" style="margin:0"><i class="bi bi-briefcase-fill me-1"></i>{{ $job->job_type }}</span>@endif
          @if ($job->work_mode)<span class="cc-badge bd" style="margin:0"><i class="bi bi-laptop me-1"></i>{{ $job->work_mode }}</span>@endif
        </div>

        <div class="row g-3 mt-1" style="font-size:13px;color:var(--muted)">
          @if ($job->location)<div class="col-6 col-md-4"><i class="bi bi-geo-alt-fill me-1"></i>{{ $job->location }}</div>@endif
          @if ($job->package)<div class="col-6 col-md-4"><i class="bi bi-cash-stack me-1"></i>{{ $job->package }}</div>@endif
          @if ($job->vacancies)<div class="col-6 col-md-4"><i class="bi bi-people-fill me-1"></i>{{ $job->vacancies }} {{ \Illuminate\Support\Str::plural('opening', $job->vacancies) }}</div>@endif
          @if ($job->apply_by)<div class="col-6 col-md-4"><i class="bi bi-calendar-event me-1"></i>Apply by {{ $job->apply_by->format('d M Y') }}</div>@endif
          @if ($job->course)<div class="col-6 col-md-4"><i class="bi bi-mortarboard-fill me-1"></i>{{ $job->course->name }}</div>@endif
        </div>

        <hr style="border-color:var(--border);margin:20px 0">

        <div style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:8px">Job Description</div>
        <p style="font-size:13.5px;color:var(--muted);white-space:pre-line;line-height:1.7">{{ $job->description }}</p>

        @if ($job->requirements)
          <div style="font-size:13px;font-weight:700;color:var(--text);margin:18px 0 8px">Requirements</div>
          <p style="font-size:13.5px;color:var(--muted);white-space:pre-line;line-height:1.7">{{ $job->requirements }}</p>
        @endif
      </div>

      <div class="cbox rv">
        <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:14px">Apply for this Role</div>

        @if (session('status'))
          <div class="cert-verify-result ok mb-3">
            <div class="cert-verify-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div><div class="cert-verify-title">{{ session('status') }}</div></div>
          </div>
        @endif

        <form method="POST" action="{{ route('careers.apply', $job) }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-2 mb-1">
            <div class="col-md-6"><label class="fl">Full Name</label><input class="inp" type="text" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required /></div>
            <div class="col-md-6"><label class="fl">Email</label><input class="inp" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required /></div>
            <div class="col-md-6"><label class="fl">Phone</label><input class="inp" type="text" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required /></div>
            <div class="col-md-6"><label class="fl">Resume (PDF/DOC, max 5MB)</label><input class="inp" type="file" name="resume" accept=".pdf,.doc,.docx" required /></div>
            <div class="col-md-12"><label class="fl">Cover Note (optional)</label><textarea class="inp" name="cover_note" rows="3" maxlength="1000" placeholder="Why are you a good fit for this role?">{{ old('cover_note') }}</textarea></div>
          </div>
          @if ($errors->any())
            <div style="font-size:12px;color:var(--danger);margin-bottom:10px">
              <ul style="margin:0;padding-left:18px">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif
          <button class="btn-enr" type="submit"><i class="bi bi-send-fill me-1"></i>Submit Application</button>
        </form>
      </div>
    </div>
  </section>
@endsection
