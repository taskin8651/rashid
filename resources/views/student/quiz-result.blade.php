@extends('layouts.student')

@section('title', 'Quiz Result')

@section('content')
  <div class="shead">
    <h4>{{ $course->name }} &mdash; Quiz Result</h4>
    <p><a href="{{ route('student.courses.learn', $course) }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to Course</a></p>
  </div>

  <div class="cert mb-4">
    <div class="cert-t">{{ $attempt->percentage }}%</div>
    <div class="cert-b">{{ $attempt->score }} out of {{ $attempt->total_questions }} correct</div>
    <a href="{{ route('student.courses.quiz.show', $course) }}" class="bcontinue" style="text-decoration:none;display:inline-block;width:auto;padding:10px 24px;margin-top:16px"><i class="bi bi-arrow-repeat me-1"></i>Retake Quiz</a>
  </div>

  @foreach ($attempt->answers as $answer)
    <div class="card-rt mb-3">
      <p style="font-size:14px;font-weight:700;margin-bottom:12px">{{ $loop->iteration }}. {{ $answer->question->question_text }}</p>
      @foreach ($answer->question->options as $option)
        @php
          $isSelected = $answer->selected_option_id === $option->id;
          $style = $option->is_correct
            ? 'background:rgba(40,180,90,.12);border-color:rgba(125,220,160,.35);color:var(--ok)'
            : ($isSelected ? 'background:rgba(239,68,68,.12);border-color:rgba(248,124,124,.35);color:var(--danger)' : '');
        @endphp
        <div class="qopt" style="cursor:default;{{ $style }}">
          <span class="odot" style="{{ $option->is_correct ? 'background:var(--ok);border-color:var(--ok)' : ($isSelected ? 'background:var(--danger);border-color:var(--danger)' : '') }}"></span>
          <span>{{ $option->option_text }}</span>
          @if ($option->is_correct)<i class="bi bi-check-circle-fill ms-auto"></i>@elseif ($isSelected)<i class="bi bi-x-circle-fill ms-auto"></i>@endif
        </div>
      @endforeach
    </div>
  @endforeach
@endsection
