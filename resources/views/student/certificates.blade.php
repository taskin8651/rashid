@extends('layouts.student')

@section('title', 'Certificates')

@section('content')
  @php
    $earnedCount = $rows->where('certificate.status', 'issued')->count();
  @endphp

  <div class="cert-hero">
    <div class="cert-hero-top">
      <div>
        <div class="cert-hero-badge"><i class="bi bi-mortarboard-fill"></i>Your Achievements</div>
        <div class="d-flex align-items-center gap-3">
          <div class="cert-hero-icon"><i class="bi bi-award-fill"></i></div>
          <div>
            <h4>Certificates</h4>
            <p>Earned on completing 100% of a course's videos</p>
          </div>
        </div>
      </div>
      <a href="{{ route('certificate-applications.status') }}" class="cert-btn ghost"><i class="bi bi-patch-question-fill"></i>Trained Offline? Apply Here</a>
    </div>
    <div class="cert-hero-stats">
      <div class="cert-hero-stat"><b>{{ $earnedCount }}</b><span>Certificates Earned</span></div>
      <div class="cert-hero-stat"><b>{{ $rows->count() }}</b><span>Enrolled Courses</span></div>
    </div>
  </div>

  <div class="row g-4">
    @forelse ($rows as $row)
      <div class="col-md-6">
        @if ($row['certificate']->status === 'issued')
          <div class="cert cert-premium">
            <div class="cert-corner-ribbon"><span>ISSUED</span></div>
            <div class="cert-seal"><i class="bi bi-award-fill"></i></div>
            <div class="cert-t">Certificate of Completion</div>
            <div class="cert-n">{{ $row['course']->name }}</div>
            <div class="cert-b">
              Issued to <strong style="color:#fff">{{ auth()->user()->name }}</strong><br>
              on {{ $row['certificate']->issued_date->format('d M Y') }}
            </div>
            <div class="cert-code-chip"><i class="bi bi-upc-scan"></i>{{ $row['certificate']->cert_code }}</div>
            <div class="d-flex gap-2 flex-wrap justify-content-center" style="margin-top:20px">
              @if ($row['certificate']->include_certificate)
                <a class="cert-btn primary" href="{{ route('student.certificates.view', $row['certificate']) }}" target="_blank"><i class="bi bi-eye-fill"></i>View</a>
                <a class="cert-btn ghost" href="{{ route('student.certificates.download', $row['certificate']) }}"><i class="bi bi-download"></i>Download PDF</a>
              @endif
              @if ($row['certificate']->include_marksheet && $row['certificate']->hasMarksheetData())
                <a class="cert-btn ghost" href="{{ route('student.certificates.marksheet.view', $row['certificate']) }}" target="_blank"><i class="bi bi-eye-fill"></i>View Marksheet</a>
                <a class="cert-btn ghost" href="{{ route('student.certificates.marksheet', $row['certificate']) }}"><i class="bi bi-file-earmark-text-fill"></i>Marksheet</a>
              @endif
              <a class="cert-btn ghost" href="{{ route('certificates.verify', ['code' => $row['certificate']->cert_code]) }}" target="_blank"><i class="bi bi-patch-check-fill"></i>Verify</a>
            </div>
          </div>
        @else
          <div class="mcc" style="padding:24px;height:100%">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="cert-locked-icon"><i class="bi bi-lock-fill"></i></div>
              <div>
                <h6 style="font-size:15px;font-weight:700;margin:0">{{ $row['course']->name }}</h6>
                <div style="font-size:11px;color:var(--muted)">Certificate locks until 100% complete</div>
              </div>
            </div>
            <p style="font-size:12px;color:var(--muted);margin:4px 0 8px">{{ $row['percent'] }}% Completed</p>
            <div class="ptrack"><div class="pfill" style="width:{{ $row['percent'] }}%"></div></div>
            <a href="{{ route('student.courses.index') }}" style="display:inline-block;margin-top:16px;font-size:12px;font-weight:700;color:var(--orange);text-decoration:none">Continue Course &rarr;</a>
          </div>
        @endif
      </div>
    @empty
      <div class="col-12">
        <div class="cert-empty">
          <i class="bi bi-award"></i>
          <b>No courses enrolled yet</b>
          <span>Enroll in a course to start earning certificates. <a href="{{ route('courses') }}" style="color:var(--orange)">Browse courses →</a></span>
        </div>
      </div>
    @endforelse
  </div>
@endsection
