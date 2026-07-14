<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>@yield('title', 'R-Tech Computer') | Skill Se Placement Tak</title>
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
  @if ($errors->any() && !$errors->has('coupon_code') && !$errors->has('payment'))
    <div id="tw"><div class="tr err"><i class="bi bi-exclamation-circle-fill"></i>{{ $errors->first() }}</div></div>
  @endif

  @include('partials.home.nav')

  @yield('content')

  @include('partials.home.footer')
  @include('partials.home.login-modal')
  @include('partials.home.bottom-nav')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.addEventListener('scroll', () => document.getElementById('topnav').classList.toggle('scrolled', scrollY > 60));

    const ro = new IntersectionObserver(es => es.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('in'); ro.unobserve(e.target); }
    }), { threshold: .12 });
    document.querySelectorAll('.rv').forEach(el => ro.observe(el));

    @if ($errors->has('login') || $errors->has('name') || $errors->has('email') || $errors->has('password'))
      new bootstrap.Modal(document.getElementById('lm')).show();
    @endif

    @if (session('show_forgot_modal'))
      new bootstrap.Modal(document.getElementById('lm')).show();
      document.getElementById('forgotTabTrigger').click();
    @endif

    const demoM = document.getElementById('demoM');
    if (demoM) {
      const demoPlayer = document.getElementById('demoMPlayer');
      demoM.addEventListener('show.bs.modal', e => {
        const card = e.relatedTarget;
        demoPlayer.src = card?.dataset.src || '';
        document.getElementById('demoMTitle').textContent = card?.dataset.title || 'Free Demo Video';
      });
      demoM.addEventListener('hidden.bs.modal', () => {
        demoPlayer.pause();
        demoPlayer.removeAttribute('src');
        demoPlayer.load();
      });
    }
  </script>
  @include('partials.theme-toggle-script')
  @yield('scripts')
</body>
</html>
