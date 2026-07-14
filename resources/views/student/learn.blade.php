@extends('layouts.student')

@section('title', $course->name)

@section('content')
  <div class="shead"><h4>{{ $course->name }}</h4><p><a href="{{ route('student.courses.index') }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to My Courses</a></p></div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card-rt mb-3" style="padding:0;overflow:hidden">
        <video id="player" src="{{ $currentVideo->fileUrl() }}" controls controlsList="nodownload noremoteplayback" disablePictureInPicture disableRemotePlayback oncontextmenu="return false" style="width:100%;display:block;background:#000;max-height:480px"></video>
      </div>
      <div class="card-rt">
        <h6 style="font-size:16px;font-weight:700;margin-bottom:4px">{{ $currentVideo->title }}</h6>
        <p style="font-size:12px;color:var(--muted);margin-bottom:10px">{{ gmdate('i:s', $currentVideo->duration_seconds) }} &middot; {{ $currentVideo->type === 'demo' ? 'Demo' : 'Premium' }}</p>
        <div class="ptrack mb-3"><div class="pfill" id="liveProgress" style="width:{{ in_array($currentVideo->id, $watchedIds) ? 100 : 0 }}%"></div></div>

        <form id="watchedForm" method="POST" action="{{ route('student.videos.watched', $currentVideo) }}">
          @csrf
          <input type="hidden" name="watched_seconds" id="watchedSecondsInput" value="0"/>
          @if ($nextVideo)
            <input type="hidden" name="next_video_id" value="{{ $nextVideo->id }}"/>
          @endif
          <button class="bsave" type="submit">
            @if (in_array($currentVideo->id, $watchedIds))
              <i class="bi bi-check-circle-fill me-1"></i>Watched
            @elseif ($nextVideo)
              <i class="bi bi-check2-circle me-1"></i>Mark Watched &amp; Next
            @else
              <i class="bi bi-trophy-fill me-1"></i>Mark Watched &amp; Finish
            @endif
          </button>
        </form>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card-rt">
        <div class="card-title">Course Content</div>
        @foreach ($allVideos as $video)
          <a href="{{ route('student.courses.learn', ['course' => $course, 'video' => $video->id]) }}" class="vit {{ $video->id === $currentVideo->id ? 'act' : '' }} {{ in_array($video->id, $watchedIds) ? 'done' : '' }}" style="text-decoration:none;color:inherit">
            <div class="vnum">
              @if (in_array($video->id, $watchedIds))
                <i class="bi bi-check-lg"></i>
              @else
                {{ $loop->iteration }}
              @endif
            </div>
            <div>
              <div class="vt">{{ $video->title }}</div>
              <div class="vd">{{ gmdate('i:s', $video->duration_seconds) }} &middot; {{ $video->type === 'demo' ? 'Demo' : 'Premium' }}</div>
            </div>
            <i class="bi bi-play-circle licon"></i>
          </a>
        @endforeach
      </div>

      <div class="card-rt mt-3">
        <div class="card-title">Course Resources</div>
        <a href="{{ route('student.courses.quiz.show', $course) }}" class="ov-action mb-2" style="text-decoration:none">
          <div class="ov-action-icon" style="background:rgba(37,99,235,.14);color:var(--orange)"><i class="bi bi-patch-question-fill"></i></div>
          <div><div class="ov-action-title">Take Quiz</div><div class="ov-action-sub">Test your knowledge</div></div>
        </a>
        <a href="{{ route('student.courses.assignments.index', $course) }}" class="ov-action mb-2" style="text-decoration:none">
          <div class="ov-action-icon" style="background:rgba(184,134,11,.14);color:#b8860b"><i class="bi bi-journal-text"></i></div>
          <div><div class="ov-action-title">Assignments</div><div class="ov-action-sub">Submit your work</div></div>
        </a>
        <a href="{{ route('student.courses.notes.index', $course) }}" class="ov-action" style="text-decoration:none">
          <div class="ov-action-icon" style="background:rgba(40,180,90,.14);color:var(--ok)"><i class="bi bi-file-earmark-pdf-fill"></i></div>
          <div><div class="ov-action-title">Notes</div><div class="ov-action-sub">Download course notes</div></div>
        </a>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var player = document.getElementById('player');
      var form = document.getElementById('watchedForm');
      var watchedInput = document.getElementById('watchedSecondsInput');
      var progressBar = document.getElementById('liveProgress');
      var alreadyWatched = {{ in_array($currentVideo->id, $watchedIds) ? 'true' : 'false' }};
      var autoSubmitted = alreadyWatched;

      player.addEventListener('timeupdate', function () {
        if (!player.duration) { return; }
        watchedInput.value = Math.floor(player.currentTime);
        if (!alreadyWatched) {
          progressBar.style.width = Math.min(100, Math.round((player.currentTime / player.duration) * 100)) + '%';
        }
        if (!autoSubmitted && player.currentTime / player.duration >= 0.9) {
          autoSubmitted = true;
          form.submit();
        }
      });

      player.addEventListener('ended', function () {
        if (!autoSubmitted) {
          autoSubmitted = true;
          watchedInput.value = Math.floor(player.duration || 0);
          form.submit();
        }
      });
    })();
  </script>
@endsection
