  <!-- HERO -->
  <section class="hero hero--carousel" id="home">
    <div class="hero-photo active" style="background-image:url('{{ asset('assets/img/hero/home-hero.jpg') }}')"></div>
    <div class="hero-photo" style="background-image:url('{{ asset('assets/img/hero/home-hero-2.jpg') }}')"></div>
    <div class="hero-photo" style="background-image:url('{{ asset('assets/img/hero/home-hero-3.jpg') }}')"></div>
    <div class="hero-scrim"></div>
    <div class="hero-grid"></div>
    <div class="hero-dots">
      <button class="hero-dot active" type="button" data-idx="0" aria-label="Slide 1"></button>
      <button class="hero-dot" type="button" data-idx="1" aria-label="Slide 2"></button>
      <button class="hero-dot" type="button" data-idx="2" aria-label="Slide 3"></button>
    </div>
    <div class="container position-relative">
      <div class="row align-items-center g-5">
        <div class="col-lg-6">
          <div class="hero-pill"><span></span>India's Best Digital Skills Training</div>
          <h1>Learn <em>High-Demand</em><br>Digital Skills</h1>
          <p class="sub">Join R-Tech Computer and build your career with practical training. 100% job-oriented courses with live projects and placement support.</p>
          <div class="d-flex flex-wrap gap-3">
            <a class="btn-p" href="{{ route('courses') }}"><i class="bi bi-lightning-fill"></i>Enroll Now</a>
            <button class="btn-g" type="button" data-bs-toggle="modal" data-bs-target="#demoM"><i class="bi bi-play-circle-fill"></i>Watch Free Demo</button>
          </div>
          <div class="hero-stats">
            <div><div class="stat-n">500+</div><div class="stat-l">Students</div></div>
            <div><div class="stat-n">{{ $courses->count() }}</div><div class="stat-l">Courses</div></div>
            <div><div class="stat-n">95%</div><div class="stat-l">Placement</div></div>
            <div><div class="stat-n">₹15K</div><div class="stat-l">Per Course</div></div>
          </div>
        </div>
        <div class="col-lg-6 fcr" style="position:relative">
          <div class="hero-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <div style="font-size:15px;font-weight:700">Our Courses</div>
                <div style="font-size:11px;color:var(--muted)">Choose your path</div>
              </div>
            </div>
            @foreach ($courses as $c)
              <div class="hc-row" onclick="location.href='{{ route('courses') }}'">
                <div class="hc-icon" style="background:rgba(30,100,220,.15)">🎓</div>
                <div>
                  <div class="hc-name">{{ $c->name }}</div>
                  <div class="hc-sub">{{ $c->category->name ?? '' }}</div>
                </div>
                <div class="hc-price">₹{{ number_format($c->price / 1000, 0) }}K</div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="f-strip">
    <div class="container">
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <div class="f-pill"><i class="bi bi-laptop-fill"></i>100% Practical Training</div>
        <div class="f-pill"><i class="bi bi-folder2-open"></i>Live Projects</div>
        <div class="f-pill"><i class="bi bi-patch-check-fill"></i>Certificate</div>
        <div class="f-pill"><i class="bi bi-briefcase-fill"></i>Job Assistance</div>
        <div class="f-pill"><i class="bi bi-infinity"></i>Lifetime Support</div>
      </div>
    </div>
  </div>

  <script>
    (function () {
      const photos = document.querySelectorAll('#home .hero-photo');
      const dots = document.querySelectorAll('#home .hero-dot');
      let idx = 0;
      let timer = setInterval(next, 5000);

      function show(i) {
        photos.forEach((p, n) => p.classList.toggle('active', n === i));
        dots.forEach((d, n) => d.classList.toggle('active', n === i));
        idx = i;
      }

      function next() {
        show((idx + 1) % photos.length);
      }

      dots.forEach(d => {
        d.addEventListener('click', () => {
          clearInterval(timer);
          show(parseInt(d.dataset.idx, 10));
          timer = setInterval(next, 5000);
        });
      });
    })();
  </script>
