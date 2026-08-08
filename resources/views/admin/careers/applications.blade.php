@extends('layouts.admin')

@section('title', 'Job Applications')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div><h4>Job Applications</h4><p>Applicants for your published job postings</p></div>
    <a href="{{ route('admin.careers.index') }}" class="bghost" style="text-decoration:none"><i class="bi bi-file-earmark-post-fill me-1"></i>Manage Job Postings</a>
  </div>

  <form method="GET" class="d-flex gap-2 mb-3 flex-wrap">
    <select class="fctrl" name="job" style="max-width:280px" onchange="this.form.submit()">
      <option value="">All Job Postings</option>
      @foreach ($jobs as $j)
        <option value="{{ $j->id }}" @selected((string) $jobId === (string) $j->id)>{{ $j->title }}</option>
      @endforeach
    </select>
    <select class="fctrl" name="status" style="max-width:200px" onchange="this.form.submit()">
      <option value="">All Statuses</option>
      @foreach (['pending', 'shortlisted', 'hired', 'rejected'] as $s)
        <option value="{{ $s }}" @selected($status === $s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
  </form>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Applicant</th><th>Job</th><th>Resume</th><th>Applied</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($applications as $app)
          <tr>
            <td>{{ $app->name }}<div style="font-size:11px;color:var(--muted)">{{ $app->email }} &middot; {{ $app->phone }}</div></td>
            <td>{{ optional($app->jobPosting)->title ?: 'Deleted posting' }}</td>
            <td><a href="{{ route('admin.job-applications.resume', $app) }}" class="action-btn" title="Download Resume"><i class="bi bi-file-earmark-arrow-down-fill"></i></a></td>
            <td>{{ $app->created_at->format('d M Y') }}</td>
            <td><span class="badge-rt {{ $app->status === 'shortlisted' || $app->status === 'hired' ? 'bg-active' : ($app->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($app->status) }}</span></td>
            <td>
              <button class="action-btn" style="border:none;background:none" title="Update Status" data-bs-toggle="modal" data-bs-target="#reviewApp{{ $app->id }}"><i class="bi bi-pencil-square"></i></button>
              <form method="POST" action="{{ route('admin.job-applications.destroy', $app) }}" onsubmit="return confirm('Delete this application?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>

          <div class="modal fade" id="reviewApp{{ $app->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">{{ $app->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.job-applications.status', $app) }}">
                  @csrf
                  <div class="modal-body">
                    @if ($app->cover_note)
                      <label class="flbl">Cover Note</label>
                      <p style="font-size:13px;color:var(--muted);background:rgba(var(--text-rgb),.04);border-radius:10px;padding:12px;margin-bottom:14px">{{ $app->cover_note }}</p>
                    @endif
                    <label class="flbl">Status</label>
                    <select class="fctrl mb-3" name="status">
                      @foreach (['pending', 'shortlisted', 'hired', 'rejected'] as $s)
                        <option value="{{ $s }}" @selected($app->status === $s)>{{ ucfirst($s) }}</option>
                      @endforeach
                    </select>
                    <label class="flbl">Note (emailed to applicant)</label>
                    <textarea class="fctrl" name="admin_notes" rows="3">{{ $app->admin_notes }}</textarea>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="bsave">Save &amp; Notify Applicant</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">No applications yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>
@endsection
