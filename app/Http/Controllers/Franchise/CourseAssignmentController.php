<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Notifications\AssignmentGradedNotification;
use Illuminate\Http\Request;

class CourseAssignmentController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $course->load(['assignments.submissions.user']);

        return view('franchise.courses-assignments', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

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
        $this->authorizeOwnership($request, $course);
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

    public function destroy(Request $request, Course $course, Assignment $assignment)
    {
        $this->authorizeOwnership($request, $course);
        abort_unless($assignment->course_id === $course->id, 404);

        $assignment->delete();

        return back()->with('status', 'Assignment deleted.');
    }

    public function grade(Request $request, Course $course, AssignmentSubmission $submission)
    {
        $this->authorizeOwnership($request, $course);
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

        $submission->user->notify(new AssignmentGradedNotification($submission->fresh(['assignment'])));

        return back()->with('status', 'Submission graded.');
    }

    protected function authorizeOwnership(Request $request, Course $course): void
    {
        abort_unless(
            $course->franchise_booking_id && $request->user()->hasFranchisePermission($course->franchise_booking_id, 'manage-courses'),
            403
        );
    }
}
