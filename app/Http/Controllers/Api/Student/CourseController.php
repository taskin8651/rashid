<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function myCourses(Request $request)
    {
        $enrollments = $request->user()->enrollments()
            ->with('course')
            ->where('status', 'paid')
            ->get();

        $courses = $enrollments->map(function ($e) {
            $totalVideos = $e->course->videos()->count();
            $watched = \App\Models\VideoProgress::where('user_id', $e->user_id)
                ->whereIn('video_id', $e->course->videos()->pluck('id'))
                ->where('completed', true)
                ->count();

            $percent = $totalVideos > 0 ? round(($watched / $totalVideos) * 100) : 0;

            return [
                'course_id' => $e->course_id,
                'name' => $e->course->name,
                'thumbnail_url' => $e->course->thumbnailUrl(),
                'percent_complete' => $percent,
                'videos_watched' => $watched,
                'total_videos' => $totalVideos,
                'status' => $e->status,
            ];
        });

        return response()->json(['courses' => $courses]);
    }
}
