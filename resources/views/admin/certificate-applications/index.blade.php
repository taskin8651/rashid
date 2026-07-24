@extends('layouts.admin')

@section('title', 'Certificate Applications')

@section('content')
  <div class="shead mb-4"><h4>Certificate Applications</h4><p>Offline students applying for a completion certificate</p></div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.certificate-applications.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All</a>
    <a href="{{ route('admin.certificate-applications.index', ['status' => 'pending']) }}" class="badge-rt {{ $status === 'pending' ? 'bg-pending' : 'bg-inactive' }}" style="text-decoration:none">Pending ({{ $pendingCount }})</a>
    <a href="{{ route('admin.certificate-applications.index', ['status' => 'approved']) }}" class="badge-rt {{ $status === 'approved' ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">Approved</a>
    <a href="{{ route('admin.certificate-applications.index', ['status' => 'rejected']) }}" class="badge-rt {{ $status === 'rejected' ? 'bg-failed' : 'bg-inactive' }}" style="text-decoration:none">Rejected</a>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Applicant</th><th>Course</th><th>Trained At</th><th>Completed On</th><th>Proof</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($applications as $app)
          <tr>
            <td>{{ $app->user->name }}<div style="font-size:11px;color:var(--muted)">{{ $app->user->email }}</div></td>
            <td>{{ $app->course->name }}</td>
            <td>{{ $app->franchiseBooking->city ?? 'R-Tech Direct' }}</td>
            <td>{{ $app->completion_date->format('d M Y') }}</td>
            <td>
              @if ($app->proof_path)
                <a href="{{ route('admin.certificate-applications.proof', $app) }}" class="action-btn" title="View Proof"><i class="bi bi-file-earmark-check-fill"></i></a>
              @else
                —
              @endif
            </td>
            <td><span class="badge-rt {{ $app->status === 'approved' ? 'bg-active' : ($app->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($app->status) }}</span></td>
            <td>
              @if ($app->status === 'pending')
                <button class="action-btn" style="border:none;background:none" title="Approve &amp; Issue Certificate" data-bs-toggle="modal" data-bs-target="#approveApp{{ $app->id }}"><i class="bi bi-check-lg"></i></button>
                <button class="action-btn danger" style="border:none;background:none" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectApp{{ $app->id }}"><i class="bi bi-x-lg"></i></button>
              @elseif ($app->status === 'approved' && $app->certificate)
                <span style="font-size:11px;color:var(--muted)">Cert: {{ $app->certificate->cert_code }}</span>
              @elseif ($app->admin_notes)
                <span style="font-size:11px;color:var(--muted)">{{ $app->admin_notes }}</span>
              @endif
            </td>
          </tr>

          @if ($app->status === 'pending')
            <div class="modal fade" id="approveApp{{ $app->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Approve &amp; Issue Certificate</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST" action="{{ route('admin.certificate-applications.approve', $app) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                      <p style="font-size:13px;color:var(--muted)">Issuing a certificate to <b style="color:var(--text)">{{ $app->user->name }}</b> for <b style="color:var(--text)">{{ $app->course->name }}</b>.</p>
                      <label class="flbl">Upload Signed Certificate PDF (optional)</label>
                      <input class="fctrl" type="file" name="certificate_pdf" accept="application/pdf" />
                      <p style="font-size:11px;color:var(--muted);margin:6px 0 0"><i class="bi bi-info-circle me-1"></i>Leave empty to auto-generate the standard R-Tech certificate instead.</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="bsave">Approve &amp; Issue</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="modal fade" id="rejectApp{{ $app->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Reject Application</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST" action="{{ route('admin.certificate-applications.reject', $app) }}">
                    @csrf
                    <div class="modal-body">
                      <p style="font-size:13px;color:var(--muted)">Rejecting {{ $app->user->name }}'s application for {{ $app->course->name }}.</p>
                      <label class="flbl">Reason (shown to applicant)</label>
                      <textarea class="fctrl" name="admin_notes" rows="3" placeholder="e.g. Could not verify training details"></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="bsave" style="background:var(--danger)">Reject Application</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @endif
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No applications found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>
@endsection
