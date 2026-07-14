@extends('layouts.franchise')

@section('title', 'Course Quiz')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0">
      <h4>{{ $course->name }} &mdash; Quiz</h4>
      <p><a href="{{ route('franchise.courses.index') }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to My Courses</a></p>
    </div>
    <button type="button" class="bsave" data-bs-toggle="modal" data-bs-target="#addQuestion"><i class="bi bi-plus-circle-fill me-1"></i>Add Question</button>
  </div>

  @if ($course->quizQuestions->isEmpty())
    <div class="card-rt text-center" style="padding:40px 24px">
      <i class="bi bi-patch-question" style="font-size:28px;color:var(--orange);margin-bottom:10px;display:inline-block"></i>
      <p style="font-size:13px;color:var(--muted);margin:0">No quiz questions yet. Add one to get started.</p>
    </div>
  @else
    @foreach ($course->quizQuestions as $question)
      <div class="card-rt mb-3">
        <div class="d-flex justify-content-between align-items-start gap-3">
          <div style="flex:1">
            <div class="d-flex align-items-center gap-2 mb-2">
              <span class="badge-rt {{ $question->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ $question->status }}</span>
              <span style="font-size:11px;color:var(--muted)">Q{{ $loop->iteration }}</span>
            </div>
            <p style="font-size:14px;font-weight:600;margin-bottom:10px">{{ $question->question_text }}</p>
            <div class="d-flex flex-wrap gap-2">
              @foreach ($question->options as $option)
                <span style="font-size:12px;padding:5px 12px;border-radius:8px;{{ $option->is_correct ? 'background:rgba(40,180,90,.14);color:var(--ok);border:1px solid rgba(125,220,160,.3)' : 'background:rgba(var(--text-rgb),.05);color:var(--muted);border:1px solid var(--border)' }}">
                  @if ($option->is_correct)<i class="bi bi-check-circle-fill me-1"></i>@endif{{ $option->option_text }}
                </span>
              @endforeach
            </div>
          </div>
          <div class="d-flex gap-2 flex-shrink-0">
            <button type="button" class="action-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editQuestion{{ $question->id }}"><i class="bi bi-pencil-fill"></i></button>
            <form method="POST" action="{{ route('franchise.courses.quiz.destroy', [$course, $question]) }}" onsubmit="return confirm('Delete this question?')">
              @csrf @method('DELETE')
              <button type="submit" class="action-btn danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
            </form>
          </div>
        </div>
      </div>

      @include('franchise.courses-quiz-modal', ['course' => $course, 'question' => $question, 'modalId' => 'editQuestion' . $question->id])
    @endforeach
  @endif

  @include('franchise.courses-quiz-modal', ['course' => $course, 'question' => null, 'modalId' => 'addQuestion'])
@endsection
