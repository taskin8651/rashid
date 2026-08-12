@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('franchisee') ? 'layouts.franchise' : 'layouts.student'))

@section('title', 'Certificate Applications')

@section('content')
  @php
    $approvedCount = $applications->where('status', 'approved')->count();
    $pendingCount = $applications->where('status', 'pending')->count();
  @endphp

  <div class="cert-hero">
    <div class="cert-hero-top">
      <div>
        <div class="cert-hero-badge"><i class="bi bi-patch-question-fill"></i>Offline Training</div>
        <div class="d-flex align-items-center gap-3">
          <div class="cert-hero-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
          <div>
            <h4>My Certificate Applications</h4>
            <p>Applications submitted for offline training certificates</p>
          </div>
        </div>
      </div>
      <a href="{{ route('certificate-applications.create') }}" class="cert-btn primary"><i class="bi bi-plus-lg"></i>New Application</a>
    </div>
    <div class="cert-hero-stats">
      <div class="cert-hero-stat"><b>{{ $approvedCount }}</b><span>Approved</span></div>
      <div class="cert-hero-stat"><b>{{ $pendingCount }}</b><span>Pending Review</span></div>
      <div class="cert-hero-stat"><b>{{ $applications->count() }}</b><span>Total Applications</span></div>
    </div>
  </div>

  <div class="cert-card">
    <div class="cert-card-head"><i class="bi bi-list-check"></i><h6>Applications</h6><span>{{ $applications->count() }} total</span></div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Course</th><th>Completion Date</th><th>Applied On</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($applications as $app)
          <tr>
            <td>{{ $app->course->name }}</td>
            <td>{{ $app->completion_date->format('d M Y') }}</td>
            <td>{{ $app->created_at->format('d M Y') }}</td>
            <td>
              @if ($app->status === 'approved')
                <span class="badge-cert-issued"><i class="bi bi-patch-check-fill"></i>Approved</span>
              @elseif ($app->status === 'pending')
                <span class="badge-rt bg-pending">Pending</span>
              @else
                <span class="badge-rt bg-failed">Rejected</span>
              @endif
              @if ($app->status === 'rejected' && $app->admin_notes)
                <div style="font-size:11px;color:var(--muted);margin-top:4px">{{ $app->admin_notes }}</div>
              @endif
            </td>
            <td>
              @if ($app->status === 'approved' && $app->certificate)
                <a href="{{ route('student.certificates.view', $app->certificate) }}" class="cert-icon-btn navy" title="View Certificate" target="_blank"><i class="bi bi-eye-fill"></i></a>
                <a href="{{ route('student.certificates.download', $app->certificate) }}" class="cert-icon-btn gold" title="Download Certificate"><i class="bi bi-download"></i></a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="padding:0">
            <div class="cert-empty">
              <i class="bi bi-file-earmark-text"></i>
              <b>No applications yet</b>
              <span>Apply for your official completion certificate. <a href="{{ route('certificate-applications.create') }}" style="color:var(--orange)">Apply now →</a></span>
            </div>
          </td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
