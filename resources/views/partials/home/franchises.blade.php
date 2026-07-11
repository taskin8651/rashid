  <!-- FRANCHISE NETWORK (server-rendered from franchise_bookings) -->
  <section class="sec sec-alt" id="franchises">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl"><i class="bi bi-patch-check-fill me-1"></i>Our Network</div>
        <h2 class="sec-h">Franchise Centers Across <em>India</em></h2>
        <p class="sec-sub mx-auto">Every center is verified and trained on the same R-Tech curriculum — pick your city and explore the courses running there.</p>
      </div>

      @if ($franchises->count())
        <div class="fr-stats rv">
          <div><div class="stat-n">{{ $franchises->count() }}</div><div class="stat-l">Franchise Centers</div></div>
          <div><div class="stat-n">{{ $franchises->pluck('city')->unique()->count() }}</div><div class="stat-l">Cities</div></div>
          <div><div class="stat-n">{{ $franchises->sum('courses_count') }}</div><div class="stat-l">Courses Running</div></div>
        </div>

        <div class="row g-4">
          @foreach ($franchises as $fr)
            <div class="col-md-6 col-lg-4 rv">
              <div class="fr-card">
                <div class="fr-corner-ribbon"><span><i class="bi bi-award-fill"></i>Verified</span></div>

                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="fr-avatar"><i class="bi bi-building-fill-check"></i></div>
                  <div>
                    <h5 class="fr-city">{{ $fr->city }}</h5>
                    <div class="fr-owner">R-Tech Computer &middot; Est. {{ $fr->created_at->format('Y') }}</div>
                  </div>
                </div>

                <div class="fr-progress">
                  <div class="fr-progress-bar"><span style="width:{{ (($fr->stageIndex() + 1) / count(\App\Models\FranchiseBooking::STAGES)) * 100 }}%"></span></div>
                  <div class="fr-progress-lbl">{{ $fr->stageLabel() }}</div>
                </div>

                <div class="fr-card-stats">
                  <div><i class="bi bi-mortarboard-fill"></i><strong>{{ $fr->courses_count }}</strong> {{ \Illuminate\Support\Str::plural('Course', $fr->courses_count) }}</div>
                  <div><i class="bi bi-people-fill"></i><strong>{{ $fr->enrollments_count }}</strong> {{ \Illuminate\Support\Str::plural('Student', $fr->enrollments_count) }}</div>
                </div>

                <div class="fr-card-ft">
                  <div class="fr-price">
                    @if ($fr->min_course_price)
                      Courses from <strong>₹{{ number_format($fr->min_course_price, 0) }}</strong>
                    @else
                      <span class="text-muted">Courses launching soon</span>
                    @endif
                  </div>
                  <a class="fr-more" href="{{ route('franchises.show', $fr) }}">View Courses<i class="bi bi-arrow-right"></i></a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="fr-empty rv">
          <i class="bi bi-buildings"></i>
          <h5>Our First Franchise Centers Are Launching Soon</h5>
          <p>Want to bring R-Tech Computer to your city?</p>
          <a href="{{ route('franchise') }}" class="btn-p"><i class="bi bi-flag-fill"></i>Explore Franchise</a>
        </div>
      @endif
    </div>
  </section>
