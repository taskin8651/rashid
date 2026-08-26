<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Checkout | R-Tech Computer</title>
  @include('partials.theme-init')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
  <style>
    .pay-tabs { display: flex; gap: 8px; margin: 20px 0 18px; }
    .pay-tab-btn {
      flex: 1; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(148,163,184,.35);
      background: transparent; color: var(--muted, #64748b); font-weight: 600; font-size: 13px; cursor: pointer;
    }
    .pay-tab-btn.active { background: #2563eb; border-color: #2563eb; color: #fff; }
    .upi-qr-box { text-align: center; }
    .upi-qr-box img { width: 220px; height: 220px; border-radius: 12px; border: 1px solid rgba(148,163,184,.35); }
  </style>
</head>
<body>
  <nav class="navbar scrolled" id="topnav">
    <div class="container">
      <div class="nav-inner">
        <a class="brand" href="{{ route('home') }}">
          <div class="brand-logo-wrap"><img src="{{ asset('assets/img/logo.png') }}" alt="R-Tech Computer" class="brand-logo"></div>
        </a>
        <div class="pay-nav-trust"><i class="bi bi-lock-fill"></i>Secure Checkout</div>
      </div>
    </div>
  </nav>

  <section class="pay-wrap">
    <div class="pay-glow"></div>
    <div class="container" style="max-width:520px;position:relative;z-index:1">

      <div class="pay-steps">
        <div class="pay-step done"><div class="pay-step-dot"><i class="bi bi-check-lg"></i></div>Details</div>
        <div class="pay-step-line"></div>
        <div class="pay-step active"><div class="pay-step-dot">2</div>Payment</div>
        <div class="pay-step-line"></div>
        <div class="pay-step"><div class="pay-step-dot">3</div>Confirmed</div>
      </div>

      <div class="pay-card">
        <div class="pay-card-top"></div>
        <div class="pay-card-body">
          <div class="pay-badge"><i class="bi bi-mortarboard-fill"></i>Course Enrollment</div>

          <div class="pay-item">
            @if ($course->thumbnailUrl())
              <img src="{{ $course->thumbnailUrl() }}" class="pay-thumb" alt="{{ $course->name }}">
            @else
              <div class="pay-thumb-fallback"><i class="bi bi-mortarboard-fill"></i></div>
            @endif
            <div>
              <h6>{{ $course->name }}</h6>
              <span>{{ $course->duration_text ?? 'Self-paced' }}</span>
            </div>
          </div>

          <div class="sbox text-start mb-0">
            <div class="sr"><span>Course Fee</span><span>₹{{ number_format($course->price, 0) }}</span></div>
            @if ($discount > 0)
              <div class="sr"><span>Discount</span><span style="color:var(--ok)">−₹{{ number_format($discount, 0) }}</span></div>
            @endif
          </div>

          <div class="pay-total-box">
            <div class="pay-total-row">
              <span class="lbl">Amount Payable</span>
              <span class="amt">₹{{ number_format($finalAmount, 0) }}</span>
            </div>
          </div>

          <div class="pay-tabs">
            <button type="button" class="pay-tab-btn active" id="tabRazorpay"><i class="bi bi-credit-card-fill me-1"></i>Razorpay</button>
            @if ($upiQr)
              <button type="button" class="pay-tab-btn" id="tabUpi"><i class="bi bi-qr-code me-1"></i>UPI / QR</button>
            @endif
          </div>

          <div id="panelRazorpay">
            <button class="pay-btn" id="payBtn"><i class="bi bi-shield-lock-fill"></i>Pay Securely with Razorpay</button>

            <div class="pay-trust">
              <div class="pay-trust-item"><i class="bi bi-patch-check-fill"></i>256-bit SSL Secured</div>
              <div class="pay-trust-item"><i class="bi bi-lightning-charge-fill"></i>Instant Access</div>
              <div class="pay-trust-item"><i class="bi bi-shield-check"></i>Trusted by Razorpay</div>
            </div>

            <p class="pay-secure-note">Your payment is processed securely by Razorpay. R-Tech Computer never stores your card details.</p>
          </div>

          @if ($upiQr)
            <div id="panelUpi" style="display:none">
              <div class="upi-qr-box">
                <img src="{{ $upiQr }}" alt="UPI QR Code">
                <div style="margin:10px 0;font-size:12.5px;color:var(--muted)">Scan with GPay, PhonePe, Paytm or any UPI app</div>
                <a href="{{ $upiLink }}" class="bghost" style="display:inline-block;margin-bottom:10px;text-decoration:none">Open in UPI App</a>
                <div style="font-size:13px;margin-bottom:6px"><strong>UPI ID:</strong> {{ $upiId }}</div>
              </div>

              @if ($errors->has('utr'))
                <div class="alert alert-danger" style="font-size:13px">{{ $errors->first('utr') }}</div>
              @endif

              <form method="POST" action="{{ route('enroll.upi.submit', $course) }}">
                @csrf
                <label class="fl">UPI Transaction Ref / UTR Number</label>
                <input class="mi" type="text" name="utr" placeholder="e.g. 123456789012" required />
                <button class="pay-btn" type="submit" style="margin-top:14px"><i class="bi bi-check-circle-fill"></i>I've Paid — Submit for Verification</button>
                <p class="pay-secure-note">Your course will be activated once we confirm the transfer — usually within a few hours.</p>
              </form>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

  <form id="verifyForm" method="POST" action="{{ route('enroll.verify', $course) }}" style="display:none">
    @csrf
    <input type="hidden" name="razorpay_order_id" id="vf_order_id">
    <input type="hidden" name="razorpay_payment_id" id="vf_payment_id">
    <input type="hidden" name="razorpay_signature" id="vf_signature">
  </form>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    @if ($upiQr)
      document.getElementById('tabRazorpay').addEventListener('click', function () {
        document.getElementById('panelRazorpay').style.display = '';
        document.getElementById('panelUpi').style.display = 'none';
        document.getElementById('tabRazorpay').classList.add('active');
        document.getElementById('tabUpi').classList.remove('active');
      });
      document.getElementById('tabUpi').addEventListener('click', function () {
        document.getElementById('panelRazorpay').style.display = 'none';
        document.getElementById('panelUpi').style.display = '';
        document.getElementById('tabUpi').classList.add('active');
        document.getElementById('tabRazorpay').classList.remove('active');
      });
    @endif

    document.getElementById('payBtn').addEventListener('click', function () {
      var rzp = new Razorpay({
        key: '{{ $order['key_id'] }}',
        amount: {{ $order['amount'] }},
        currency: '{{ $order['currency'] }}',
        order_id: '{{ $order['order_id'] }}',
        name: 'R-Tech Computer',
        description: '{{ $course->name }} Enrollment',
        handler: function (response) {
          document.getElementById('vf_order_id').value = response.razorpay_order_id;
          document.getElementById('vf_payment_id').value = response.razorpay_payment_id;
          document.getElementById('vf_signature').value = response.razorpay_signature;
          document.getElementById('verifyForm').submit();
        },
        theme: { color: '#2563eb' },
      });
      rzp.open();
    });
  </script>
</body>
</html>
