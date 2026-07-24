@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('franchisee') ? 'layouts.franchise' : 'layouts.student'))

@section('title', 'Certificate Applications')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>My Certificate Applications</h4><p>Applications submitted for offline training certificates</p></div>
    <a href="{{ route('certificate-applications.create') }}" class="bsave" style="text-decoration:none"><i class="bi bi-plus-lg me-1"></i>New Application</a>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Course</th><th>Completion Date</th><th>Applied On</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($applications as $app)
          <tr>
            <td>{{ $app->course->name }}</td>
            <td>{{ $app->completion_date->format('d M Y') }}</td>
            <td>{{ $app->created_at->format('d M Y') }}</td>
            <td>
              <span class="badge-rt {{ $app->status === 'approved' ? 'bg-active' : ($app->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($app->status) }}</span>
              @if ($app->status === 'rejected' && $app->admin_notes)
                <div style="font-size:11px;color:var(--muted);margin-top:4px">{{ $app->admin_notes }}</div>
              @endif
            </td>
            <td>
              @if ($app->status === 'approved' && $app->certificate)
                <a href="{{ route('student.certificates.download', $app->certificate) }}" class="action-btn" title="Download Certificate"><i class="bi bi-download"></i></a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:var(--muted)">No applications yet. <a href="{{ route('certificate-applications.create') }}" style="color:var(--orange)">Apply for a certificate →</a></td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
