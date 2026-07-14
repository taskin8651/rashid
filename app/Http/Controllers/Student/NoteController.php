<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $user = $request->user();

        abort_unless(
            $user->enrollments()->where('course_id', $course->id)->whereIn('status', ['paid', 'completed'])->exists(),
            403,
            'You are not enrolled in this course.'
        );

        $notes = $course->notes()->orderBy('sort_order')->get();

        return view('student.notes', compact('course', 'notes'));
    }
}
