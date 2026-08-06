@extends('layouts.franchise')

@section('title', 'Attendance Records')

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Attendance Records</h4><p>Check-ins from your students</p></div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('franchise.attendance.export', request()->query()) }}" class="bghost" style="text-decoration:none"><i class="bi bi-download me-1"></i>Export CSV</a>
      <a href="{{ route('franchise.attendance.manage') }}" class="bghost" style="text-decoration:none"><i class="bi bi-geo-alt-fill me-1"></i>Manage QR &amp; Location</a>
    </div>
  </div>

  @if ($locations->isEmpty())
    <div class="card-rt mt-4" style="padding:24px">
      <p style="font-size:13px;color:var(--muted);margin:0">No attendance point has been set up for your institute yet. Contact R-Tech admin to get one configured.</p>
    </div>
  @else
    <form method="GET" action="{{ route('franchise.attendance.records') }}" class="d-flex gap-2 flex-wrap my-3">
      @if ($locations->count() > 1)
        <select class="fctrl" name="location" style="max-width:220px">
          <option value="">All My Locations</option>
          @foreach ($locations as $loc)
            <option value="{{ $loc->id }}" @selected((string) $locationId === (string) $loc->id)>{{ $loc->name }}</option>
          @endforeach
        </select>
      @endif
      <input class="fctrl" type="date" name="date" value="{{ $date }}" style="max-width:180px">
      <button class="bsave" type="submit">Filter</button>
      @if ($locationId || $date)
        <a href="{{ route('franchise.attendance.records') }}" class="bghost">Clear</a>
      @endif
    </form>

    <div class="card-rt">
      <div class="table-wrap"><table class="table-rt">
        <thead><tr><th>Student</th><th>Location</th><th>Date</th><th>Punch In</th><th>Punch Out</th><th>Duration</th></tr></thead>
        <tbody>
          @forelse ($attendances as $a)
            <tr>
              <td>{{ $a->user->name }}</td>
              <td>{{ $a->location->name }}</td>
              <td>{{ $a->date->format('d M Y') }}</td>
              <td>{{ $a->marked_at->format('h:i:s A') }} <span class="badge-rt {{ $a->method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->method) }}</span></td>
              <td>
                @if ($a->isPunchedOut())
                  {{ $a->check_out_at->format('h:i:s A') }} <span class="badge-rt {{ $a->check_out_method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->check_out_method) }}</span>
                @else
                  <span style="color:var(--muted)">—</span>
                @endif
              </td>
              <td>{{ $a->durationLabel() ?? '—' }}</td>
            </tr>
          @empty
            <tr><td colspan="6" style="color:var(--muted)">No check-ins yet.</td></tr>
          @endforelse
        </tbody>
      </table></div>
    </div>

    <div class="mt-3">{{ $attendances->links() }}</div>
  @endif
@endsection
