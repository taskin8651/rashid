<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function show(Request $request, Course $course)
    {
        $user = $request->user();
        $this->authorizeEnrollment($user, $course);

        $questions = $course->quizQuestions()->where('status', 'active')->with('options')->orderBy('sort_order')->get();
        abort_if($questions->isEmpty(), 404, 'No quiz is available for this course yet.');

        $lastAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->first();

        return view('student.quiz', compact('course', 'questions', 'lastAttempt'));
    }

    public function submit(Request $request, Course $course)
    {
        $user = $request->user();
        $this->authorizeEnrollment($user, $course);

        $questions = $course->quizQuestions()->where('status', 'active')->with('options')->orderBy('sort_order')->get();
        abort_if($questions->isEmpty(), 404);

        $validated = $request->validate(['answers' => ['nullable', 'array']]);
        $answers = $validated['answers'] ?? [];

        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'total_questions' => $questions->count(),
            'started_at' => now(),
            'status' => 'in_progress',
        ]);

        $score = 0;
        foreach ($questions as $question) {
            $selectedId = $answers[$question->id] ?? null;
            $correctOption = $question->options->firstWhere('is_correct', true);
            $isCorrect = $selectedId && $correctOption && (int) $selectedId === $correctOption->id;

            if ($isCorrect) {
                $score++;
            }

            QuizAttemptAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'selected_option_id' => $selectedId ?: null,
                'is_correct' => $isCorrect,
            ]);
        }

        $percentage = $questions->count() > 0 ? round(($score / $questions->count()) * 100, 2) : 0;

        $attempt->update([
            'score' => $score,
            'percentage' => $percentage,
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        return redirect()->route('student.courses.quiz.result', [$course, $attempt]);
    }

    public function result(Request $request, Course $course, QuizAttempt $attempt)
    {
        abort_unless($attempt->user_id === $request->user()->id && $attempt->course_id === $course->id, 403);

        $attempt->load(['answers.question.options', 'answers.selectedOption']);

        return view('student.quiz-result', compact('course', 'attempt'));
    }

    protected function authorizeEnrollment($user, Course $course): void
    {
        abort_unless(
            $user->enrollments()->where('course_id', $course->id)->whereIn('status', ['paid', 'completed'])->exists(),
            403,
            'You are not enrolled in this course.'
        );
    }
}
