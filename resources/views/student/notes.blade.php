@extends('layouts.student')

@section('title', 'Course Notes')

@section('content')
  <div class="shead">
    <h4>{{ $course->name }} &mdash; Notes</h4>
    <p><a href="{{ route('student.courses.learn', $course) }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to Course</a></p>
  </div>

  @if ($notes->isEmpty())
    <div class="card-rt text-center" style="padding:40px 24px">
      <i class="bi bi-file-earmark-pdf" style="font-size:28px;color:var(--orange);margin-bottom:10px;display:inline-block"></i>
      <p style="font-size:13px;color:var(--muted);margin:0">No notes available for this course yet.</p>
    </div>
  @else
    <div class="row g-3">
      @foreach ($notes as $note)
        <div class="col-md-6 col-lg-4">
          <a href="{{ route('notes.download', $note) }}" class="card-rt d-flex align-items-center gap-3" style="text-decoration:none;color:inherit">
            <div class="sicon" style="background:rgba(239,68,68,.14);color:var(--danger);flex-shrink:0"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $note->title }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $note->page_count }} pages &middot; <i class="bi bi-download"></i> Download</div>
            </div>
          </a>
        </div>
      @endforeach
    </div>
  @endif
@endsection
