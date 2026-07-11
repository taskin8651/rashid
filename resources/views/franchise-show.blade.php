@extends('layouts.site')

@section('title', $franchiseBooking->city . ' Franchise')

@section('content')
  <!-- FRANCHISE HERO -->
  <section class="hero hero--carousel" style="min-height:64vh;padding:112px 0 56px">
    <div class="hero-photo active" style="background-image:url('{{ asset('assets/img/hero/franchise-hero.jpg') }}')"></div>
    <div class="hero-scrim"></div>
    <div class="hero-grid"></div>
    <div class="container position-relative">
      <div class="row align-items-center g-5">
        <div class="col-lg-7 rv">
          <a href="{{ route('home') }}#franchises" style="font-size:12px;font-weight:600;color:rgba(255,255,255,.7);text-decoration:none;display:inline-flex;align-items:center;gap:6px;margin-bottom:18px"><i class="bi bi-arrow-left"></i>All Franchise Centers</a>
          <div class="hero-pill"><span></span>Verified Franchise Partner</div>
          <h1>R-Tech Computer <em>{{ $franchiseBooking->city }}</em></h1>
          <p class="sub">Operated by {{ $franchiseBooking->name }} &middot; Same certified curriculum, faculty standards and placement support as every R-Tech center.</p>
          <div class="d-flex flex-wrap gap-3">
            @if ($franchiseBooking->courses_count)
              <a class="btn-p" href="#courses"><i class="bi bi-mortarboard-fill"></i>Explore Courses</a>
            @endif
            <a class="btn-g" style="color:#fff" href="tel:{{ $franchiseBooking->phone }}"><i class="bi bi-telephone-fill"></i>Call This Center</a>
          </div>
          <div class="hero-stats">
            <div><div class="stat-n">{{ $franchiseBooking->courses_count }}</div><div class="stat-l">{{ \Illuminate\Support\Str::plural('Course', $franchiseBooking->courses_count) }}</div></div>
            <div><div class="stat-n">{{ $franchiseBooking->enrollments_count }}</div><div class="stat-l">{{ \Illuminate\Support\Str::plural('Student', $franchiseBooking->enrollments_count) }}</div></div>
            <div><div class="stat-n">{{ $franchiseBooking->created_at->format('Y') }}</div><div class="stat-l">Established</div></div>
            <div><div class="stat-n">100%</div><div class="stat-l">Certified</div></div>
          </div>
        </div>
        <div class="col-lg-5 rv">
          <div class="hero-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <div style="font-size:15px;font-weight:700">Center Snapshot</div>
                <div style="font-size:11px;color:var(--muted)">Live franchise details</div>
              </div>
              <span class="fr-tag {{ $franchiseBooking->stage === 'launched' ? 'fr-tag-ok' : 'fr-tag-warn' }}"><i class="bi bi-circle-fill"></i>{{ $franchiseBooking->stageLabel() }}</span>
            </div>
            <div class="hc-row" style="cursor:default">
              <div class="hc-icon" style="background:rgba(30,100,220,.15)">🏙️</div>
              <div><div class="hc-name">{{ $franchiseBooking->city }}</div><div class="hc-sub">Center location</div></div>
              <div class="hc-price">Est. {{ $franchiseBooking->created_at->format('Y') }}</div>
            </div>
            <div class="hc-row" style="cursor:default">
              <div class="hc-icon" style="background:rgba(40,180,90,.15)">🎓</div>
              <div><div class="hc-name">{{ $franchiseBooking->courses_count }} {{ \Illuminate\Support\Str::plural('Course', $franchiseBooking->courses_count) }}</div><div class="hc-sub">Running at this center</div></div>
              @if ($minCoursePrice)
                <div class="hc-price">₹{{ number_format($minCoursePrice, 0) }}</div>
              @endif
            </div>
            <div class="hc-row" style="cursor:default">
              <div class="hc-icon" style="background:rgba(245,166,35,.15)">👥</div>
              <div><div class="hc-name">{{ $franchiseBooking->enrollments_count }} {{ \Illuminate\Support\Str::plural('Student', $franchiseBooking->enrollments_count) }}</div><div class="hc-sub">Enrolled so far</div></div>
            </div>
            <a class="btn-p w-100 justify-content-center mt-2" style="padding:12px" href="tel:{{ $franchiseBooking->phone }}"><i class="bi bi-telephone-fill"></i>{{ $franchiseBooking->phone }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FRANCHISE JOURNEY -->
  <section style="padding:36px 0 0">
    <div class="container">
      <div class="fr-journey rv">
        <h6><i class="bi bi-signpost-split-fill me-1"></i>Franchise Journey</h6>
        <div class="fr-steps">
          @foreach (\App\Models\FranchiseBooking::STAGES as $key => $label)
            <div class="fr-step {{ $loop->index <= $franchiseBooking->stageIndex() ? 'done' : '' }} {{ $key === $franchiseBooking->stage ? 'current' : '' }}">
              <div class="fr-step-dot">
                @if ($loop->index < $franchiseBooking->stageIndex())
                  <i class="bi bi-check-lg"></i>
                @else
                  {{ $loop->iteration }}
                @endif
              </div>
              <div class="fr-step-lbl">{{ $label }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <!-- WHAT'S THE SAME EVERYWHERE -->
  <section class="sec">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Franchise Standard</div>
        <h2 class="sec-h">Same Quality, <em>Every City</em></h2>
        <p class="sec-sub mx-auto">Whichever R-Tech center you join, you get the exact same training experience.</p>
      </div>
      <div class="row g-3">
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-journal-check"></i></div><h5>Certified Curriculum</h5><p>Same syllabus and materials as every R-Tech center</p></div></div>
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-person-video3"></i></div><h5>Trained Faculty</h5><p>Instructors certified directly by R-Tech HQ</p></div></div>
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-folder2-open"></i></div><h5>Live Projects</h5><p>Hands-on, portfolio-worthy project work</p></div></div>
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-briefcase-fill"></i></div><h5>Placement Support</h5><p>Job assistance on course completion</p></div></div>
      </div>
    </div>
  </section>

  <!-- FRANCHISE COURSES -->
  <section class="sec sec-alt" id="courses">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Available Here</div>
        <h2 class="sec-h">Courses at This <em>Center</em></h2>
        <p class="sec-sub mx-auto">Practical, job-oriented training with live projects and placement support.</p>
      </div>

      @if ($franchiseBooking->courses->count())
        <div class="row g-4">
          @foreach ($franchiseBooking->courses as $course)
            <div class="col-lg-6 rv">
              <div class="cc">
                <div class="cc-hd">
                  <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                    <div class="cc-badge bw" style="margin-bottom:0">{{ $course->category->name ?? 'Course' }}</div>
                    <span style="font-size:10px;background:rgba(var(--accent-rgb),.12);color:var(--orange);border:1px solid rgba(var(--accent-rgb),.25);padding:3px 9px;border-radius:10px;font-weight:700">🏙 {{ $franchiseBooking->city }} Franchise</span>
                  </div>
                  <h3>{{ $course->name }}</h3>
                  <p>{{ $course->description }}</p>
                  <div class="cc-meta">
                    <span><i class="bi bi-clock-fill"></i>{{ $course->duration_text }}</span>
                    @if ($course->modules->count())
                      <span><i class="bi bi-collection-play-fill"></i>{{ $course->modules->count() }} {{ \Illuminate\Support\Str::plural('Module', $course->modules->count()) }}</span>
                    @endif
                    @if ($course->rating)
                      <span><i class="bi bi-star-fill" style="color:var(--warn)"></i>{{ $course->rating }} ({{ $course->rating_count }})</span>
                    @endif
                  </div>
                </div>
                <div class="cc-ft">
                  <div class="price-big">₹{{ number_format($course->price, 0) }}<span class="price-sub">All-inclusive · Certificate</span></div>
                  <div>
                    @auth
                      <a class="btn-enr" href="{{ route('enroll.create', $course) }}">Enroll Now</a>
                    @else
                      <button class="btn-enr" type="button" data-bs-toggle="modal" data-bs-target="#lm">Enroll Now</button>
                    @endauth
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="fr-empty rv">
          <i class="bi bi-mortarboard"></i>
          <h5>Courses Launching Soon at This Center</h5>
          <p>This franchise is being onboarded &mdash; check back shortly or explore our other courses.</p>
          <a href="{{ route('courses') }}" class="btn-p"><i class="bi bi-lightning-fill"></i>Browse All Courses</a>
        </div>
      @endif
    </div>
  </section>

  <!-- CONTACT THIS CENTER -->
  <section style="padding:0 0 88px">
    <div class="container">
      <div class="fr-contact-cta rv">
        <div>
          <h5><i class="bi bi-headset me-2"></i>Have Questions About This Center?</h5>
          <p>Talk to the {{ $franchiseBooking->city }} team directly for batch timings and seat availability.</p>
        </div>
        <a class="fr-call-btn" href="tel:{{ $franchiseBooking->phone }}"><i class="bi bi-telephone-fill"></i>Call {{ $franchiseBooking->phone }}</a>
      </div>
    </div>
  </section>
@endsection
