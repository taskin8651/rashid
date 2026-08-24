@extends('layouts.teacher')

@section('title', 'Daily Reports')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-journal-text"></i>Daily Reports</div>
    <h4>My Daily Work Reports</h4>
    <p>Log what you worked on each day. Your admin reviews every report.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['this_month'] }}</b><span>This Month</span></div>
      <div><b>{{ $stats['pending'] }}</b><span>Pending Review</span></div>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success mb-3" style="font-size:13px">{{ session('status') }}</div>
  @endif

  <a class="bsave mb-4 d-inline-block" href="{{ route('teacher.reports.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Today's Report</a>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Date</th><th>Task / Subject</th><th>Hours</th><th>Status</th><th>Review</th><th></th></tr></thead>
      <tbody>
        @forelse ($reports as $r)
          <tr>
            <td>{{ $r->report_date->format('d M Y') }}</td>
            <td>{{ $r->task_subject ?? '—' }}</td>
            <td>{{ $r->hours_worked ?? '—' }}</td>
            <td><span class="badge-rt {{ $r->status === 'present' ? 'bg-active' : ($r->status === 'leave' ? 'bg-pending' : 'bg-inactive') }}">{{ ucfirst($r->status) }}</span></td>
            <td>
              @if ($r->review_status === 'approved')
                <span class="badge-rt bg-active">Approved</span>
              @elseif ($r->review_status === 'rejected')
                <span class="badge-rt" style="background:rgba(239,68,68,.14);color:#ef4444;border:1px solid rgba(239,68,68,.3)" title="{{ $r->admin_comment }}">Rejected</span>
              @else
                <span class="badge-rt bg-pending">Pending</span>
              @endif
            </td>
            <td>
              @if ($r->isEditable())
                <a class="bghost" style="font-size:12px;padding:6px 10px" href="{{ route('teacher.reports.edit', $r) }}"><i class="bi bi-pencil-fill"></i></a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">No reports submitted yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $reports->links() }}</div>
@endsection
