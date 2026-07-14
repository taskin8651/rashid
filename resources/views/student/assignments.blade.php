@extends('layouts.student')

@section('title', 'Assignments')

@section('content')
  <div class="shead">
    <h4>{{ $course->name }} &mdash; Assignments</h4>
    <p><a href="{{ route('student.courses.learn', $course) }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to Course</a></p>
  </div>

  @forelse ($assignments as $assignment)
    @php $submission = $assignment->submissions->first(); @endphp
    <div class="acard mb-3">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
        <h6 style="font-size:15px;font-weight:700;margin:0">{{ $assignment->title }}</h6>
        @if ($submission)
          <span class="bstatus {{ $submission->status === 'graded' ? 'b-graded' : 'b-pending' }}">{{ $submission->status === 'graded' ? 'Graded' : 'Submitted' }}</span>
        @else
          <span class="bstatus b-pending">Not Submitted</span>
        @endif
      </div>
      @if ($assignment->description)
        <p style="font-size:13px;color:var(--muted);margin-bottom:10px">{{ $assignment->description }}</p>
      @endif
      <p style="font-size:12px;color:var(--muted);margin-bottom:14px">
        @if ($assignment->due_date)Due {{ $assignment->due_date->format('d M Y') }} &middot; @endif
        Max Score: {{ $assignment->max_score }}
      </p>

      @if ($submission && $submission->status === 'graded')
        <div style="background:rgba(37,99,235,.06);border:1px solid var(--border);border-radius:10px;padding:12px 14px;margin-bottom:14px">
          <div style="font-size:14px;font-weight:700;color:var(--orange)">{{ $submission->score }}/{{ $assignment->max_score }}</div>
          @if ($submission->feedback)
            <p style="font-size:12px;color:var(--muted);margin:6px 0 0">{{ $submission->feedback }}</p>
          @endif
        </div>
      @endif

      <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data" class="d-flex gap-2 flex-wrap">
        @csrf
        <input class="fctrl" type="file" name="file" style="flex:1;min-width:200px" required>
        <button class="bsave" type="submit" style="white-space:nowrap"><i class="bi bi-upload me-1"></i>{{ $submission ? 'Resubmit' : 'Submit' }}</button>
      </form>
      @if ($submission)
        <a href="{{ route('submissions.download', $submission) }}" style="font-size:11px;color:var(--muted);text-decoration:none;display:inline-block;margin-top:8px"><i class="bi bi-file-earmark-arrow-down me-1"></i>View your submission</a>
      @endif
    </div>
  @empty
    <div class="card-rt text-center" style="padding:40px 24px">
      <i class="bi bi-journal-check" style="font-size:28px;color:var(--orange);margin-bottom:10px;display:inline-block"></i>
      <p style="font-size:13px;color:var(--muted);margin:0">No assignments for this course yet.</p>
    </div>
  @endforelse
@endsection
