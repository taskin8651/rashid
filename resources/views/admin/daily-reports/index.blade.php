@extends('layouts.admin')

@section('title', 'Daily Reports')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Daily Reports</h4><p>Review the work reports submitted by staff and teachers</p></div>
    <a class="bghost" href="{{ route('admin.daily-reports.export', request()->query()) }}"><i class="bi bi-download me-1"></i>Export CSV</a>
  </div>

  @if (session('status'))
    <div class="alert alert-success mb-3" style="font-size:13px">{{ session('status') }}</div>
  @endif

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card-rt"><div class="card-title">Total</div><h3>{{ $stats['total'] }}</h3></div></div>
    <div class="col-6 col-md-3"><div class="card-rt"><div class="card-title">Pending</div><h3>{{ $stats['pending'] }}</h3></div></div>
    <div class="col-6 col-md-3"><div class="card-rt"><div class="card-title">Approved</div><h3>{{ $stats['approved'] }}</h3></div></div>
    <div class="col-6 col-md-3"><div class="card-rt"><div class="card-title">Rejected</div><h3>{{ $stats['rejected'] }}</h3></div></div>
  </div>

  <div class="card-rt mb-4">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-2">
        <label class="flbl">Role</label>
        <select class="fctrl" name="role">
          <option value="">All</option>
          <option value="staff" @selected($role === 'staff')>Staff</option>
          <option value="teacher" @selected($role === 'teacher')>Teacher</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="flbl">Member</label>
        <select class="fctrl" name="user">
          <option value="">All</option>
          @foreach ($members as $m)
            <option value="{{ $m->id }}" @selected((string) $userId === (string) $m->id)>{{ $m->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="flbl">Review Status</label>
        <select class="fctrl" name="review_status">
          <option value="">All</option>
          <option value="pending" @selected($reviewStatus === 'pending')>Pending</option>
          <option value="approved" @selected($reviewStatus === 'approved')>Approved</option>
          <option value="rejected" @selected($reviewStatus === 'rejected')>Rejected</option>
        </select>
      </div>
      <div class="col-md-2"><label class="flbl">From</label><input type="date" class="fctrl" name="from" value="{{ $from }}"/></div>
      <div class="col-md-2"><label class="flbl">To</label><input type="date" class="fctrl" name="to" value="{{ $to }}"/></div>
      <div class="col-md-1"><button type="submit" class="bsave w-100">Filter</button></div>
    </form>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Name</th><th>Role</th><th>Date</th><th>Course</th><th>Task/Subject</th><th>Hours</th><th>Status</th><th>Metrics</th><th>Review</th><th></th></tr></thead>
      <tbody>
        @forelse ($reports as $r)
          <tr>
            <td><a href="{{ route('admin.daily-reports.performance', $r->user) }}" style="text-decoration:none">{{ $r->user->name }}</a></td>
            <td style="text-transform:capitalize">{{ $r->user->getRoleNames()->first() }}</td>
            <td>{{ $r->report_date->format('d M Y') }}</td>
            <td>{{ $r->course->name ?? '—' }}</td>
            <td>{{ $r->task_subject ?? '—' }}</td>
            <td>{{ $r->hours_worked ?? '—' }}</td>
            <td><span class="badge-rt {{ $r->status === 'present' ? 'bg-active' : ($r->status === 'leave' ? 'bg-pending' : 'bg-inactive') }}">{{ ucfirst($r->status) }}</span></td>
            <td style="font-size:11.5px;color:var(--muted)">
              @if ($r->user->hasRole('teacher'))
                {{ $r->students_present ?? '—' }} students · HW: {{ $r->homework_assigned ? 'Yes' : 'No' }}
              @else
                {{ $r->leads_followed_up ?? 0 }} leads · {{ $r->students_enrolled ?? 0 }} enrolled · {{ $r->calls_made ?? 0 }} calls
              @endif
            </td>
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
              @if ($r->review_status === 'pending')
                <div class="d-flex gap-1">
                  <form method="POST" action="{{ route('admin.daily-reports.approve', $r) }}">
                    @csrf
                    <button type="submit" class="bghost" style="font-size:12px;padding:6px 10px;color:var(--ok)" title="Approve"><i class="bi bi-check-lg"></i></button>
                  </form>
                  <button type="button" class="bghost" style="font-size:12px;padding:6px 10px;color:var(--danger)" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $r->id }}"><i class="bi bi-x-lg"></i></button>
                </div>

                <div class="modal fade" id="rejectModal{{ $r->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header"><h5 class="modal-title">Reject Report — {{ $r->user->name }} ({{ $r->report_date->format('d M Y') }})</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                      <form method="POST" action="{{ route('admin.daily-reports.reject', $r) }}">
                        @csrf
                        <div class="modal-body">
                          <label class="flbl mb-2">Reason for rejection</label>
                          <textarea class="fctrl" name="admin_comment" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer"><button type="submit" class="bsave">Reject Report</button></div>
                      </form>
                    </div>
                  </div>
                </div>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="10" style="color:var(--muted)">No reports found for these filters.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $reports->links() }}</div>
@endsection
