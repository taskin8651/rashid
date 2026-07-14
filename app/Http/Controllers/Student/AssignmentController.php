<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $user = $request->user();
        $this->authorizeEnrollment($user, $course);

        $assignments = $course->assignments()
            ->with(['submissions' => fn ($q) => $q->where('user_id', $user->id)])
            ->orderBy('due_date')
            ->get();

        return view('student.assignments', compact('course', 'assignments'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        $user = $request->user();
        $this->authorizeEnrollment($user, $assignment->course);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,doc,docx,zip,jpg,jpeg,png'],
        ]);

        $submission = AssignmentSubmission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => $user->id],
            ['submitted_at' => now(), 'status' => 'submitted', 'score' => null, 'feedback' => null, 'graded_at' => null]
        );

        $submission->addMediaFromRequest('file')->toMediaCollection('file');

        return back()->with('status', 'Assignment submitted.');
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
