<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $user = $request->user();

        $enrollments = $user->enrollments()->with('course')->where('status', 'paid')->get();

        $videosWatched = $user->videoProgress()->where('completed', true)->count();
        $quizzesPassed = $user->quizAttempts()->where('status', 'submitted')->where('percentage', '>=', 50)->count();
        $certificatesEarned = $user->certificates()->where('status', 'issued')->count();

        return response()->json([
            'stats' => [
                'courses_enrolled' => $enrollments->count(),
                'videos_watched' => $videosWatched,
                'quizzes_passed' => $quizzesPassed,
                'certificates' => $certificatesEarned,
            ],
            'enrolled_courses' => $enrollments->map(fn ($e) => [
                'course_id' => $e->course_id,
                'course_name' => $e->course->name,
                'enrolled_at' => optional($e->enrolled_at)->format('d M Y'),
            ]),
        ]);
    }
}
