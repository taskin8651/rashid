@extends('layouts.admin')

@section('title', 'Attendance Records')

@section('content')
  <link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet"/>

  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-qr-code-scan"></i>Attendance</div>
    <h4>Attendance Overview</h4>
    <p>Check-ins across all R-Tech and franchise locations</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['total'] }}</b><span>Records</span></div>
      <div><b>{{ $stats['still_in'] }}</b><span>Currently In</span></div>
      <div><b>{{ $stats['completed'] }}</b><span>Completed</span></div>
      <div><b>{{ \App\Models\Attendance::formatMinutes($stats['avg_duration_minutes'] ? (int) round($stats['avg_duration_minutes']) : null) ?? '—' }}</b><span>Avg Duration</span></div>
    </div>
  </div>

  <div class="d-flex justify-content-end gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.attendance-locations.index') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-geo-alt-fill me-1"></i>Manage Locations</a>
    <a href="{{ route('admin.attendance.export', request()->query()) }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-download me-1"></i>Export CSV</a>
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
      <thead><tr><th>Student</th><th>Location</th><th>Date</th><th>Punch In</th><th>Punch Out</th><th>Duration</th><th>Where From</th><th>Device</th><th></th></tr></thead>
      <tbody>
        @forelse ($attendances as $a)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                @if ($a->user->photoUrl())
                  <img src="{{ $a->user->photoUrl() }}" alt="{{ $a->user->name }}" class="att-avatar">
                @else
                  <div class="att-avatar-ph">{{ strtoupper(substr($a->user->name, 0, 1)) }}</div>
                @endif
                <span style="font-weight:600">{{ $a->user->name }}</span>
              </div>
            </td>
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
            <td>
              @if ($a->method === 'gps')
                <span class="att-pill"><i class="bi bi-geo-alt-fill" style="color:var(--ok)"></i>In {{ $a->distance_meters }}m</span>
              @else
                <span class="att-pill"><i class="bi bi-wifi" style="color:var(--ok)"></i>In WiFi</span>
              @endif
              @if ($a->isPunchedOut())
                <br>
                @if ($a->check_out_method === 'gps')
                  <span class="att-pill"><i class="bi bi-geo-alt-fill" style="color:var(--warn)"></i>Out {{ $a->check_out_distance_meters }}m</span>
                @else
                  <span class="att-pill"><i class="bi bi-wifi" style="color:var(--warn)"></i>Out WiFi</span>
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
                <div class="modal-body" style="padding:20px">
                  <div class="ov-banner" style="padding:22px 24px;margin-bottom:20px">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" style="position:absolute;top:18px;right:20px;z-index:2"></button>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                      @if ($a->user->photoUrl())
                        <img src="{{ $a->user->photoUrl() }}" alt="{{ $a->user->name }}" class="att-avatar-lg">
                      @else
                        <div class="att-avatar-lg-ph">{{ strtoupper(substr($a->user->name, 0, 1)) }}</div>
                      @endif
                      <div>
                        <h4 class="mb-0">{{ $a->user->name }}</h4>
                        <p class="mb-0"><i class="bi bi-geo-alt-fill me-1"></i>{{ $a->location->name }} &middot; {{ $a->date->format('d M Y') }}</p>
                      </div>
                      <div class="ms-auto text-end" style="position:relative">
                        <div style="font-family:'Space Grotesk',sans-serif;font-size:24px;font-weight:700">{{ $a->durationLabel() ?? '—' }}</div>
                        <div style="font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:rgba(255,255,255,.75)">Duration</div>
                      </div>
                    </div>
                  </div>

                  <div class="att-stepper">
                    <div class="att-step">
                      <div class="att-step-icon in"><i class="bi bi-box-arrow-in-right"></i></div>
                      <div class="att-step-body">
                        <div class="att-step-title">Punch In</div>
                        <div class="att-step-time">{{ $a->marked_at->format('h:i:s A') }}</div>
                        @if ($a->method === 'gps')
                          <span class="att-pill"><i class="bi bi-geo-alt-fill" style="color:var(--ok)"></i>{{ $a->distance_meters }}m from {{ $a->location->name }}</span>
                          @if ($a->accuracy_meters)
                            <span class="att-pill"><i class="bi bi-crosshair"></i>&plusmn;{{ $a->accuracy_meters }}m accuracy</span>
                          @endif
                        @else
                          <span class="att-pill"><i class="bi bi-wifi" style="color:var(--ok)"></i>{{ $a->wifi_ssid }}</span>
                        @endif
                        <div class="att-meta-row"><i class="bi bi-phone"></i>{{ $a->deviceLabel() ?? 'Unknown device' }}</div>
                        @if ($a->ip_address)
                          <div class="att-meta-row"><i class="bi bi-hdd-network"></i>{{ $a->ip_address }}</div>
                        @endif
                      </div>
                    </div>
                    <div class="att-step">
                      <div class="att-step-icon {{ $a->isPunchedOut() ? 'out' : 'pending' }}"><i class="bi bi-box-arrow-right"></i></div>
                      <div class="att-step-body">
                        <div class="att-step-title">Punch Out</div>
                        @if ($a->isPunchedOut())
                          <div class="att-step-time">{{ $a->check_out_at->format('h:i:s A') }}</div>
                          @if ($a->check_out_method === 'gps')
                            <span class="att-pill"><i class="bi bi-geo-alt-fill" style="color:var(--warn)"></i>{{ $a->check_out_distance_meters }}m from {{ $a->location->name }}</span>
                            @if ($a->check_out_accuracy_meters)
                              <span class="att-pill"><i class="bi bi-crosshair"></i>&plusmn;{{ $a->check_out_accuracy_meters }}m accuracy</span>
                            @endif
                          @else
                            <span class="att-pill"><i class="bi bi-wifi" style="color:var(--warn)"></i>{{ $a->check_out_wifi_ssid }}</span>
                          @endif
                          <div class="att-meta-row"><i class="bi bi-phone"></i>{{ $a->checkOutDeviceLabel() ?? 'Unknown device' }}</div>
                          @if ($a->check_out_ip_address)
                            <div class="att-meta-row"><i class="bi bi-hdd-network"></i>{{ $a->check_out_ip_address }}</div>
                          @endif
                        @else
                          <span style="font-size:13px;color:var(--muted)">Not punched out yet.</span>
                        @endif
                      </div>
                    </div>
                  </div>

                  @if (($a->method === 'gps' && $a->latitude) || ($a->check_out_method === 'gps' && $a->check_out_latitude))
                    <div class="att-map-card">
                      <div class="att-map-head">
                        <h6><i class="bi bi-map me-1"></i>Location Map</h6>
                        <button type="button" class="att-map-recenter" title="Recenter" data-map-recenter="attMap{{ $a->id }}"><i class="bi bi-arrows-angle-contract"></i></button>
                      </div>
                      <div class="att-map-wrap">
                        <div class="att-map-overlay">
                          @if ($a->method === 'gps')
                            <div class="att-map-overlay-row"><span class="att-map-overlay-dot" style="background:#16a34a"></span>In: {{ $a->distance_meters }}m</div>
                          @endif
                          @if ($a->check_out_method === 'gps')
                            <div class="att-map-overlay-row"><span class="att-map-overlay-dot" style="background:#f59e0b"></span>Out: {{ $a->check_out_distance_meters }}m</div>
                          @endif
                          <div class="att-map-overlay-row"><span class="att-map-overlay-dot" style="background:#2563eb"></span>Allowed: {{ $a->location->radius_meters }}m</div>
                        </div>
                        <div
                          id="attMap{{ $a->id }}"
                          class="att-map"
                          style="height:340px"
                          data-loc-lat="{{ $a->location->latitude }}"
                          data-loc-lng="{{ $a->location->longitude }}"
                          data-loc-name="{{ $a->location->name }}"
                          data-radius="{{ $a->location->radius_meters }}"
                          data-in-lat="{{ $a->method === 'gps' ? $a->latitude : '' }}"
                          data-in-lng="{{ $a->method === 'gps' ? $a->longitude : '' }}"
                          data-in-dist="{{ $a->distance_meters }}"
                          data-out-lat="{{ $a->check_out_method === 'gps' ? $a->check_out_latitude : '' }}"
                          data-out-lng="{{ $a->check_out_method === 'gps' ? $a->check_out_longitude : '' }}"
                          data-out-dist="{{ $a->check_out_distance_meters }}"
                        ></div>
                      </div>
                      <div class="att-legend">
                        <div class="att-legend-item"><span class="att-legend-dot" style="background:#2563eb"></span>Institute</div>
                        <div class="att-legend-item"><span class="att-legend-dot" style="background:#16a34a"></span>Punch In</div>
                        <div class="att-legend-item"><span class="att-legend-dot" style="background:#f59e0b"></span>Punch Out</div>
                        <div class="att-legend-item"><span class="att-legend-dot" style="background:rgba(37,99,235,.15);border-color:#2563eb"></span>Allowed Radius</div>
                      </div>
                    </div>
                  @else
                    <p style="font-size:12px;color:var(--muted);margin:16px 0 0">No GPS location recorded for this check-in (marked via WiFi).</p>
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
    function attPin(color, icon) {
      return L.divIcon({
        className: '',
        html: '<div class="att-pin" style="background:' + color + '"><i class="bi ' + icon + '"></i></div>',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -28],
      });
    }

    function attDistLabel(midpoint, text) {
      return L.marker(midpoint, {
        icon: L.divIcon({ className: '', html: '<div class="att-dist-label">' + text + '</div>', iconSize: [0, 0] }),
        interactive: false,
        keyboard: false,
      });
    }

    function attIsDarkTheme() {
      const attr = document.documentElement.getAttribute('data-theme');
      if (attr === 'dark') return true;
      if (attr === 'light') return false;
      return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    const attMaps = {};

    document.querySelectorAll('.modal').forEach(function (modalEl) {
      modalEl.addEventListener('shown.bs.modal', function () {
        const mapDiv = modalEl.querySelector('.att-map');
        if (!mapDiv || mapDiv.dataset.initialized) return;
        mapDiv.dataset.initialized = '1';

        const locLat = parseFloat(mapDiv.dataset.locLat), locLng = parseFloat(mapDiv.dataset.locLng);
        const inLat = parseFloat(mapDiv.dataset.inLat), inLng = parseFloat(mapDiv.dataset.inLng);
        const outLat = parseFloat(mapDiv.dataset.outLat), outLng = parseFloat(mapDiv.dataset.outLng);
        const radius = parseFloat(mapDiv.dataset.radius) || 0;

        const map = L.map(mapDiv, { zoomControl: true }).setView([locLat, locLng], 16);
        attMaps[mapDiv.id] = map;

        const tiles = attIsDarkTheme()
          ? 'https://{s}.basemaps.cartocdn.com/rastertiles/dark_all/{z}/{x}/{y}{r}.png'
          : 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
        L.tileLayer(tiles, {
          attribution: '&copy; OpenStreetMap &copy; CARTO',
          maxZoom: 20,
          subdomains: 'abcd',
        }).addTo(map);

        const bounds = [[locLat, locLng]];

        // Geofence — the circle within which a punch is actually accepted,
        // drawn so it's obvious at a glance whether a check-in/out landed
        // inside or outside the allowed zone.
        if (radius > 0) {
          L.circle([locLat, locLng], {
            radius: radius,
            color: '#2563eb',
            weight: 1.5,
            dashArray: '5,7',
            fillColor: '#2563eb',
            fillOpacity: 0.08,
          }).addTo(map);
        }

        L.marker([locLat, locLng], { icon: attPin('#2563eb', 'bi-building') })
          .addTo(map).bindPopup('<b>' + mapDiv.dataset.locName + '</b><br>Institute &middot; ' + radius + 'm allowed radius', { className: 'att-popup' });

        if (!isNaN(inLat) && !isNaN(inLng)) {
          bounds.push([inLat, inLng]);
          L.marker([inLat, inLng], { icon: attPin('#16a34a', 'bi-geo-alt-fill') })
            .addTo(map).bindPopup('<b>Punch In</b><br>' + mapDiv.dataset.inDist + 'm away', { className: 'att-popup' });
          L.polyline([[locLat, locLng], [inLat, inLng]], { color: '#16a34a', weight: 3, dashArray: '4,7', opacity: .8 }).addTo(map);
          attDistLabel([(locLat + inLat) / 2, (locLng + inLng) / 2], mapDiv.dataset.inDist + 'm').addTo(map);
        }

        if (!isNaN(outLat) && !isNaN(outLng)) {
          bounds.push([outLat, outLng]);
          L.marker([outLat, outLng], { icon: attPin('#f59e0b', 'bi-geo-alt-fill') })
            .addTo(map).bindPopup('<b>Punch Out</b><br>' + mapDiv.dataset.outDist + 'm away', { className: 'att-popup' });
          L.polyline([[locLat, locLng], [outLat, outLng]], { color: '#f59e0b', weight: 3, dashArray: '4,7', opacity: .8 }).addTo(map);
          attDistLabel([(locLat + outLat) / 2, (locLng + outLng) / 2], mapDiv.dataset.outDist + 'm').addTo(map);
        }

        setTimeout(function () {
          map.invalidateSize();
          map.flyToBounds(bounds, { padding: [40, 40], duration: 1.1 });
        }, 200);
      });
    });

    document.querySelectorAll('[data-map-recenter]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const map = attMaps[btn.dataset.mapRecenter];
        if (!map) return;
        const bounds = [];
        map.eachLayer(function (layer) {
          if (layer instanceof L.Marker) bounds.push(layer.getLatLng());
        });
        if (bounds.length) map.flyToBounds(L.latLngBounds(bounds), { padding: [40, 40], duration: .8 });
      });
    });
  </script>
@endsection
