<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\VideoProgress;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $enrollments = $user->enrollments()->with('course')->where('status', 'paid')->get();

        $stats = [
            'courses_enrolled' => $enrollments->count(),
            'videos_watched' => $user->videoProgress()->where('completed', true)->count(),
            'quizzes_passed' => $user->quizAttempts()->where('status', 'submitted')->where('percentage', '>=', 50)->count(),
            'certificates' => $user->certificates()->where('status', 'issued')->count(),
        ];

        $myCourses = $enrollments->map(function ($e) use ($user) {
            $totalVideos = $e->course->videos()->count();
            $watched = VideoProgress::where('user_id', $user->id)
                ->whereIn('video_id', $e->course->videos()->pluck('id'))
                ->where('completed', true)
                ->count();
            $percent = $totalVideos > 0 ? round(($watched / $totalVideos) * 100) : 0;

            return [
                'course' => $e->course,
                'percent_complete' => $percent,
                'videos_watched' => $watched,
                'total_videos' => $totalVideos,
                'enrolled_at' => optional($e->enrolled_at)->format('d M Y'),
            ];
        });

        return view('student.dashboard', compact('stats', 'myCourses'));
    }
}
