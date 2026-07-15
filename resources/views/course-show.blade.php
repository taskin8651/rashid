@extends('layouts.site')

@section('title', $course->name)
@section('meta_title', $course->seoTitle())
@section('meta_description', $course->seoDescription())
@if ($course->seoImage())
  @section('meta_image', $course->seoImage())
@endif

@section('content')
  <section class="sec" style="padding-bottom:0">
    <div class="container">
      <a href="{{ route('courses') }}" style="color:var(--muted);text-decoration:none;font-size:12.5px;display:inline-block;margin-bottom:16px"><i class="bi bi-arrow-left me-1"></i>Back to Courses</a>

      <div class="row g-5 align-items-start">
        <div class="col-lg-7 rv">
          <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
            <div class="cc-badge bw" style="margin-bottom:0">{{ $course->category->name ?? 'Course' }}</div>
            @if ($course->franchise_booking_id)
              <span style="font-size:10px;background:rgba(var(--accent-rgb),.12);color:var(--orange);border:1px solid rgba(var(--accent-rgb),.25);padding:3px 9px;border-radius:10px;font-weight:700">🏙 {{ $course->franchiseBooking->city }} Franchise</span>
            @else
              <span style="font-size:10px;background:rgba(40,180,90,.12);color:var(--ok);border:1px solid rgba(125,220,160,.25);padding:3px 9px;border-radius:10px;font-weight:700">R-Tech Official</span>
            @endif
          </div>

          <h1 class="sec-h" style="text-align:left;font-size:clamp(26px,4vw,38px);margin-bottom:14px">{{ $course->name }}</h1>
          <p style="font-size:14.5px;color:var(--muted);line-height:1.7;margin-bottom:18px">{{ $course->description }}</p>

          <div class="d-flex flex-wrap gap-4 mb-2" style="font-size:13px;color:var(--muted)">
            <span><i class="bi bi-clock-fill me-1" style="color:var(--orange)"></i>{{ $course->duration_text }}</span>
            @if ($course->averageRating())
              <span><i class="bi bi-star-fill me-1" style="color:var(--warn)"></i>{{ $course->averageRating() }} ({{ $course->reviewCount() }} {{ \Illuminate\Support\Str::plural('review', $course->reviewCount()) }})</span>
            @endif
            <span><i class="bi bi-mortarboard-fill me-1" style="color:var(--orange)"></i>{{ $course->enrollments()->whereIn('status', ['paid','completed'])->count() }} Enrolled</span>
          </div>
        </div>

        <div class="col-lg-5 rv">
          <div class="cbox">
            @if ($course->thumbnailUrl())
              <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->name }}" style="width:100%;border-radius:14px;margin-bottom:18px;object-fit:cover;max-height:220px">
            @endif
            <div class="price-big" style="margin-bottom:16px">₹{{ number_format($course->price, 0) }}<span class="price-sub">All-inclusive · Certificate on completion</span></div>

            @if ($isEnrolled)
              <a href="{{ route('student.courses.learn', $course) }}" class="btn-enr w-100 justify-content-center" style="text-decoration:none"><i class="bi bi-play-circle-fill me-1"></i>Continue Learning</a>
            @elseif (auth()->check())
              <a href="{{ route('enroll.create', $course) }}" class="btn-enr w-100 justify-content-center" style="text-decoration:none"><i class="bi bi-lightning-fill me-1"></i>Enroll Now</a>
            @else
              <button class="btn-enr w-100 justify-content-center" type="button" data-bs-toggle="modal" data-bs-target="#lm"><i class="bi bi-lightning-fill me-1"></i>Enroll Now</button>
            @endif

            <div class="mt-3" style="font-size:12px;color:var(--muted)">
              <div class="mb-1"><i class="bi bi-check-circle-fill me-1" style="color:var(--ok)"></i>Lifetime access to recordings</div>
              <div class="mb-1"><i class="bi bi-check-circle-fill me-1" style="color:var(--ok)"></i>Live projects &amp; placement support</div>
              <div><i class="bi bi-check-circle-fill me-1" style="color:var(--ok)"></i>Verified certificate on completion</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  @if ($course->modules->isNotEmpty())
    <section class="sec">
      <div class="container">
        <div class="mb-4 rv">
          <div class="sec-lbl">Curriculum</div>
          <h2 class="sec-h" style="text-align:left">What You'll <em>Learn</em></h2>
        </div>
        <div class="row g-3 rv">
          @foreach ($course->modules as $module)
            <div class="col-md-6">
              <div class="demo-module-card" style="align-items:flex-start">
                <div class="demo-module-num"><i class="bi bi-check-lg"></i></div>
                <div>
                  <span style="display:block;font-weight:700">{{ $module->title }}</span>
                  @if ($module->videos->count())
                    <span style="font-size:11.5px;color:var(--muted)">{{ $module->videos->count() }} {{ \Illuminate\Support\Str::plural('lesson', $module->videos->count()) }}</span>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  @if ($demoVideos->isNotEmpty())
    <section class="sec" style="padding-top:0">
      <div class="container">
        <div class="mb-4 rv">
          <div class="sec-lbl">Preview</div>
          <h2 class="sec-h" style="text-align:left">Free Demo <em>Videos</em></h2>
        </div>
        <div class="row g-3 rv">
          @foreach ($demoVideos as $video)
            <div class="col-md-6 col-lg-4">
              <div class="dc" data-bs-toggle="modal" data-bs-target="#demoM" data-src="{{ $video->fileUrl() }}" data-title="{{ $video->title }}">
                <div class="dthumb" style="background:var(--grad)">
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
      </div>
    </section>
  @endif

  @if ($batches->isNotEmpty())
    <section class="sec" style="padding-top:0">
      <div class="container">
        <div class="mb-4 rv">
          <div class="sec-lbl">Schedule</div>
          <h2 class="sec-h" style="text-align:left">Upcoming <em>Batches</em></h2>
        </div>
        <div class="row g-3 rv">
          @foreach ($batches as $batch)
            <div class="col-md-6 col-lg-4">
              <div class="card-rt">
                <h6 style="font-size:14px;font-weight:700;margin-bottom:6px">{{ $batch->label }}</h6>
                <p style="font-size:12.5px;color:var(--muted);margin-bottom:4px"><i class="bi bi-calendar-event me-1"></i>Starts {{ $batch->start_date->format('d M Y') }}</p>
                <p style="font-size:12.5px;color:var(--muted);margin-bottom:0"><i class="bi bi-clock me-1"></i>{{ $batch->schedule_text }} &middot; {{ ucfirst($batch->mode) }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif

  <section class="sec" style="padding-top:0">
    <div class="container" style="max-width:820px">
      <div class="mb-4 rv">
        <div class="sec-lbl">Student Feedback</div>
        <h2 class="sec-h" style="text-align:left">Reviews &amp; <em>Ratings</em></h2>
      </div>

      @if ($course->reviewCount())
        <div class="d-flex align-items-center gap-4 mb-4 rv flex-wrap">
          <div class="text-center">
            <div style="font-size:40px;font-weight:800;font-family:'Space Grotesk',sans-serif;color:var(--text)">{{ $course->averageRating() }}</div>
            <div style="color:var(--warn);font-size:14px">{{ str_repeat('★', round($course->averageRating())) }}{{ str_repeat('☆', 5 - round($course->averageRating())) }}</div>
            <div style="font-size:11.5px;color:var(--muted)">{{ $course->reviewCount() }} {{ \Illuminate\Support\Str::plural('review', $course->reviewCount()) }}</div>
          </div>
          <div class="flex-grow-1" style="min-width:200px">
            @foreach ($ratingBreakdown as $star => $count)
              <div class="d-flex align-items-center gap-2 mb-1">
                <span style="font-size:11px;color:var(--muted);width:34px">{{ $star }} ★</span>
                <div class="ptrack" style="flex:1"><div class="pfill" style="width:{{ $course->reviewCount() ? round($count / $course->reviewCount() * 100) : 0 }}%"></div></div>
                <span style="font-size:11px;color:var(--muted);width:20px">{{ $count }}</span>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      @forelse ($reviews as $review)
        <div class="rv" style="border-bottom:1px solid var(--border);padding:16px 0">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <div class="d-flex align-items-center gap-2">
              <div class="ra" style="background:var(--grad);width:34px;height:34px;font-size:13px">{{ strtoupper(substr($review->user->name, 0, 1)) }}</div>
              <div>
                <div style="font-size:13px;font-weight:700;color:var(--text)">{{ $review->user->name }}</div>
                <div style="font-size:11px;color:var(--warn)">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</div>
              </div>
            </div>
            <span style="font-size:11px;color:var(--muted)">{{ $review->created_at->diffForHumans() }}</span>
          </div>
          @if ($review->comment)
            <p style="font-size:13px;color:var(--muted);margin:8px 0 0 44px">{{ $review->comment }}</p>
          @endif
        </div>
      @empty
        <p style="font-size:13px;color:var(--muted)" class="rv">No reviews yet — be the first to complete and rate this course!</p>
      @endforelse
    </div>
  </section>

  @if ($relatedCourses->isNotEmpty())
    <section class="sec" style="padding-top:0">
      <div class="container">
        <div class="mb-4 rv">
          <div class="sec-lbl">Explore More</div>
          <h2 class="sec-h" style="text-align:left">Related <em>Courses</em></h2>
        </div>
        <div class="row g-3 rv">
          @foreach ($relatedCourses as $rc)
            <div class="col-md-4">
              <a href="{{ route('courses.show', $rc) }}" class="card-rt d-block" style="text-decoration:none">
                <h6 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:6px">{{ $rc->name }}</h6>
                <p style="font-size:12.5px;color:var(--muted);margin:0">₹{{ number_format($rc->price, 0) }} &middot; {{ $rc->duration_text }}</p>
              </a>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
@endsection
