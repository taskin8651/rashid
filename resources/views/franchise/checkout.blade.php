<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Franchise Checkout | R-Tech Computer</title>
  @include('partials.theme-init')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
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
          <div class="pay-badge"><i class="bi bi-shop-window"></i>Franchise Registration</div>

          <div class="pay-item">
            <div class="pay-thumb-fallback"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
              <h6>{{ $booking->city }} Franchise</h6>
              <span>Reserved for {{ $booking->name }}</span>
            </div>
          </div>

          <div class="sbox text-start mb-0">
            <div class="sr"><span>Franchise Registration Fee</span><span>₹{{ number_format($booking->amount, 0) }}</span></div>
            <div class="sr"><span>Refundable</span><span style="color:var(--ok)">Yes</span></div>
          </div>

          <div class="pay-total-box">
            <div class="pay-total-row">
              <span class="lbl">Amount Payable</span>
              <span class="amt">₹{{ number_format($booking->amount, 0) }}</span>
            </div>
          </div>

          <button class="pay-btn" id="payBtn"><i class="bi bi-shield-lock-fill"></i>Pay Securely with Razorpay</button>

          <div class="pay-trust">
            <div class="pay-trust-item"><i class="bi bi-patch-check-fill"></i>256-bit SSL Secured</div>
            <div class="pay-trust-item"><i class="bi bi-arrow-counterclockwise"></i>Refundable Fee</div>
            <div class="pay-trust-item"><i class="bi bi-shield-check"></i>Trusted by Razorpay</div>
          </div>

          <p class="pay-secure-note">Your payment is processed securely by Razorpay. R-Tech Computer never stores your card details.</p>
        </div>
      </div>
    </div>
  </section>

  <form id="verifyForm" method="POST" action="{{ route('franchise.booking.verify') }}" style="display:none">
    @csrf
    <input type="hidden" name="razorpay_order_id" id="vf_order_id">
    <input type="hidden" name="razorpay_payment_id" id="vf_payment_id">
    <input type="hidden" name="razorpay_signature" id="vf_signature">
  </form>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    document.getElementById('payBtn').addEventListener('click', function () {
      var rzp = new Razorpay({
        key: '{{ $order['key_id'] }}',
        amount: {{ $order['amount'] }},
        currency: '{{ $order['currency'] }}',
        order_id: '{{ $order['order_id'] }}',
        name: 'R-Tech Computer',
        description: 'Franchise Registration — {{ $booking->city }}',
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
