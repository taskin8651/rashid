@extends('layouts.admin')

@section('title', 'Attendance Locations')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Attendance Locations</h4><p>Each location gets a QR code students scan to mark attendance</p></div>
    <a href="{{ route('admin.attendance.index') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-list-check me-1"></i>View Attendance Records</a>
  </div>

  <div class="card-rt mb-4">
    <div class="card-title">Add Location</div>
    <form method="POST" action="{{ route('admin.attendance-locations.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-4"><label class="flbl">Name</label><input class="fctrl" name="name" placeholder="e.g. R-Tech Computer HQ" required/></div>
        <div class="col-md-4">
          <label class="flbl">Franchise (leave blank for HQ)</label>
          <select class="fctrl" name="franchise_booking_id">
            <option value="">R-Tech HQ (Direct)</option>
            @foreach ($franchises as $f)
              <option value="{{ $f->id }}">{{ $f->city }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4"><label class="flbl">WiFi Network Name (optional)</label><input class="fctrl" name="wifi_ssid" placeholder="Institute's WiFi SSID"/></div>
        <div class="col-md-3"><label class="flbl">Latitude</label><input class="fctrl" type="text" name="latitude" id="newLat" required/></div>
        <div class="col-md-3"><label class="flbl">Longitude</label><input class="fctrl" type="text" name="longitude" id="newLng" required/></div>
        <div class="col-md-3"><label class="flbl">Geofence Radius (m)</label><input class="fctrl" type="number" name="radius_meters" value="150" min="20" max="2000" required/></div>
        <div class="col-md-3 d-flex align-items-end"><button type="button" class="bghost w-100" onclick="useMyLocation('newLat','newLng')"><i class="bi bi-crosshair me-1"></i>Use My Location</button></div>
        <div class="col-12"><button class="bsave" type="submit"><i class="bi bi-plus-lg me-1"></i>Add Location</button></div>
      </div>
    </form>
  </div>

  <div class="row g-3">
    @forelse ($locations as $loc)
      <div class="col-md-6 col-lg-4">
        <div class="card-rt">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <h6 style="font-size:14px;font-weight:700;margin:0">{{ $loc->name }}</h6>
              <span style="font-size:11px;color:var(--muted)">{{ $loc->ownerLabel() }}</span>
            </div>
            <span class="badge-rt {{ $loc->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ ucfirst($loc->status) }}</span>
          </div>
          <p style="font-size:11.5px;color:var(--muted);margin-bottom:4px"><i class="bi bi-geo-alt-fill me-1"></i>{{ $loc->latitude }}, {{ $loc->longitude }} &middot; {{ $loc->radius_meters }}m radius</p>
          @if ($loc->wifi_ssid)
            <p style="font-size:11.5px;color:var(--muted);margin-bottom:4px"><i class="bi bi-wifi me-1"></i>{{ $loc->wifi_ssid }}</p>
          @endif
          <p style="font-size:11.5px;color:var(--muted);margin-bottom:12px"><i class="bi bi-people-fill me-1"></i>{{ $loc->attendances_count }} check-ins</p>
          <div class="d-flex gap-2">
            <button class="bghost flex-grow-1" style="font-size:12px;padding:8px" data-bs-toggle="modal" data-bs-target="#qr{{ $loc->id }}"><i class="bi bi-qr-code me-1"></i>QR Code</button>
            <button class="bghost flex-grow-1" style="font-size:12px;padding:8px" data-bs-toggle="modal" data-bs-target="#edit{{ $loc->id }}"><i class="bi bi-pencil-fill me-1"></i>Edit</button>
          </div>
        </div>
      </div>

      <!-- QR Modal -->
      <div class="modal fade" id="qr{{ $loc->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">{{ $loc->name }} — QR Code</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body text-center">
              <div id="qrcanvas{{ $loc->id }}" class="d-flex justify-content-center mb-3"></div>
              <p style="font-size:11px;color:var(--muted);word-break:break-all">{{ $loc->scanUrl() }}</p>
              <button class="bsave mt-2" type="button" onclick="printQr('{{ $loc->id }}', '{{ addslashes($loc->name) }}')"><i class="bi bi-printer-fill me-1"></i>Print</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div class="modal fade" id="edit{{ $loc->id }}" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit {{ $loc->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('admin.attendance-locations.update', $loc) }}" id="editLocForm{{ $loc->id }}">
              @csrf
              <div class="modal-body">
                <div class="mb-3"><label class="flbl">Name</label><input class="fctrl" name="name" value="{{ $loc->name }}" required/></div>
                <div class="row g-3">
                  <div class="col-6"><label class="flbl">Latitude</label><input class="fctrl" type="text" name="latitude" id="lat{{ $loc->id }}" value="{{ $loc->latitude }}" required/></div>
                  <div class="col-6"><label class="flbl">Longitude</label><input class="fctrl" type="text" name="longitude" id="lng{{ $loc->id }}" value="{{ $loc->longitude }}" required/></div>
                </div>
                <button type="button" class="bghost w-100 mt-2" onclick="useMyLocation('lat{{ $loc->id }}','lng{{ $loc->id }}')"><i class="bi bi-crosshair me-1"></i>Use My Current Location</button>
                <div class="row g-3 mt-1">
                  <div class="col-6"><label class="flbl">Radius (m)</label><input class="fctrl" type="number" name="radius_meters" value="{{ $loc->radius_meters }}" min="20" max="2000" required/></div>
                  <div class="col-6">
                    <label class="flbl">Status</label>
                    <select class="fctrl" name="status">
                      <option value="active" @selected($loc->status === 'active')>Active</option>
                      <option value="inactive" @selected($loc->status === 'inactive')>Inactive</option>
                    </select>
                  </div>
                </div>
                <div class="mt-3"><label class="flbl">WiFi Network Name</label><input class="fctrl" name="wifi_ssid" value="{{ $loc->wifi_ssid }}"/></div>
              </div>
            </form>
            <!-- Kept as a sibling of the edit form above, not nested inside it —
                 nesting one form inside another is invalid HTML and browsers
                 silently misattribute submit buttons when it happens, which
                 was causing "Save Changes" to trigger the delete instead.
                 Both buttons below target their form explicitly via form=. -->
            <form method="POST" action="{{ route('admin.attendance-locations.destroy', $loc) }}" id="deleteLocForm{{ $loc->id }}" onsubmit="return confirm('Delete this location? Its attendance history will also be removed.')">
              @csrf @method('DELETE')
            </form>
            <div class="modal-footer">
              <button type="submit" form="deleteLocForm{{ $loc->id }}" class="bghost" style="color:var(--danger);border-color:rgba(239,68,68,.3)">Delete</button>
              <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" form="editLocForm{{ $loc->id }}" class="bsave">Save Changes</button>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="color:var(--muted)">No attendance locations set up yet.</p></div>
    @endforelse
  </div>
@endsection

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
  <script>
    function useMyLocation(latId, lngId) {
      if (!navigator.geolocation) { alert('Geolocation not supported in this browser.'); return; }
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          document.getElementById(latId).value = pos.coords.latitude.toFixed(7);
          document.getElementById(lngId).value = pos.coords.longitude.toFixed(7);
        },
        () => alert('Could not get your location. Please allow location access.')
      );
    }

    @foreach ($locations as $loc)
      new QRCode(document.getElementById('qrcanvas{{ $loc->id }}'), {
        text: @json($loc->scanUrl()),
        width: 200,
        height: 200,
      });
    @endforeach

    function printQr(id, name) {
      const canvas = document.querySelector('#qrcanvas' + id + ' img, #qrcanvas' + id + ' canvas');
      const img = canvas.tagName === 'CANVAS' ? canvas.toDataURL() : canvas.src;
      const w = window.open('', '_blank');
      w.document.write('<html><body style="text-align:center;font-family:sans-serif"><h3>' + name + '</h3><img src="' + img + '"><p>Scan to mark attendance</p></body></html>');
      w.document.close();
      w.print();
    }
  </script>
@endsection
