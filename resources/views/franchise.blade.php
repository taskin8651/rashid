<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Franchise Opportunity | R-Tech Computer</title>
  <meta name="description" content="Start your own R-Tech Computer institute. Trusted brand, proven curriculum, marketing support and complete business setup — low investment, high ROI franchise model." />
  @include('partials.theme-init')
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet" />
</head>
<body>
  @if (session('status'))
    <div id="tw"><div class="tr ok"><i class="bi bi-check-circle-fill"></i>{{ session('status') }}</div></div>
  @endif

  @include('partials.franchise.nav')
  @include('partials.franchise.hero')
  @include('partials.franchise.why')
  @include('partials.franchise.tiers')
  @include('partials.franchise.cities')
  @include('partials.franchise.faq')
  @include('partials.franchise.apply-form')
  @include('partials.franchise.footer')
  @include('partials.franchise.booking-modal')
  @include('partials.home.bottom-nav')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.addEventListener('scroll', () => document.getElementById('topnav').classList.toggle('scrolled', scrollY > 60));
    const ro = new IntersectionObserver(es => es.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); ro.unobserve(e.target); } }), { threshold: .12 });
    document.querySelectorAll('.rv').forEach(el => ro.observe(el));

    @if ($errors->has('login'))
      new bootstrap.Modal(document.getElementById('flm')).show();
    @endif
    @if ($errors->has('password') || $errors->has('email') && !$errors->has('login'))
      new bootstrap.Modal(document.getElementById('fbm')).show();
    @endif
  </script>
  @include('partials.theme-toggle-script')
</body>
</html>
