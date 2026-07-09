<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Booking Confirmed | R-Tech Computer</title>
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
    <div class="container" style="max-width:640px">
      <div class="cbox text-center">
        <div style="width:64px;height:64px;background:rgba(40,180,90,.15);border:1px solid rgba(125,220,160,.3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:30px;color:var(--ok);margin:0 auto 20px">
          <i class="bi bi-check-lg"></i>
        </div>
        <h4 style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:8px">Booking Confirmed!</h4>
        <p style="font-size:14px;color:var(--muted);margin-bottom:24px">Thanks {{ $booking->name }} — your franchise registration for <strong style="color:var(--text)">{{ $booking->city }}</strong> is confirmed. Our franchise team will call you within 24 hours.</p>

        <div class="sbox text-start mb-4">
          <div class="sr"><span>Booking Reference</span><span>#{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</span></div>
          <div class="sr"><span>City</span><span>{{ $booking->city }}</span></div>
          <div class="sr"><span>Amount Paid</span><span style="color:var(--ok)">₹{{ number_format($booking->amount, 0) }}</span></div>
        </div>

        <h6 style="font-size:14px;font-weight:700;color:var(--text);text-align:left;margin-bottom:14px">What Happens Next</h6>
        <div class="text-start">
          @foreach (\App\Models\FranchiseBooking::STAGES as $key => $label)
            <div class="d-flex align-items-center gap-3 mb-2">
              <div style="width:26px;height:26px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;
                @if($key === $booking->stage) background:var(--grad);color:#fff;
                @else background:rgba(var(--text-rgb),.06);color:var(--muted);
                @endif">
                @if($key === $booking->stage)<i class="bi bi-check-lg"></i>@else {{ $loop->iteration }} @endif
              </div>
              <span style="font-size:13px;color:{{ $key === $booking->stage ? '#fff' : 'var(--muted)' }}">{{ $label }}</span>
            </div>
          @endforeach
        </div>

        <a class="btn-p w-100 justify-content-center mt-4" href="{{ route('franchise.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Go to Franchise Dashboard</a>
      </div>
    </div>
  </section>
</body>
</html>
