  <!-- COURSES (server-rendered from the courses table) -->
  <section class="sec" id="courses">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Our Courses</div>
        <h2 class="sec-h">Choose Your <em>Career Path</em></h2>
        <p class="sec-sub mx-auto">Practical, job-oriented training with live projects and placement support.</p>
      </div>
      <div class="row g-4" id="cg">
        @foreach ($courses as $course)
          <div class="col-lg-6 ci rv">
            <div class="cc">
              <div class="cc-hd">
                <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
                  <div class="cc-badge bw" style="margin-bottom:0">{{ $course->category->name ?? 'Course' }}</div>
                  @if ($course->franchise_booking_id)
                    <span style="font-size:10px;background:rgba(var(--accent-rgb),.12);color:var(--orange);border:1px solid rgba(var(--accent-rgb),.25);padding:3px 9px;border-radius:10px;font-weight:700">🏙 {{ $course->franchiseBooking->city }} Franchise</span>
                  @else
                    <span style="font-size:10px;background:rgba(40,180,90,.12);color:var(--ok);border:1px solid rgba(125,220,160,.25);padding:3px 9px;border-radius:10px;font-weight:700">R-Tech Official</span>
                  @endif
                </div>
                <h3><a href="{{ route('courses.show', $course) }}" style="color:inherit;text-decoration:none">{{ $course->name }}</a></h3>
                <p>{{ $course->description }}</p>
                <div class="cc-meta">
                  <span><i class="bi bi-clock-fill"></i>{{ $course->duration_text }}</span>
                  @if ($course->averageRating())
                    <span><i class="bi bi-star-fill" style="color:var(--warn)"></i>{{ $course->averageRating() }} ({{ $course->reviewCount() }})</span>
                  @endif
                </div>
              </div>
              <div class="cc-ft">
                <div class="price-big">₹{{ number_format($course->price, 0) }}<span class="price-sub">All-inclusive · Certificate</span></div>
                <div class="d-flex gap-2">
                  <a class="btn-dm" href="{{ route('courses.show', $course) }}">Details</a>
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
    </div>
  </section>
