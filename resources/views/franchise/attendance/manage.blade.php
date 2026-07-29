@extends('layouts.franchise')

@section('title', 'Attendance')

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Attendance</h4><p>Your institute's QR check-in point, location and WiFi settings</p></div>
    <a href="{{ route('franchise.attendance.records') }}" class="bghost" style="text-decoration:none"><i class="bi bi-list-check me-1"></i>View Check-in Records</a>
  </div>

  @if ($locations->isEmpty())
    <div class="card-rt mt-4" style="padding:24px">
      <p style="font-size:13px;color:var(--muted);margin:0">No attendance point has been set up for your institute yet. Contact R-Tech admin to get one configured.</p>
    </div>
  @else
    <div class="row g-3 mt-1">
      @foreach ($locations as $loc)
        <div class="col-md-6">
          <div class="card-rt">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 style="font-size:14px;font-weight:700;margin:0">{{ $loc->name }}</h6>
              <span class="badge-rt {{ $loc->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ ucfirst($loc->status) }}</span>
            </div>
            <p style="font-size:11.5px;color:var(--muted);margin-bottom:14px"><i class="bi bi-people-fill me-1"></i>{{ $loc->attendances_count }} check-ins recorded</p>
            <div id="qrcanvas{{ $loc->id }}" class="d-flex justify-content-center my-3"></div>
            <p style="font-size:11px;color:var(--muted);word-break:break-all;text-align:center;margin-bottom:16px">{{ $loc->scanUrl() }}</p>

            <form method="POST" action="{{ route('franchise.attendance.update', $loc) }}">
              @csrf
              <div class="row g-2">
                <div class="col-6"><label class="flbl">Latitude</label><input class="fctrl" type="text" name="latitude" id="lat{{ $loc->id }}" value="{{ $loc->latitude }}" required/></div>
                <div class="col-6"><label class="flbl">Longitude</label><input class="fctrl" type="text" name="longitude" id="lng{{ $loc->id }}" value="{{ $loc->longitude }}" required/></div>
              </div>
              <button type="button" class="bghost w-100 mt-2" style="font-size:12px" onclick="useMyLocation('lat{{ $loc->id }}','lng{{ $loc->id }}')"><i class="bi bi-crosshair me-1"></i>Use My Current Location</button>
              <div class="mt-2"><label class="flbl">WiFi Network Name</label><input class="fctrl" name="wifi_ssid" value="{{ $loc->wifi_ssid }}" placeholder="Your institute's WiFi SSID"/></div>
              <button class="bsave w-100 mt-3" type="submit">Save</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  @endif
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
        width: 180,
        height: 180,
      });
    @endforeach
  </script>
@endsection
