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

        $enrollments = $user->enrollments()->with(['course', 'payments'])->whereIn('status', ['paid', 'completed'])->get();

        $stats = [
            'courses_enrolled' => $enrollments->count(),
            'videos_watched' => $user->videoProgress()->where('completed', true)->count(),
            'quizzes_passed' => $user->quizAttempts()->where('status', 'submitted')->where('percentage', '>=', 50)->count(),
            'certificates' => $user->certificates()->where('status', 'issued')->count(),
        ];

        $myCourses = $enrollments->map(function ($e) use ($user) {
            $totalVideos = $e->course->videos()->where('status', 'active')->whereHas('media')->count();
            $watched = VideoProgress::where('user_id', $user->id)
                ->whereIn('video_id', $e->course->videos()->where('status', 'active')->whereHas('media')->pluck('id'))
                ->where('completed', true)
                ->count();
            $percent = $totalVideos > 0 ? round(($watched / $totalVideos) * 100) : 0;

            return [
                'course' => $e->course,
                'percent_complete' => $percent,
                'videos_watched' => $watched,
                'total_videos' => $totalVideos,
                'enrolled_at' => optional($e->enrolled_at)->format('d M Y'),
                'amount' => (float) $e->final_amount,
            ];
        });

        $progressSegments = [
            ['label' => 'Completed', 'value' => $myCourses->where('percent_complete', '>=', 100)->count(), 'color' => '40,180,90'],
            ['label' => 'In Progress', 'value' => $myCourses->filter(fn ($mc) => $mc['percent_complete'] > 0 && $mc['percent_complete'] < 100)->count(), 'color' => '37,99,235'],
            ['label' => 'Not Started', 'value' => $myCourses->where('percent_complete', 0)->count(), 'color' => '107,127,163'],
        ];

        $totalSpent = $enrollments->sum('final_amount');
        $totalPaid = $enrollments->sum('amount_paid');

        $recentPayments = $enrollments->sortByDesc(fn ($e) => $e->enrolled_at ?? $e->created_at)->take(5);

        return view('student.dashboard', compact('stats', 'myCourses', 'progressSegments', 'totalSpent', 'totalPaid', 'recentPayments'));
    }
}
