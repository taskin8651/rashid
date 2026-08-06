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

  <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"/>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Location</th><th>Date</th><th>Punch In</th><th>Punch Out</th><th>Duration</th><th>Where From</th><th>Device</th><th></th></tr></thead>
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
            <td style="font-size:11.5px;color:var(--muted)">
              @if ($a->method === 'gps')
                In: {{ $a->distance_meters }}m away
              @else
                In: WiFi &middot; {{ $a->wifi_ssid }}
              @endif
              @if ($a->isPunchedOut())
                <br>
                @if ($a->check_out_method === 'gps')
                  Out: {{ $a->check_out_distance_meters }}m away
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
            <td>
              <button class="action-btn" style="border:none;background:none" title="View Full Detail" data-bs-toggle="modal" data-bs-target="#attView{{ $a->id }}"><i class="bi bi-eye-fill"></i></button>
            </td>
          </tr>

          <!-- Attendance Detail Modal -->
          <div class="modal fade" id="attView{{ $a->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Attendance Detail — {{ $a->user->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                  <div class="row g-3 mb-3">
                    <div class="col-md-4"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Student</span><div style="font-size:13px;font-weight:600">{{ $a->user->name }}</div></div>
                    <div class="col-md-4"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Location</span><div style="font-size:13px;font-weight:600">{{ $a->location->name }}</div></div>
                    <div class="col-md-4"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Date</span><div style="font-size:13px;font-weight:600">{{ $a->date->format('d M Y') }}</div></div>
                  </div>

                  <div class="row g-3 mb-3">
                    <div class="col-md-6">
                      <div class="card-rt" style="padding:14px">
                        <h6 style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:10px"><i class="bi bi-box-arrow-in-right me-1"></i>Punch In</h6>
                        <div style="font-size:13px;margin-bottom:4px">Time: <b>{{ $a->marked_at->format('h:i:s A') }}</b> <span class="badge-rt {{ $a->method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->method) }}</span></div>
                        @if ($a->method === 'gps')
                          <div style="font-size:13px;margin-bottom:4px">Distance: <b>{{ $a->distance_meters }}m</b> from {{ $a->location->name }}@if ($a->accuracy_meters) <span style="color:var(--muted)">(GPS accuracy &plusmn;{{ $a->accuracy_meters }}m)</span>@endif</div>
                        @else
                          <div style="font-size:13px;margin-bottom:4px">WiFi: <b>{{ $a->wifi_ssid }}</b></div>
                        @endif
                        <div style="font-size:12px;color:var(--muted)">Device: {{ $a->deviceLabel() ?? '—' }}</div>
                        <div style="font-size:12px;color:var(--muted)">IP: {{ $a->ip_address ?? '—' }}</div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card-rt" style="padding:14px">
                        <h6 style="font-size:12px;font-weight:700;text-transform:uppercase;color:var(--muted);margin-bottom:10px"><i class="bi bi-box-arrow-right me-1"></i>Punch Out</h6>
                        @if ($a->isPunchedOut())
                          <div style="font-size:13px;margin-bottom:4px">Time: <b>{{ $a->check_out_at->format('h:i:s A') }}</b> <span class="badge-rt {{ $a->check_out_method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->check_out_method) }}</span></div>
                          @if ($a->check_out_method === 'gps')
                            <div style="font-size:13px;margin-bottom:4px">Distance: <b>{{ $a->check_out_distance_meters }}m</b> from {{ $a->location->name }}@if ($a->check_out_accuracy_meters) <span style="color:var(--muted)">(GPS accuracy &plusmn;{{ $a->check_out_accuracy_meters }}m)</span>@endif</div>
                          @else
                            <div style="font-size:13px;margin-bottom:4px">WiFi: <b>{{ $a->check_out_wifi_ssid }}</b></div>
                          @endif
                          <div style="font-size:12px;color:var(--muted)">Device: {{ $a->checkOutDeviceLabel() ?? '—' }}</div>
                          <div style="font-size:12px;color:var(--muted)">IP: {{ $a->check_out_ip_address ?? '—' }}</div>
                          <div style="font-size:12px;color:var(--muted)">Duration: {{ $a->durationLabel() ?? '—' }}</div>
                        @else
                          <span style="font-size:13px;color:var(--muted)">Not punched out yet.</span>
                        @endif
                      </div>
                    </div>
                  </div>

                  @if (($a->method === 'gps' && $a->latitude) || ($a->check_out_method === 'gps' && $a->check_out_latitude))
                    <div
                      id="attMap{{ $a->id }}"
                      class="att-map"
                      style="height:320px;border-radius:12px"
                      data-loc-lat="{{ $a->location->latitude }}"
                      data-loc-lng="{{ $a->location->longitude }}"
                      data-loc-name="{{ $a->location->name }}"
                      data-in-lat="{{ $a->method === 'gps' ? $a->latitude : '' }}"
                      data-in-lng="{{ $a->method === 'gps' ? $a->longitude : '' }}"
                      data-in-dist="{{ $a->distance_meters }}"
                      data-out-lat="{{ $a->check_out_method === 'gps' ? $a->check_out_latitude : '' }}"
                      data-out-lng="{{ $a->check_out_method === 'gps' ? $a->check_out_longitude : '' }}"
                      data-out-dist="{{ $a->check_out_distance_meters }}"
                    ></div>
                  @else
                    <p style="font-size:12px;color:var(--muted);margin:0">No GPS location recorded for this check-in (marked via WiFi).</p>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          <tr><td colspan="9" style="color:var(--muted)">No attendance records found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $attendances->links() }}</div>
