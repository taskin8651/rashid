@extends('layouts.site')

@section('title', 'Mark Attendance')

@section('content')
  <section class="sec">
    <div class="container" style="max-width:520px">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Attendance</div>
        <h2 class="sec-h">{{ $location->name }}</h2>
        <p style="font-size:13px;color:var(--muted)">{{ now()->format('l, d M Y') }}</p>
      </div>

      <div class="cbox rv text-center">
        @if (session('status'))
          <div class="alert alert-success" style="font-size:13px">{{ session('status') }}</div>
        @endif
        @if ($errors->has('location'))
          <div class="alert alert-danger" style="font-size:13px">{{ $errors->first('location') }}</div>
        @endif

        @if ($today)
          <div class="att-marked">
            <i class="bi bi-check-circle-fill"></i>
            <div>
              <div class="att-marked-title">Attendance Marked</div>
              <div class="att-marked-sub">You checked in at {{ $today->marked_at->format('h:i A') }} via {{ strtoupper($today->method) }}</div>
            </div>
          </div>
        @else
          <p style="font-size:13px;color:var(--muted);margin-bottom:20px">Tap below to mark your attendance using your phone's location.</p>

          <form method="POST" action="{{ route('attendance.mark') }}" id="gpsForm">
            @csrf
            <input type="hidden" name="qr_token" value="{{ $location->qr_token }}">
            <input type="hidden" name="method" value="gps">
            <input type="hidden" name="latitude" id="gpsLat">
            <input type="hidden" name="longitude" id="gpsLng">
            <button type="button" class="btn-enr w-100 justify-content-center" style="padding:14px" id="gpsBtn"><i class="bi bi-geo-alt-fill me-2"></i>Mark Attendance (GPS)</button>
          </form>
          <p id="gpsStatus" style="font-size:12px;color:var(--muted);margin-top:10px"></p>

          <div class="att-divider"><span>or</span></div>

          <button class="bghost w-100" type="button" data-bs-toggle="collapse" data-bs-target="#wifiForm"><i class="bi bi-wifi me-1"></i>Mark via Institute WiFi Instead</button>

          <form method="POST" action="{{ route('attendance.mark') }}" class="collapse mt-3" id="wifiForm">
            @csrf
            <input type="hidden" name="qr_token" value="{{ $location->qr_token }}">
            <input type="hidden" name="method" value="wifi">
            <label class="fl">Institute WiFi Network Name</label>
            <input class="mi mb-3" type="text" name="wifi_ssid" placeholder="Check your phone's WiFi settings" required>
            <button class="btn-enr w-100 justify-content-center" type="submit"><i class="bi bi-wifi me-2"></i>Mark Attendance (WiFi)</button>
          </form>
        @endif
      </div>
    </div>
  </section>
@endsection

@section('scripts')
  <script>
    const gpsBtn = document.getElementById('gpsBtn');
    if (gpsBtn) {
      gpsBtn.addEventListener('click', () => {
        const statusEl = document.getElementById('gpsStatus');
        if (!navigator.geolocation) {
          statusEl.textContent = 'Location is not supported on this device. Please use the WiFi option.';
          return;
        }
        gpsBtn.disabled = true;
        statusEl.textContent = 'Getting your location…';
        navigator.geolocation.getCurrentPosition(
          (position) => {
            document.getElementById('gpsLat').value = position.coords.latitude;
            document.getElementById('gpsLng').value = position.coords.longitude;
            statusEl.textContent = 'Location found, marking attendance…';
            document.getElementById('gpsForm').submit();
          },
          () => {
            gpsBtn.disabled = false;
            statusEl.textContent = 'Location permission denied. Please allow location access, or use the WiFi option instead.';
          },
          { enableHighAccuracy: true, timeout: 10000 }
        );
      });
    }
  </script>
@endsection
