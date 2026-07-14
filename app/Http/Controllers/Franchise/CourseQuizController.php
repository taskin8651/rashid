<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseQuizController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $course->load(['quizQuestions' => fn ($q) => $q->with('options')->orderBy('sort_order')]);

        return view('franchise.courses-quiz', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $validated = $this->validateQuestion($request);

        $question = QuizQuestion::create([
            'course_id' => $course->id,
            'question_text' => $validated['question_text'],
            'sort_order' => $course->quizQuestions()->count(),
            'status' => $validated['status'],
        ]);

        $this->saveOptions($question, $validated);

        return back()->with('status', 'Question added.');
    }

    public function update(Request $request, Course $course, QuizQuestion $question)
    {
        $this->authorizeOwnership($request, $course);
        abort_unless($question->course_id === $course->id, 404);

        $validated = $this->validateQuestion($request);

        $question->update([
            'question_text' => $validated['question_text'],
            'status' => $validated['status'],
        ]);

        $question->options()->delete();
        $this->saveOptions($question, $validated);

        return back()->with('status', 'Question updated.');
    }

    public function destroy(Request $request, Course $course, QuizQuestion $question)
    {
        $this->authorizeOwnership($request, $course);
        abort_unless($question->course_id === $course->id, 404);

        $question->delete();

        return back()->with('status', 'Question deleted.');
    }

    protected function validateQuestion(Request $request): array
    {
        return $request->validate([
            'question_text' => ['required', 'string', 'max:500'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0'],
        ]);
    }

    protected function saveOptions(QuizQuestion $question, array $validated): void
    {
        foreach ($validated['options'] as $i => $text) {
            QuizOption::create([
                'question_id' => $question->id,
                'option_text' => $text,
                'is_correct' => (int) $i === (int) $validated['correct_index'],
            ]);
        }
    }

    protected function authorizeOwnership(Request $request, Course $course): void
    {
        abort_unless(
            $course->franchise_booking_id && $course->franchiseBooking->user_id === $request->user()->id,
            403
        );
    }
}