@endsection

@section('scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    document.querySelectorAll('.modal').forEach(function (modalEl) {
      modalEl.addEventListener('shown.bs.modal', function () {
        const mapDiv = modalEl.querySelector('.att-map');
        if (!mapDiv || mapDiv.dataset.initialized) return;
        mapDiv.dataset.initialized = '1';

        const locLat = parseFloat(mapDiv.dataset.locLat), locLng = parseFloat(mapDiv.dataset.locLng);
        const inLat = parseFloat(mapDiv.dataset.inLat), inLng = parseFloat(mapDiv.dataset.inLng);
        const outLat = parseFloat(mapDiv.dataset.outLat), outLng = parseFloat(mapDiv.dataset.outLng);

        const map = L.map(mapDiv).setView([locLat, locLng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; OpenStreetMap contributors',
          maxZoom: 19,
        }).addTo(map);

        const bounds = [[locLat, locLng]];
        L.circleMarker([locLat, locLng], { radius: 9, color: '#2563eb', fillColor: '#2563eb', fillOpacity: 1 })
          .addTo(map).bindPopup(mapDiv.dataset.locName + ' (Institute)');

        if (!isNaN(inLat) && !isNaN(inLng)) {
          bounds.push([inLat, inLng]);
          L.circleMarker([inLat, inLng], { radius: 8, color: '#15803d', fillColor: '#15803d', fillOpacity: 1 })
            .addTo(map).bindPopup('Punch In — ' + mapDiv.dataset.inDist + 'm away');
          L.polyline([[locLat, locLng], [inLat, inLng]], { color: '#15803d', dashArray: '4,6' }).addTo(map);
        }

        if (!isNaN(outLat) && !isNaN(outLng)) {
          bounds.push([outLat, outLng]);
          L.circleMarker([outLat, outLng], { radius: 8, color: '#b45309', fillColor: '#b45309', fillOpacity: 1 })
            .addTo(map).bindPopup('Punch Out — ' + mapDiv.dataset.outDist + 'm away');
          L.polyline([[locLat, locLng], [outLat, outLng]], { color: '#b45309', dashArray: '4,6' }).addTo(map);
        }

        map.fitBounds(bounds, { padding: [30, 30] });

        setTimeout(function () { map.invalidateSize(); }, 200);
      });
    });
  </script>
@endsection
