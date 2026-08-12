@extends('layouts.franchise')

@section('title', 'Certificates')

@section('content')
  <div class="cert-hero">
    <div class="cert-hero-top">
      <div>
        <div class="cert-hero-badge"><i class="bi bi-patch-check-fill"></i>Certification Center</div>
        <div class="d-flex align-items-center gap-3">
          <div class="cert-hero-icon"><i class="bi bi-award-fill"></i></div>
          <div>
            <h4>Certificates</h4>
            <p>Certificate applications and issued certificates for your students</p>
          </div>
        </div>
      </div>
    </div>
    <div class="cert-hero-stats">
      <div class="cert-hero-stat"><b>{{ number_format($issuedCount) }}</b><span>Issued Certificates</span></div>
      <div class="cert-hero-stat"><b>{{ number_format($pendingCount) }}</b><span>Pending Applications</span></div>
      <div class="cert-hero-stat"><b>{{ number_format($applications->total()) }}</b><span>Total Applications</span></div>
    </div>
  </div>

  <div class="cert-card mb-4">
    <div class="cert-card-head"><i class="bi bi-patch-question-fill"></i><h6>Applications</h6><span>{{ $applications->total() }} total</span></div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Course</th><th>Completion Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse ($applications as $a)
          <tr>
            <td>{{ $a->user->name ?? '—' }}</td>
            <td>{{ $a->course->name ?? '—' }}</td>
            <td>{{ optional($a->completion_date)->format('d M Y') ?? '—' }}</td>
            <td>
              @php $badge = ['pending' => 'bg-pending', 'approved' => 'bg-active', 'rejected' => 'bg-failed'][$a->status] ?? 'bg-inactive'; @endphp
              <span class="badge-rt {{ $badge }}">{{ ucfirst($a->status) }}</span>
              @if ($a->proof_path)
                <a href="{{ route('franchise.certificate-applications.proof', $a) }}" class="cert-icon-btn gold ms-1" title="Download Proof"><i class="bi bi-paperclip"></i></a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="4" style="padding:0">
            <div class="cert-empty">
              <i class="bi bi-patch-question"></i>
              <b>No certificate applications yet</b>
              <span>Applications submitted by your students will appear here.</span>
            </div>
          </td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mb-4">{{ $applications->links() }}</div>

  <div class="cert-card">
    <div class="cert-card-head"><i class="bi bi-award-fill"></i><h6>Issued Certificates</h6><span>{{ $certificates->total() }} total</span></div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Course</th><th>Cert Code</th><th>Status</th><th>Documents</th></tr></thead>
      <tbody>
        @forelse ($certificates as $cert)
          @php
            $initials = collect(explode(' ', trim($cert->user->name ?? 'S')))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('');
          @endphp
          <tr>
            <td>
              <div class="cert-name-cell">
                <div class="cert-avatar">{{ strtoupper($initials ?: 'S') }}</div>
                <div class="nm">{{ $cert->user->name }}</div>
              </div>
            </td>
            <td>{{ $cert->course->name }}</td>
            <td><span style="font-family:'Space Grotesk',sans-serif;font-weight:700;letter-spacing:.3px">{{ $cert->cert_code }}</span></td>
            <td>
              @if ($cert->status === 'issued')
                <span class="badge-cert-issued"><i class="bi bi-patch-check-fill"></i>Issued</span>
              @else
                <span class="badge-rt bg-pending">{{ ucfirst($cert->status) }}</span>
              @endif
            </td>
            <td>
              @if ($cert->status === 'issued')
                @if ($cert->include_certificate)
                  <a href="{{ route('franchise.certificates.view', $cert) }}" class="cert-icon-btn navy" title="View Certificate" target="_blank"><i class="bi bi-eye-fill"></i></a>
                  <a href="{{ route('franchise.certificates.download', $cert) }}" class="cert-icon-btn gold" title="Download Certificate"><i class="bi bi-award-fill"></i></a>
                @endif
                @if ($cert->include_marksheet && $cert->subjects->isNotEmpty())
                  <a href="{{ route('franchise.certificates.marksheet.view', $cert) }}" class="cert-icon-btn navy" title="View Marksheet" target="_blank"><i class="bi bi-eye-fill"></i></a>
                  <a href="{{ route('franchise.certificates.marksheet', $cert) }}" class="cert-icon-btn gold" title="Download Marksheet"><i class="bi bi-file-earmark-text-fill"></i></a>
                @endif
              @else
                <span style="font-size:11px;color:var(--muted)">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="padding:0">
            <div class="cert-empty">
              <i class="bi bi-award"></i>
              <b>No certificates issued yet</b>
              <span>Once an application is approved, the certificate will show up here.</span>
            </div>
          </td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $certificates->links() }}</div>
@endsection
