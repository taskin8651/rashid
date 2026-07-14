@extends('layouts.admin')

@section('title', 'Course Assignments')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0">
      <h4>{{ $course->name }} &mdash; Assignments</h4>
      <p><a href="{{ route('admin.courses.index') }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to Course Management</a></p>
    </div>
    <button type="button" class="bsave" data-bs-toggle="modal" data-bs-target="#addAssignment"><i class="bi bi-plus-circle-fill me-1"></i>Add Assignment</button>
  </div>

  @if ($course->assignments->isEmpty())
    <div class="card-rt text-center" style="padding:40px 24px">
      <i class="bi bi-journal-text" style="font-size:28px;color:var(--orange);margin-bottom:10px;display:inline-block"></i>
      <p style="font-size:13px;color:var(--muted);margin:0">No assignments yet. Add one to get started.</p>
    </div>
  @else
    @foreach ($course->assignments as $assignment)
      <div class="card-rt mb-3">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
          <div>
            <h6 style="font-size:15px;font-weight:700;margin-bottom:4px">{{ $assignment->title }}</h6>
            <p style="font-size:12px;color:var(--muted);margin:0">
              @if ($assignment->due_date)Due {{ $assignment->due_date->format('d M Y') }} &middot; @endif
              Max Score: {{ $assignment->max_score }} &middot; {{ $assignment->submissions->count() }} {{ \Illuminate\Support\Str::plural('submission', $assignment->submissions->count()) }}
            </p>
          </div>
          <div class="d-flex gap-2 flex-shrink-0">
            <button type="button" class="action-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editAssignment{{ $assignment->id }}"><i class="bi bi-pencil-fill"></i></button>
            <form method="POST" action="{{ route('admin.courses.assignments.destroy', [$course, $assignment]) }}" onsubmit="return confirm('Delete this assignment?')">
              @csrf @method('DELETE')
              <button type="submit" class="action-btn danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
            </form>
          </div>
        </div>

        @if ($assignment->submissions->isNotEmpty())
          <div class="table-wrap">
            <table class="table-rt">
              <thead><tr><th>Student</th><th>Submitted</th><th>Status</th><th>Score</th><th style="text-align:right">Actions</th></tr></thead>
              <tbody>
                @foreach ($assignment->submissions as $submission)
                  <tr>
                    <td>{{ $submission->user->name }}</td>
                    <td>{{ optional($submission->submitted_at)->format('d M Y') }}</td>
                    <td><span class="badge-rt {{ $submission->status === 'graded' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($submission->status) }}</span></td>
                    <td>{{ $submission->score !== null ? $submission->score . '/' . $assignment->max_score : '—' }}</td>
                    <td style="text-align:right;white-space:nowrap">
                      <a href="{{ route('submissions.download', $submission) }}" class="action-btn" title="Download"><i class="bi bi-download"></i></a>
                      <button type="button" class="action-btn" title="Grade" data-bs-toggle="modal" data-bs-target="#grade{{ $submission->id }}"><i class="bi bi-award-fill"></i></button>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      @include('admin.courses.partials.assignment-modal', ['course' => $course, 'assignment' => $assignment, 'modalId' => 'editAssignment' . $assignment->id])

      @foreach ($assignment->submissions as $submission)
        <div class="modal fade" id="grade{{ $submission->id }}" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-award-fill me-2"></i>Grade &mdash; {{ $submission->user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <form method="POST" action="{{ route('admin.courses.assignments.grade', [$course, $submission]) }}">
                @csrf
                <div class="modal-body">
                  <div class="row g-3">
                    <div class="col-6"><label class="flbl">Score (out of {{ $assignment->max_score }})</label><input class="fctrl" type="number" name="score" min="0" max="{{ $assignment->max_score }}" value="{{ $submission->score }}" required></div>
                    <div class="col-12"><label class="flbl">Feedback</label><textarea class="fctrl" name="feedback" rows="3">{{ $submission->feedback }}</textarea></div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="bsave"><i class="bi bi-check2-circle me-1"></i>Save Grade</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    @endforeach
  @endif

  @include('admin.courses.partials.assignment-modal', ['course' => $course, 'assignment' => null, 'modalId' => 'addAssignment'])
@endsection
