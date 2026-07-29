@extends('layouts.student')

@section('title', 'Attendance')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-qr-code-scan"></i>Attendance</div>
    <h4>My Attendance</h4>
    <p>Scan your institute's QR code below to check in each day.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['total'] }}</b><span>Total Check-ins</span></div>
      <div><b>{{ $stats['this_month'] }}</b><span>This Month</span></div>
    </div>
  </div>

  @if (session('status'))
    <div class="alert alert-success mb-3" style="font-size:13px">{{ session('status') }}</div>
  @endif
  @if ($errors->has('location'))
    <div class="alert alert-danger mb-3" style="font-size:13px">{{ $errors->first('location') }}</div>
  @endif

  <button class="bsave mb-4" type="button" data-bs-toggle="modal" data-bs-target="#qrScanModal"><i class="bi bi-camera-fill me-1"></i>Scan QR to Mark Attendance</button>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Date</th><th>Location</th><th>Punch In</th><th>Punch Out</th><th>Duration</th></tr></thead>
      <tbody>
        @forelse ($attendances as $a)
          <tr>
            <td>{{ $a->date->format('d M Y') }}</td>
            <td>{{ $a->location->name }}</td>
            <td>{{ $a->marked_at->format('h:i A') }} <span class="badge-rt {{ $a->method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->method) }}</span></td>
            <td>
              @if ($a->isPunchedOut())
                {{ $a->check_out_at->format('h:i A') }} <span class="badge-rt {{ $a->check_out_method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->check_out_method) }}</span>
              @else
                <span style="color:var(--muted)">Still in class</span>
              @endif
            </td>
            <td>{{ $a->durationLabel() ?? '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:var(--muted)">No attendance marked yet. Scan the QR code at your institute to check in.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $attendances->links() }}</div>

  <!-- QR SCAN MODAL -->
  <div class="modal fade" id="qrScanModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Scan Attendance QR Code</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body text-center">
          <div id="qr-reader" style="width:100%;border-radius:12px;overflow:hidden"></div>
          <p id="qrScanStatus" style="font-size:12.5px;color:var(--muted);margin-top:14px">Point your camera at the QR code displayed at your institute.</p>

          <div class="att-divider"><span>or</span></div>

          <button class="bghost w-100" type="button" data-bs-toggle="collapse" data-bs-target="#qrWifiFallback"><i class="bi bi-wifi me-1"></i>Enter WiFi Manually Instead</button>
          <form method="POST" action="{{ route('attendance.mark') }}" class="collapse mt-3 text-start" id="qrWifiFallback">
            @csrf
            <input type="hidden" name="method" value="wifi">
            <label class="flbl">QR / Location Code</label>
            <input class="fctrl mb-2" type="text" name="qr_token" id="manualToken" placeholder="Paste the code from your institute" required>
            <label class="flbl">Institute WiFi Network Name</label>
            <input class="fctrl mb-3" type="text" name="wifi_ssid" placeholder="Check your phone's WiFi settings" required>
            <button class="bsave w-100" type="submit"><i class="bi bi-wifi me-2"></i>Mark Attendance (WiFi)</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('attendance.mark') }}" id="qrGpsForm" style="display:none">
    @csrf
    <input type="hidden" name="method" value="gps">
    <input type="hidden" name="qr_token" id="qrGpsToken">
    <input type="hidden" name="latitude" id="qrGpsLat">
    <input type="hidden" name="longitude" id="qrGpsLng">
  </form>
@endsection

@section('scripts')
  <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <script>
    const qrModalEl = document.getElementById('qrScanModal');
    const qrStatus = document.getElementById('qrScanStatus');
    let html5QrCode = null;

    function extractToken(decodedText) {
      try {
        const url = new URL(decodedText);
        const parts = url.pathname.split('/').filter(Boolean);
        return parts[parts.length - 1];
      } catch (e) {
        return decodedText.trim();
      }
    }

    function onScanSuccess(decodedText) {
      const token = extractToken(decodedText);
      qrStatus.textContent = 'QR found — getting your location…';

      if (html5QrCode) {
        html5QrCode.stop().catch(() => {});
      }

      if (!navigator.geolocation) {
        qrStatus.textContent = 'Location is not supported on this device. Use the WiFi option instead.';
        document.getElementById('manualToken').value = token;
        return;
      }

      navigator.geolocation.getCurrentPosition(
        (position) => {
          document.getElementById('qrGpsToken').value = token;
          document.getElementById('qrGpsLat').value = position.coords.latitude;
          document.getElementById('qrGpsLng').value = position.coords.longitude;
          qrStatus.textContent = 'Location found, marking attendance…';
          document.getElementById('qrGpsForm').submit();
        },
        () => {
          qrStatus.textContent = 'Location permission denied. Use the WiFi option instead.';
          document.getElementById('manualToken').value = token;
        },
        { enableHighAccuracy: true, timeout: 10000 }
      );
    }

    if (qrModalEl) {
      qrModalEl.addEventListener('shown.bs.modal', () => {
        qrStatus.textContent = 'Point your camera at the QR code displayed at your institute.';
        html5QrCode = new Html5Qrcode('qr-reader');
        html5QrCode.start(
          { facingMode: 'environment' },
          { fps: 10, qrbox: 220 },
          onScanSuccess,
          () => {}
        ).catch(() => {
          qrStatus.textContent = 'Could not access camera. Please allow camera access, or use the WiFi option below.';
        });
      });

      qrModalEl.addEventListener('hidden.bs.modal', () => {
        if (html5QrCode) {
          html5QrCode.stop().catch(() => {});
          html5QrCode = null;
        }
      });
    }
  </script>
@endsection
