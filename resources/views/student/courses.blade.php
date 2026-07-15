@extends('layouts.student')

@section('title', 'My Courses')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-mortarboard-fill"></i>My Learning</div>
    <h4>My Courses</h4>
    <p>Pick up where you left off, or start a new lesson.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['total'] }}</b><span>{{ \Illuminate\Support\Str::plural('Course', $stats['total']) }}</span></div>
      <div><b>{{ $stats['in_progress'] }}</b><span>In Progress</span></div>
      <div><b>{{ $stats['completed'] }}</b><span>Completed</span></div>
      <div><b>{{ $stats['certificates'] }}</b><span>{{ \Illuminate\Support\Str::plural('Certificate', $stats['certificates']) }}</span></div>
    </div>
  </div>

  @php
    $catColors = [
      'web-development' => ['59,130,246', '💻'],
      'graphic-design' => ['168,85,247', '🎨'],
      'digital-marketing' => ['16,185,129', '📈'],
      'video-editing' => ['249,115,22', '🎬'],
    ];
  @endphp

  <div class="row g-4">
    @forelse ($myCourses as $mc)
      @php [$catRgb, $catIcon] = $catColors[$mc['course']->category->slug ?? ''] ?? ['37,99,235', '🎓']; @endphp
      <div class="col-md-6 col-lg-4">
        <div class="mcc">
          <div class="cthumb" style="background:linear-gradient(135deg, rgba({{ $catRgb }},.9), rgba({{ $catRgb }},.55)), linear-gradient(135deg,#0d1e3c,#142a52)">{{ $catIcon }}</div>
          <div class="cinfo">
            <div class="d-flex justify-content-between mb-1"><h6 style="font-size:15px;font-weight:700;margin:0">{{ $mc['course']->name }}</h6>
              @if ($mc['percent_complete'] >= 100)
                <span style="font-size:11px;background:rgba(184,134,11,.15);color:#b8860b;padding:2px 8px;border-radius:8px;border:1px solid rgba(184,134,11,.28);white-space:nowrap"><i class="bi bi-trophy-fill"></i> Completed</span>
              @else
                <span style="font-size:11px;background:rgba(40,180,90,.15);color:var(--ok);padding:2px 8px;border-radius:8px;border:1px solid rgba(125,220,160,.28)">Active</span>
              @endif
            </div>
            <p style="font-size:12px;color:var(--muted);margin:4px 0 10px">{{ $mc['course']->category->name ?? 'Course' }} &middot; {{ $mc['percent_complete'] }}% Completed &middot; {{ $mc['videos_watched'] }}/{{ $mc['total_videos'] }} Videos</p>
            <div class="ptrack"><div class="pfill" style="width:{{ $mc['percent_complete'] }}%"></div></div>
            @if ($mc['total_videos'] > 0)
              <a href="{{ route('student.courses.learn', $mc['course']) }}" class="bcontinue" style="text-decoration:none;text-align:center;display:block"><i class="bi bi-play-circle-fill me-1"></i>{{ $mc['percent_complete'] > 0 ? 'Continue Learning' : 'Start Learning' }}</a>
            @else
              <p style="font-size:11px;color:var(--muted);margin:12px 0 0;text-align:center">Videos coming soon</p>
            @endif
            @if ($mc['percent_complete'] >= 100)
              <a href="{{ route('student.certificates.index') }}" class="bcontinue" style="text-decoration:none;text-align:center;display:block;margin-top:8px;background:linear-gradient(135deg,#b8860b,#d4a017)"><i class="bi bi-award-fill me-1"></i>View Certificate</a>
            @endif

            @if ($mc['review'])
              <p style="font-size:11px;color:var(--muted);margin:10px 0 0;text-align:center">
                <i class="bi bi-star-fill" style="color:var(--warn)"></i> You rated {{ $mc['review']->rating }}/5 &middot;
                <span class="badge-rt {{ $mc['review']->status === 'approved' ? 'bg-active' : ($mc['review']->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($mc['review']->status) }}</span>
              </p>
            @else
              <button type="button" class="bcontinue" style="margin-top:8px;background:none;border:1px solid var(--border);color:var(--text)" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $mc['course']->id }}"><i class="bi bi-star me-1"></i>Rate this Course</button>
            @endif
          </div>
        </div>
      </div>

      <div class="modal fade" id="reviewModal{{ $mc['course']->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Rate {{ $mc['course']->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('student.courses.review.store', $mc['course']) }}">
              @csrf
              <div class="modal-body">
                <div class="star-picker mb-3">
                  @for ($i = 5; $i >= 1; $i--)
                    <input type="radio" name="rating" id="star{{ $mc['course']->id }}_{{ $i }}" value="{{ $i }}" {{ $i === 5 ? 'required' : '' }}>
                    <label for="star{{ $mc['course']->id }}_{{ $i }}"><i class="bi bi-star-fill"></i></label>
                  @endfor
                </div>
                <label class="flbl">Your Review (optional)</label>
                <textarea class="fctrl" name="comment" rows="3" placeholder="Share your experience with this course…"></textarea>
              </div>
              <div class="modal-footer">
                <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="bsave">Submit Review</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card-rt text-center" style="padding:40px 24px">
          <i class="bi bi-collection-play" style="font-size:28px;color:var(--orange);margin-bottom:10px;display:inline-block"></i>
          <p style="font-size:13px;color:var(--muted);margin:0">You are not enrolled in any course yet. <a href="{{ route('courses') }}" style="color:var(--orange)">Browse courses →</a></p>
        </div>
      </div>
    @endforelse
  </div>
@endsection
