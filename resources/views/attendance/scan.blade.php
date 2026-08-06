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
          <div class="att-marked mb-3">
            <i class="bi bi-check-circle-fill"></i>
            <div>
              <div class="att-marked-title">Punched In</div>
              <div class="att-marked-sub">{{ $today->marked_at->format('h:i:s A') }} via {{ strtoupper($today->method) }}</div>
            </div>
          </div>
        @endif

        @if ($today && $today->isPunchedOut())
          <div class="att-marked">
            <i class="bi bi-flag-fill"></i>
            <div>
              <div class="att-marked-title">Punched Out</div>
              <div class="att-marked-sub">{{ $today->check_out_at->format('h:i A') }} via {{ strtoupper($today->check_out_method) }} &middot; Total time: {{ $today->durationLabel() }}</div>
            </div>
          </div>
        @else
          <p style="font-size:13px;color:var(--muted);margin:20px 0">
            {{ $today ? "Tap below when you're leaving to punch out." : "Tap below to punch in using your phone's location." }}
          </p>

          <form method="POST" action="{{ route('attendance.mark') }}" id="gpsForm">
            @csrf
            <input type="hidden" name="qr_token" value="{{ $location->qr_token }}">
            <input type="hidden" name="method" value="gps">
            <input type="hidden" name="latitude" id="gpsLat">
            <input type="hidden" name="longitude" id="gpsLng">
            <input type="hidden" name="accuracy" id="gpsAcc">
            <button type="button" class="btn-enr w-100 justify-content-center" style="padding:14px" id="gpsBtn"><i class="bi bi-geo-alt-fill me-2"></i>{{ $today ? 'Punch Out (GPS)' : 'Punch In (GPS)' }}</button>
          </form>
          <p id="gpsStatus" style="font-size:12px;color:var(--muted);margin-top:10px"></p>

          <div class="att-divider"><span>or</span></div>

          <button class="bghost w-100" type="button" data-bs-toggle="collapse" data-bs-target="#wifiForm"><i class="bi bi-wifi me-1"></i>{{ $today ? 'Punch Out via WiFi Instead' : 'Punch In via WiFi Instead' }}</button>

          <form method="POST" action="{{ route('attendance.mark') }}" class="collapse mt-3" id="wifiForm">
            @csrf
            <input type="hidden" name="qr_token" value="{{ $location->qr_token }}">
            <input type="hidden" name="method" value="wifi">
            <label class="fl">Institute WiFi Network Name</label>
            <input class="mi mb-3" type="text" name="wifi_ssid" placeholder="Check your phone's WiFi settings" required>
            <button class="btn-enr w-100 justify-content-center" type="submit" id="wifiBtn"><i class="bi bi-wifi me-2"></i>{{ $today ? 'Punch Out (WiFi)' : 'Punch In (WiFi)' }}</button>
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

        const errorMessages = {
          1: 'Location permission denied. Please allow location access in your browser settings, or use the WiFi option instead.',
          2: 'Could not determine your location — weak GPS signal. Try moving near a window or outdoors, or use the WiFi option instead.',
          3: 'Location request timed out. Please try again, or use the WiFi option instead.',
        };

        navigator.geolocation.getCurrentPosition(
          (position) => {
            document.getElementById('gpsLat').value = position.coords.latitude;
            document.getElementById('gpsLng').value = position.coords.longitude;
            document.getElementById('gpsAcc').value = position.coords.accuracy;
            statusEl.textContent = 'Location found, marking attendance…';
            document.getElementById('gpsForm').submit();
          },
          (err) => {
            gpsBtn.disabled = false;
            statusEl.textContent = errorMessages[err.code] || 'Could not get your location. Please use the WiFi option instead.';
          },
          // maximumAge lets the browser reuse a recent fix (up to 30s old)
          // instead of forcing a brand-new cold GPS lock every single time —
          // that cold lock is what was timing out indoors/on weak signal.
          { enableHighAccuracy: true, timeout: 15000, maximumAge: 30000 }
        );
      });
    }

    // Prevents a second tap (nervous double-tap on a slow connection) from
    // firing a second request while the first is still in flight.
    const wifiForm = document.getElementById('wifiForm');
    if (wifiForm) {
      wifiForm.addEventListener('submit', () => {
        document.getElementById('wifiBtn').disabled = true;
      });
    }
  </script>
@endsection
