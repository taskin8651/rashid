<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Enroll in {{ $course->name }} | R-Tech Computer</title>
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
    <div class="container" style="max-width:720px;position:relative;z-index:1">

      <div class="pay-steps">
        <div class="pay-step active"><div class="pay-step-dot">1</div>Details</div>
        <div class="pay-step-line"></div>
        <div class="pay-step"><div class="pay-step-dot">2</div>Payment</div>
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
              <span>{{ $course->duration_text ?? 'Self-paced' }} &middot; Secure payment powered by Razorpay</span>
            </div>
          </div>

          @if ($errors->any())
            <div class="alert alert-danger" style="font-size:13px">{{ $errors->first() }}</div>
          @endif

          <form method="POST" action="{{ route('enroll.store', $course) }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="fl">Select Batch</label>
                <select class="mi" name="batch_id" style="cursor:pointer">
                  <option value="">No preference</option>
                  @foreach ($batches as $b)
                    <option value="{{ $b->id }}">{{ $b->label }} · {{ $b->schedule_text }} · Starts {{ $b->start_date->format('d M Y') }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="fl">Coupon Code (optional)</label>
                <input class="mi" type="text" name="coupon_code" placeholder="Enter coupon code" value="{{ old('coupon_code') }}" />
              </div>
            </div>

            <div class="pay-total-box">
              <div class="pay-total-row">
                <span class="lbl">Course Fee</span>
                <span class="amt">₹{{ number_format($course->price, 0) }}</span>
              </div>
              <div style="font-size:11.5px;color:var(--muted);margin-top:6px">Coupon discount, if valid, will be applied on the next step.</div>
            </div>

            <button class="pay-btn" type="submit"><i class="bi bi-shield-lock-fill"></i>Continue to Payment</button>

            <div class="pay-trust">
              <div class="pay-trust-item"><i class="bi bi-patch-check-fill"></i>256-bit SSL Secured</div>
              <div class="pay-trust-item"><i class="bi bi-lightning-charge-fill"></i>Instant Access</div>
              <div class="pay-trust-item"><i class="bi bi-shield-check"></i>Trusted by Razorpay</div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</body>
</html>
