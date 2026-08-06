@extends('layouts.admin')

@section('title', 'Attendance Records')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Attendance Records</h4><p>Check-ins across all R-Tech and franchise locations</p></div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('admin.attendance-locations.index') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-geo-alt-fill me-1"></i>Manage Locations</a>
      <a href="{{ route('admin.attendance.export', request()->query()) }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-download me-1"></i>Export CSV</a>
    </div>
  </div>

  <form method="GET" action="{{ route('admin.attendance.index') }}" class="d-flex gap-2 flex-wrap mb-3">
    <select class="fctrl" name="location" style="max-width:220px">
      <option value="">All Locations</option>
      @foreach ($locations as $loc)
        <option value="{{ $loc->id }}" @selected((string) $locationId === (string) $loc->id)>{{ $loc->name }}</option>
      @endforeach
    </select>
    <input class="fctrl" type="date" name="date" value="{{ $date }}" style="max-width:180px">
    <button class="bsave" type="submit">Filter</button>
    @if ($locationId || $date)
      <a href="{{ route('admin.attendance.index') }}" class="bghost">Clear</a>
    @endif
  </form>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Location</th><th>Date</th><th>Punch In</th><th>Punch Out</th><th>Duration</th><th>Where From</th><th>Device</th></tr></thead>
      <tbody>
        @forelse ($attendances as $a)
          <tr>
            <td>{{ $a->user->name }}</td>
            <td>{{ $a->location->name }}</td>
            <td>{{ $a->date->format('d M Y') }}</td>
            <td>{{ $a->marked_at->format('h:i A') }} <span class="badge-rt {{ $a->method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->method) }}</span></td>
            <td>
              @if ($a->isPunchedOut())
                {{ $a->check_out_at->format('h:i A') }} <span class="badge-rt {{ $a->check_out_method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->check_out_method) }}</span>
              @else
                <span style="color:var(--muted)">—</span>
              @endif
            </td>
            <td>{{ $a->durationLabel() ?? '—' }}</td>
            <td style="font-size:11.5px;color:var(--muted)">
              @if ($a->method === 'gps')
                In:
                @if ($a->mapUrl())
                  <a href="{{ $a->mapUrl() }}" target="_blank" rel="noopener">{{ $a->distance_meters }}m away &middot; view map</a>
                @else
                  {{ $a->distance_meters }}m away
                @endif
              @else
                In: WiFi &middot; {{ $a->wifi_ssid }}
              @endif
              @if ($a->isPunchedOut())
                <br>
                @if ($a->check_out_method === 'gps')
                  Out:
                  @if ($a->checkOutMapUrl())
                    <a href="{{ $a->checkOutMapUrl() }}" target="_blank" rel="noopener">{{ $a->check_out_distance_meters }}m away &middot; view map</a>
                  @else
                    {{ $a->check_out_distance_meters }}m away
                  @endif
                @else
                  Out: WiFi &middot; {{ $a->check_out_wifi_ssid }}
                @endif
              @endif
            </td>
            <td style="font-size:11.5px;color:var(--muted)">
              @if ($a->deviceLabel())
                <span title="{{ $a->device }}">In: {{ $a->deviceLabel() }}</span>
              @if ($a->ip_address)
                <br><span style="opacity:.75">{{ $a->ip_address }}</span>
              @endif
              @endif
              @if ($a->isPunchedOut() && $a->checkOutDeviceLabel())
                <br><span title="{{ $a->check_out_device }}">Out: {{ $a->checkOutDeviceLabel() }}</span>
                @if ($a->check_out_ip_address)
                  <br><span style="opacity:.75">{{ $a->check_out_ip_address }}</span>
                @endif
              @endif
              @if (!$a->deviceLabel() && !$a->checkOutDeviceLabel())
                <span style="color:var(--muted)">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="color:var(--muted)">No attendance records found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $attendances->links() }}</div>
@endsection
