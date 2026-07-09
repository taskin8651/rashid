  <!-- FREE DEMO -->
  <section class="sec" id="demos">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Free Demo</div>
        <h2 class="sec-h">Watch Before You <em>Decide</em></h2>
        <p class="sec-sub mx-auto">Free demo videos and full curriculum preview per course. Premium videos unlock after enrollment.</p>
      </div>

      <div class="demo-stats rv">
        <div class="demo-stat">
          <i class="bi bi-play-circle-fill"></i>
          <div><b>{{ $totalDemoVideos }}</b><span>Free Demo Videos</span></div>
        </div>
        <div class="demo-stat">
          <i class="bi bi-collection-play-fill"></i>
          <div><b>{{ $categories->flatMap->courses->count() }}</b><span>Courses Covered</span></div>
        </div>
        <div class="demo-stat">
          <i class="bi bi-tags-fill"></i>
          <div><b>{{ $categories->count() }}</b><span>Categories</span></div>
        </div>
      </div>

      @if ($categories->count() > 1)
        <div class="demo-tabs rv">
          <button class="fb act" type="button" onclick="demoFilter('all', this)">All Categories</button>
          @foreach ($categories as $category)
            <button class="fb" type="button" onclick="demoFilter('cat-{{ $category->id }}', this)">{{ $category->icon }} {{ $category->name }}</button>
          @endforeach
        </div>
      @endif

      @php
        $catColors = [
          'web-development' => '59,130,246',
          'graphic-design' => '168,85,247',
          'digital-marketing' => '16,185,129',
          'video-editing' => '249,115,22',
        ];
      @endphp

      @forelse ($categories as $category)
        @php
          $catRgb = $catColors[$category->slug] ?? null;
          $demoVideos = $category->courses->flatMap->videos;
          $moduleChips = $category->courses->flatMap->modules;
        @endphp
        <div class="demo-spotlight rv demo-group" data-cat="cat-{{ $category->id }}" @if($catRgb) style="--cat-rgb:{{ $catRgb }}" @endif>
          <div class="demo-spotlight-head">
            <div class="demo-spotlight-icon">{{ $category->icon ?: '🎓' }}</div>
            <div>
              <h3>{{ $category->name }}</h3>
              <p>{{ $category->courses->count() }} course{{ $category->courses->count() === 1 ? '' : 's' }} · {{ $demoVideos->count() }} free demo{{ $demoVideos->count() === 1 ? '' : 's' }}</p>
            </div>
          </div>
          <div class="demo-spotlight-body">
            @if ($demoVideos->isNotEmpty())
              <div class="row g-3 mb-2">
                @foreach ($demoVideos as $video)
                  <div class="col-md-6 col-lg-4">
                    <div class="dc" data-bs-toggle="modal" data-bs-target="#demoM">
                      <div class="dthumb" style="background:linear-gradient(135deg, rgba({{ $catRgb ?? '37,99,235' }},.9), rgba({{ $catRgb ?? '37,99,235' }},.4)), linear-gradient(135deg,#050d1f,#0f2147)">
                        <span class="free-chip">FREE</span>
                        <div class="pc"><i class="bi bi-play-fill"></i></div>
                      </div>
                      <div class="di">
                        <h6>{{ $video->title }}</h6>
                        <span style="font-size:11px;color:var(--orange)"><i class="bi bi-clock me-1"></i>{{ gmdate('i:s', $video->duration_seconds) }}</span>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="demo-empty">
                <i class="bi bi-camera-reels-fill"></i>
                <div>
                  <b>Video previews launching soon</b>
                  <span>Meanwhile, here's exactly what this course covers</span>
                </div>
              </div>
            @endif

            @if ($moduleChips->isNotEmpty())
              <div class="mt-4">
                <div class="demo-modules-lbl">What You'll Learn</div>
                <div class="demo-modules">
                  @foreach ($moduleChips as $module)
                    <div class="demo-module-card">
                      <div class="demo-module-num"><i class="bi bi-check-lg"></i></div>
                      <span>{{ $module->title }}</span>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        </div>
      @empty
        <p style="font-size:13px;color:var(--muted)" class="text-center">No categories available yet.</p>
      @endforelse

      <div class="text-center mt-5 rv">
        <p style="font-size:13px;color:var(--muted);margin-bottom:16px"><i class="bi bi-lock-fill me-1" style="color:var(--orange)"></i>Login or enroll to unlock all premium course videos</p>
        <button class="btn-p" type="button" data-bs-toggle="modal" data-bs-target="#lm"><i class="bi bi-play-circle-fill"></i>Access All Videos</button>
      </div>
    </div>
  </section>

  <script>
    function demoFilter(cat, el) {
      document.querySelectorAll('.demo-tabs .fb').forEach(b => b.classList.remove('act'));
      el.classList.add('act');
      document.querySelectorAll('.demo-group').forEach(g => {
        g.style.display = (cat === 'all' || g.dataset.cat === cat) ? '' : 'none';
      });
    }
  </script>
