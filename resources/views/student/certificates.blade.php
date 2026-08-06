@extends('layouts.student')

@section('title', 'Certificates')

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Certificates</h4><p>Earned on completing 100% of a course's videos</p></div>
    <a href="{{ route('certificate-applications.status') }}" class="bghost" style="text-decoration:none"><i class="bi bi-patch-question-fill me-1"></i>Trained Offline? Apply Here</a>
  </div>

  <div class="row g-4">
    @forelse ($rows as $row)
      <div class="col-md-6">
        @if ($row['certificate']->status === 'issued')
          <div class="cert">
            <div style="position:absolute;top:16px;right:16px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.3);color:#fff;font-size:10px;font-weight:700;padding:4px 11px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px"><i class="bi bi-patch-check-fill me-1"></i>Issued</div>
            <div style="font-size:34px;margin-bottom:6px">🏆</div>
            <div class="cert-t">Certificate of Completion</div>
            <div class="cert-n">{{ $row['course']->name }}</div>
            <div class="cert-b">
              Issued to <strong style="color:#fff">{{ auth()->user()->name }}</strong><br>
              on {{ $row['certificate']->issued_date->format('d M Y') }} &middot; Cert No. {{ $row['certificate']->cert_code }}
            </div>
            <div class="d-flex gap-2 flex-wrap" style="margin-top:20px">
              @if ($row['certificate']->include_certificate)
                <a class="bcontinue" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;width:auto;padding:11px 24px;margin:0" href="{{ route('student.certificates.download', $row['certificate']) }}"><i class="bi bi-download"></i>Download PDF</a>
              @endif
              @if ($row['certificate']->include_marksheet && $row['certificate']->hasMarksheetData())
                <a class="bcontinue" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;width:auto;padding:11px 24px;margin:0;background:rgba(255,255,255,.12)" href="{{ route('student.certificates.marksheet', $row['certificate']) }}"><i class="bi bi-file-earmark-text-fill"></i>Marksheet</a>
              @endif
              <a class="bcontinue" style="display:inline-flex;align-items:center;justify-content:center;gap:8px;width:auto;padding:11px 24px;margin:0;background:rgba(255,255,255,.12)" href="{{ route('certificates.verify', ['code' => $row['certificate']->cert_code]) }}" target="_blank"><i class="bi bi-patch-check-fill"></i>Verify</a>
            </div>
          </div>
        @else
          <div class="mcc" style="padding:24px;height:100%">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="sicon" style="background:rgba(var(--text-rgb),.06);color:var(--muted)"><i class="bi bi-lock-fill"></i></div>
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
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">You are not enrolled in any course yet. <a href="{{ route('courses') }}" style="color:var(--orange)">Browse courses →</a></p></div>
    @endforelse
  </div>
@endsection
