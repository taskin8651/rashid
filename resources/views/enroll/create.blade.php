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
          <div class="brand-logo-wrap"><img src="{{ asset('"assets/img/logo.png"') }}" alt="R-Tech Computer" class="brand-logo"></div>
        </a>
      </div>
    </div>
  </nav>

  <section class="sec" style="padding-top:120px">
    <div class="container" style="max-width:760px">
      <div class="cbox">
        <h5 style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:20px;margin-bottom:6px">Enroll in {{ $course->name }}</h5>
        <p style="font-size:13px;color:var(--muted);margin-bottom:24px">Secure payment powered by Razorpay</p>

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

          <div class="sbox mt-4">
            <div class="sr"><span>Course Fee</span><span>₹{{ number_format($course->price, 0) }}</span></div>
            <div class="sr"><span>Discount</span><span style="color:var(--ok)">Applied at checkout if valid</span></div>
            <div class="st"><span>Total</span><span style="color:var(--orange)">Calculated on next step</span></div>
          </div>

          <button class="btn-enr w-100 justify-content-center mt-4" style="font-size:14px;padding:12px" type="submit"><i class="bi bi-shield-lock-fill me-2"></i>Continue to Payment</button>
        </form>
      </div>
    </div>
  </section>
</body>
</html>
