@extends('layouts.admin')

@section('title', 'Lead Management')

@php
  $statusBadge = ['new' => 'bg-pending', 'contacted' => 'bg-pending', 'follow_up' => 'bg-pending', 'interested' => 'bg-active', 'converted' => 'bg-paid', 'lost' => 'bg-failed'];
  $statusLabel = fn ($s) => ucwords(str_replace('_', ' ', $s));
  $sourceLabel = fn ($s) => ucwords(str_replace('_', ' ', $s));
@endphp

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
    <div class="shead mb-0"><h4>Lead Management</h4><p>Course enquiries — track, follow up, and convert to enrolled students</p></div>
    <div class="d-flex gap-2 flex-wrap">
      <form method="GET" action="{{ route('admin.leads.index') }}" class="d-flex gap-2">
        @if ($status)<input type="hidden" name="status" value="{{ $status }}"/>@endif
        <input class="fctrl" type="text" name="search" value="{{ $search }}" placeholder="Search name/phone/email…" style="width:220px"/>
        <button class="bsave" type="submit">Search</button>
      </form>
      @can('manage-leads')
        <a href="{{ route('admin.leads.export') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-download me-1"></i>Export CSV</a>
        <button class="bsave" style="font-size:14px;padding:11px 20px" data-bs-toggle="modal" data-bs-target="#addLead"><i class="bi bi-person-plus-fill me-1"></i>Add Lead</button>
      @endcan
    </div>
  </div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.leads.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All ({{ $counts->sum() }})</a>
    @foreach (\App\Models\StudentLead::STATUSES as $s)
      <a href="{{ route('admin.leads.index', ['status' => $s]) }}" class="badge-rt {{ $status === $s ? $statusBadge[$s] : 'bg-inactive' }}" style="text-decoration:none">{{ $statusLabel($s) }} ({{ $counts[$s] ?? 0 }})</a>
    @endforeach
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Lead</th><th>Course</th><th>Franchise</th><th>Source</th><th>Assigned</th><th>Status</th><th>Follow-up</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($leads as $lead)
          <tr>
            <td><a href="{{ route('admin.leads.show', $lead) }}" style="color:var(--text);text-decoration:none;font-weight:700">{{ $lead->name }}</a><div style="font-size:11px;color:var(--muted)">{{ $lead->phone }}{{ $lead->email ? ' · '.$lead->email : '' }}</div></td>
            <td>{{ $lead->course->name ?? '—' }}</td>
            <td>{{ $lead->franchiseBooking->city ?? 'Head Office' }}</td>
            <td>{{ $sourceLabel($lead->source) }}</td>
            <td>{{ $lead->assignedTo->name ?? '—' }}</td>
            <td><span class="badge-rt {{ $statusBadge[$lead->status] }}">{{ $statusLabel($lead->status) }}</span></td>
            <td style="font-size:12px">{{ optional($lead->next_follow_up_date)->format('d M Y') ?? '—' }}</td>
            <td>
              <a href="{{ route('admin.leads.show', $lead) }}" class="action-btn" title="View"><i class="bi bi-eye-fill"></i></a>
              @can('manage-leads')
                <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" onsubmit="return confirm('Delete this lead? This cannot be undone.')" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
                </form>
              @endcan
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="color:var(--muted)">No leads found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $leads->links() }}</div>

  @can('manage-leads')
    <!-- Add Lead Modal -->
    <div class="modal fade" id="addLead" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Add Lead</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('admin.leads.store') }}">
            @csrf
            <div class="modal-body">
              <div class="mb-3"><label class="flbl">Name</label><input class="fctrl" name="name" required/></div>
              <div class="row g-3 mb-3">
                <div class="col-6"><label class="flbl">Phone</label><input class="fctrl" name="phone" required/></div>
                <div class="col-6"><label class="flbl">Email</label><input class="fctrl" type="email" name="email"/></div>
              </div>
              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label class="flbl">Course Interested</label>
                  <select class="fctrl" name="course_id">
                    <option value="">—</option>
                    @foreach ($courses as $c)
                      <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6">
                  <label class="flbl">Source</label>
                  <select class="fctrl" name="source" required>
                    @foreach (\App\Models\StudentLead::SOURCES as $s)
                      <option value="{{ $s }}">{{ $sourceLabel($s) }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label class="flbl">Refer to Franchise (optional)</label>
                  <select class="fctrl" name="franchise_booking_id">
                    <option value="">Head Office</option>
                    @foreach ($bookings as $b)
                      <option value="{{ $b->id }}">{{ $b->city }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6">
                  <label class="flbl">Assign To</label>
                  <select class="fctrl" name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach ($staff as $st)
                      <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="mb-3"><label class="flbl">Next Follow-up</label><input class="fctrl" type="date" name="next_follow_up_date"/></div>
              <div class="mb-1"><label class="flbl">Note (optional)</label><input class="fctrl" name="note" placeholder="e.g. Walked in asking about Digital Marketing course"/></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="bsave">Add Lead</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endcan
@endsection
