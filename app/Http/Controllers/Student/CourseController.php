<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\VideoProgress;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $myCourses = $user->enrollments()->with('course')->where('status', 'paid')->get()->map(function ($e) use ($user) {
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
            ];
        });

        return view('student.courses', compact('myCourses'));
    }
}
