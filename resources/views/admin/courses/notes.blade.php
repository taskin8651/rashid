@extends('layouts.admin')

@section('title', 'Course Notes')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0">
      <h4>{{ $course->name }} &mdash; Notes</h4>
      <p><a href="{{ route('admin.courses.index') }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to Course Management</a></p>
    </div>
    <button type="button" class="bsave" data-bs-toggle="modal" data-bs-target="#addNote"><i class="bi bi-plus-circle-fill me-1"></i>Upload Note</button>
  </div>

  @if ($course->notes->isEmpty())
    <div class="card-rt text-center" style="padding:40px 24px">
      <i class="bi bi-file-earmark-pdf" style="font-size:28px;color:var(--orange);margin-bottom:10px;display:inline-block"></i>
      <p style="font-size:13px;color:var(--muted);margin:0">No notes uploaded yet.</p>
    </div>
  @else
    <div class="row g-3">
      @foreach ($course->notes as $note)
        <div class="col-md-6 col-lg-4">
          <div class="card-rt d-flex align-items-center gap-3">
            <div class="sicon" style="background:rgba(239,68,68,.14);color:var(--danger);flex-shrink:0"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div style="flex:1;min-width:0">
              <div style="font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $note->title }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $note->page_count }} pages</div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
              <button type="button" class="action-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editNote{{ $note->id }}"><i class="bi bi-pencil-fill"></i></button>
              <form method="POST" action="{{ route('admin.courses.notes.destroy', [$course, $note]) }}" onsubmit="return confirm('Delete this note?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </div>
          </div>
        </div>

        @include('admin.courses.partials.note-modal', ['course' => $course, 'note' => $note, 'modalId' => 'editNote' . $note->id])
      @endforeach
    </div>
  @endif

  @include('admin.courses.partials.note-modal', ['course' => $course, 'note' => null, 'modalId' => 'addNote'])
@endsection
