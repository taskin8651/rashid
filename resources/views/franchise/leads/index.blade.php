@extends('layouts.franchise')

@section('title', 'Leads')

@php
  $statusBadge = ['new' => 'bg-pending', 'contacted' => 'bg-pending', 'follow_up' => 'bg-pending', 'interested' => 'bg-active', 'converted' => 'bg-paid', 'lost' => 'bg-failed'];
  $statusLabel = fn ($s) => ucwords(str_replace('_', ' ', $s));
  $sourceLabel = fn ($s) => ucwords(str_replace('_', ' ', $s));
@endphp

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
    <div class="shead mb-0"><h4>Leads</h4><p>Course enquiries — track, follow up, and convert to enrolled students</p></div>
    @if ($canManage)
      <button class="bsave" data-bs-toggle="modal" data-bs-target="#addLead"><i class="bi bi-person-plus-fill me-1"></i>Add Lead</button>
    @endif
  </div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('franchise.leads.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All ({{ $counts->sum() }})</a>
    @foreach (\App\Models\StudentLead::STATUSES as $s)
      <a href="{{ route('franchise.leads.index', ['status' => $s]) }}" class="badge-rt {{ $status === $s ? $statusBadge[$s] : 'bg-inactive' }}" style="text-decoration:none">{{ $statusLabel($s) }} ({{ $counts[$s] ?? 0 }})</a>
    @endforeach
  </div>

  <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden">
    <div class="prow" style="background:rgba(var(--text-rgb),.03);font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase">
      <div style="flex:2">Lead</div><div style="flex:2">Course</div><div style="flex:1">Source</div><div style="flex:1">Assigned</div><div style="flex:1">Status</div><div style="flex:1">Follow-up</div>
    </div>
    @forelse ($leads as $lead)
      <a href="{{ route('franchise.leads.show', $lead) }}" class="prow" style="text-decoration:none;color:inherit">
        <div style="flex:2"><div style="font-size:14px;font-weight:700;color:var(--text)">{{ $lead->name }}</div><div style="font-size:11px;color:var(--muted)">{{ $lead->phone }}{{ $lead->email ? ' · '.$lead->email : '' }}</div></div>
        <div style="flex:2;font-size:13px">{{ $lead->course->name ?? '—' }}</div>
        <div style="flex:1;font-size:12px;color:rgba(var(--text-rgb),.7)">{{ $sourceLabel($lead->source) }}</div>
        <div style="flex:1;font-size:12px;color:rgba(var(--text-rgb),.7)">{{ $lead->assignedTo->name ?? '—' }}</div>
        <div style="flex:1"><span class="badge-rt {{ $statusBadge[$lead->status] }}">{{ $statusLabel($lead->status) }}</span></div>
        <div style="flex:1;font-size:12px">{{ optional($lead->next_follow_up_date)->format('d M Y') ?? '—' }}</div>
      </a>
    @empty
      <div class="prow"><div style="flex:2;font-size:13px;color:var(--muted)">No leads yet.</div></div>
    @endforelse
  </div>
  <div class="mt-3">{{ $leads->links() }}</div>

  @if ($canManage)
    <!-- Add Lead Modal -->
    <div class="modal fade" id="addLead" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Add Lead</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('franchise.leads.store') }}">
            @csrf
            <div class="modal-body">
              @if ($bookings->count() > 1)
                <div class="mb-3">
                  <label class="flbl">Institute</label>
                  <select class="fctrl" name="franchise_booking_id" required>
                    @foreach ($bookings as $b)
                      <option value="{{ $b->id }}">{{ $b->city }}</option>
                    @endforeach
                  </select>
                </div>
              @else
                <input type="hidden" name="franchise_booking_id" value="{{ $bookings->first()->id ?? '' }}"/>
              @endif
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
                  <label class="flbl">Assign To</label>
                  <select class="fctrl" name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach ($staff as $st)
                      <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-6"><label class="flbl">Next Follow-up</label><input class="fctrl" type="date" name="next_follow_up_date"/></div>
              </div>
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
  @endif
@endsection
