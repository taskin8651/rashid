  @if ($featuredPlacements->isNotEmpty())
    <!-- PLACEMENTS -->
    <section class="sec">
      <div class="container">
        <div class="text-center mb-5 rv">
          <div class="sec-lbl">Career Outcomes</div>
          <h2 class="sec-h">Where Our Students <em>Work</em></h2>
          <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">Real students, real jobs — a look at the companies hiring R-Tech Computer graduates.</p>
        </div>
        <div class="row g-4">
          @foreach ($featuredPlacements as $p)
            <div class="col-md-6 col-lg-4 rv">
              @include('partials.placement-card', ['p' => $p, 'index' => $loop->index])
            </div>
          @endforeach
        </div>
        <div class="text-center mt-4 rv">
          <a href="{{ route('placements') }}" class="btn-g"><i class="bi bi-briefcase-fill"></i>View All Placements</a>
        </div>
      </div>
    </section>
  @endif
