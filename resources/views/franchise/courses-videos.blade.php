@extends('layouts.franchise')

@section('title', 'Course Videos')

@section('content')
  <div class="shead">
    <h4>{{ $course->name }} &mdash; Videos</h4>
    <p><a href="{{ route('franchise.courses.index') }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to My Courses</a></p>
  </div>

  <details class="card-rt mb-4">
    <summary class="bsave" style="display:inline-block;cursor:pointer;list-style:none"><i class="bi bi-upload me-1"></i>Upload New Video</summary>
    <form method="POST" action="{{ route('franchise.courses.videos.store', $course) }}" enctype="multipart/form-data" class="mt-4">
      @csrf
      <div class="row g-3">
        <div class="col-md-6"><label class="flbl">Video Title</label><input class="fctrl" type="text" name="title" placeholder="e.g. Introduction to HTML" required/></div>
        <div class="col-md-3"><label class="flbl">Module</label>
          <select class="fctrl" name="module_id">
            <option value="">Unassigned</option>
            @foreach ($course->modules as $module)
              <option value="{{ $module->id }}">{{ $module->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-3"><label class="flbl">Type</label>
          <select class="fctrl" name="type" required>
            <option value="demo">Demo (public)</option>
            <option value="premium">Premium (enrolled only)</option>
          </select>
        </div>
        <div class="col-md-4"><label class="flbl">Duration (seconds)</label><input class="fctrl" type="number" name="duration_seconds" min="0" placeholder="e.g. 480"/></div>
        <div class="col-md-4"><label class="flbl">Status</label><select class="fctrl" name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
        <div class="col-md-4"><label class="flbl">Video File (optional)</label><input class="fctrl" type="file" name="file" accept="video/mp4,video/webm,video/ogg,video/quicktime"/></div>
      </div>
      <p style="font-size:11px;color:var(--muted);margin-top:8px">MP4, WebM, OGG or MOV &middot; no size limit. Leave the file empty to save the video entry now and attach the file later by editing it. Premium videos are stored privately — only your enrolled students, you and R-Tech admins can stream them.</p>
      <button class="bsave mt-3" type="submit"><i class="bi bi-check2-circle me-1"></i>Save Video</button>
    </form>
  </details>

  @if (!$course->modules->count() && !$unassigned->count())
    <div class="card-rt"><p style="font-size:13px;color:var(--muted);margin:0">No videos yet. Upload one above to get started.</p></div>
  @endif

  @foreach ($course->modules as $module)
    @if ($module->videos->count())
      <div class="card-rt mb-3">
        <div class="card-title">{{ $module->title }}</div>
        @include('franchise.courses-videos-table', ['videos' => $module->videos, 'course' => $course])
      </div>
    @endif
  @endforeach

  @if ($unassigned->count())
    <div class="card-rt mb-3">
      <div class="card-title">Unassigned</div>
      @include('franchise.courses-videos-table', ['videos' => $unassigned, 'course' => $course])
    </div>
  @endif
@endsection
