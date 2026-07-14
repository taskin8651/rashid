@extends('layouts.student')

@section('title', 'Quiz')

@section('content')
  <div class="shead">
    <h4>{{ $course->name }} &mdash; Quiz</h4>
    <p><a href="{{ route('student.courses.learn', $course) }}" style="color:var(--muted);text-decoration:none"><i class="bi bi-arrow-left me-1"></i>Back to Course</a></p>
  </div>

  @if ($lastAttempt)
    <div class="card-rt mb-4" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <div style="font-size:12px;color:var(--muted)">Your last attempt</div>
        <div style="font-size:15px;font-weight:700">{{ $lastAttempt->score }}/{{ $lastAttempt->total_questions }} correct &middot; {{ $lastAttempt->percentage }}%</div>
      </div>
      <a href="{{ route('student.courses.quiz.result', [$course, $lastAttempt]) }}" class="bghost" style="text-decoration:none;font-size:12px;padding:9px 18px">View Result</a>
    </div>
  @endif

  <form method="POST" action="{{ route('student.courses.quiz.submit', $course) }}">
    @csrf
    @foreach ($questions as $question)
      <div class="card-rt mb-3">
        <p style="font-size:14px;font-weight:700;margin-bottom:14px">{{ $loop->iteration }}. {{ $question->question_text }}</p>
        @foreach ($question->options as $option)
          <label class="qopt" onclick="document.querySelectorAll('[data-q=&quot;{{ $question->id }}&quot;]').forEach(o=>o.classList.remove('sel'));this.classList.add('sel')" data-q="{{ $question->id }}">
            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required style="display:none">
            <span class="odot"></span>
            <span>{{ $option->option_text }}</span>
          </label>
        @endforeach
      </div>
    @endforeach

    <button class="bsave mt-2" type="submit"><i class="bi bi-check2-circle me-1"></i>Submit Quiz</button>
  </form>
@endsection
