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
</head>
<body>
  <nav class="navbar scrolled" id="topnav">
    <div class="container">
      <div class="nav-inner">
        <a class="brand" href="{{ route('home') }}">
          <div class="brand-logo-wrap"><img src="{{ asset('"assets/img/logo.png"') }}" alt="R-Tech Computer" class="brand-logo"></div>
        </a>
      </div>
    </div>
  </nav>

  <section class="sec" style="padding-top:120px">
    <div class="container" style="max-width:560px">
      <div class="cbox text-center">
        <h5 style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:20px;margin-bottom:20px">Complete Your Payment</h5>
        <div class="sbox text-start mb-4">
          <div class="sr"><span>Course</span><span>{{ $course->name }}</span></div>
          <div class="sr"><span>Discount</span><span style="color:var(--ok)">−₹{{ number_format($discount, 0) }}</span></div>
          <div class="st"><span>Total</span><span style="color:var(--orange)">₹{{ number_format($finalAmount, 0) }}</span></div>
        </div>
        <button class="btn-enr w-100 justify-content-center" style="font-size:14px;padding:12px" id="payBtn"><i class="bi bi-shield-lock-fill me-2"></i>Pay Securely with Razorpay</button>
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
