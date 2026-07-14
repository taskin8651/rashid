<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseAssignmentController extends Controller
{
    public function index(Course $course)
    {
        $course->load(['assignments.submissions.user']);

        return view('admin.courses.assignments', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'max_score' => ['required', 'integer', 'min:1'],
        ]);

        $course->assignments()->create($validated);

        return back()->with('status', 'Assignment added.');
    }

    public function update(Request $request, Course $course, Assignment $assignment)
    {
        abort_unless($assignment->course_id === $course->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'max_score' => ['required', 'integer', 'min:1'],
        ]);

        $assignment->update($validated);

        return back()->with('status', 'Assignment updated.');
    }

    public function destroy(Course $course, Assignment $assignment)
    {
        abort_unless($assignment->course_id === $course->id, 404);

        $assignment->delete();

        return back()->with('status', 'Assignment deleted.');
    }

    public function grade(Request $request, Course $course, AssignmentSubmission $submission)
    {
        abort_unless($submission->assignment->course_id === $course->id, 404);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:' . $submission->assignment->max_score],
            'feedback' => ['nullable', 'string'],
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        return back()->with('status', 'Submission graded.');
    }
}
