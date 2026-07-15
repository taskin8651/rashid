@extends('layouts.site')

@section('title', 'Verify Certificate')

@section('content')
  <section class="sec">
    <div class="container" style="max-width:640px">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Trust &amp; Authenticity</div>
        <h2 class="sec-h">Verify a <em>Certificate</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:480px;margin:10px auto 0">Enter the certificate code printed on any R-Tech Computer certificate to confirm it's genuine.</p>
      </div>

      <div class="cbox rv">
        <form method="GET" action="{{ route('certificates.verify') }}" class="d-flex gap-2 flex-wrap mb-2">
          <input class="inp" style="flex:1;min-width:200px" type="text" name="code" value="{{ $code }}" placeholder="e.g. RTC-26-AB12CD" required>
          <button class="btn-enr" type="submit"><i class="bi bi-search me-1"></i>Verify</button>
        </form>

        @if ($searched)
          @if ($certificate)
            <div class="cert-verify-result ok">
              <div class="cert-verify-icon"><i class="bi bi-patch-check-fill"></i></div>
              <div>
                <div class="cert-verify-title">Certificate Verified</div>
                <div class="cert-verify-row"><span>Student</span><b>{{ $certificate->user->name }}</b></div>
                <div class="cert-verify-row"><span>Course</span><b>{{ $certificate->course->name }}</b></div>
                <div class="cert-verify-row"><span>Issued On</span><b>{{ $certificate->issued_date->format('d M Y') }}</b></div>
                <div class="cert-verify-row"><span>Certificate Code</span><b>{{ $certificate->cert_code }}</b></div>
              </div>
            </div>
          @else
            <div class="cert-verify-result fail">
              <div class="cert-verify-icon"><i class="bi bi-x-circle-fill"></i></div>
              <div>
                <div class="cert-verify-title">Certificate Not Found</div>
                <p style="font-size:13px;color:var(--muted);margin:4px 0 0">We couldn't find an issued certificate with that code. Double-check the code and try again.</p>
              </div>
            </div>
          @endif
        @endif
      </div>
    </div>
  </section>
@endsection
