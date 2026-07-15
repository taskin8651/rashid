<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $user = $request->user();

        $isEnrolled = $user->enrollments()->where('course_id', $course->id)->whereIn('status', ['paid', 'completed'])->exists();
        abort_unless($isEnrolled, 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
                'status' => 'pending',
            ]
        );

        return back()->with('status', 'Thanks! Your review has been submitted for approval.');
    }
}
