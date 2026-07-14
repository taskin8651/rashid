<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Video;
use App\Models\VideoProgress;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $myCourses = $user->enrollments()->with('course.category')->whereIn('status', ['paid', 'completed'])->get()->map(function ($e) use ($user) {
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
            ];
        });

        $stats = [
            'total' => $myCourses->count(),
            'completed' => $myCourses->where('percent_complete', '>=', 100)->count(),
            'in_progress' => $myCourses->filter(fn ($mc) => $mc['percent_complete'] > 0 && $mc['percent_complete'] < 100)->count(),
            'certificates' => $user->certificates()->where('status', 'issued')->count(),
        ];

        return view('student.courses', compact('myCourses', 'stats'));
    }

    public function learn(Request $request, Course $course)
    {
        $user = $request->user();

        $this->authorizeEnrollment($user, $course);

        $course->load([
            'modules' => fn ($q) => $q->orderBy('sort_order'),
            'modules.videos' => fn ($q) => $q->where('status', 'active')->whereHas('media')->orderBy('sort_order'),
        ]);

        $unassigned = $course->videos()->where('status', 'active')->whereHas('media')->whereNull('module_id')->orderBy('sort_order')->get();

        $allVideos = $course->modules->flatMap->videos->concat($unassigned)->sortBy('sort_order')->values();

        abort_if($allVideos->isEmpty(), 404, 'No videos are available for this course yet.');

        $watchedIds = VideoProgress::where('user_id', $user->id)
            ->whereIn('video_id', $allVideos->pluck('id'))
            ->where('completed', true)
            ->pluck('video_id')
            ->all();

        $requestedId = $request->integer('video');
        $currentVideo = $requestedId ? $allVideos->firstWhere('id', $requestedId) : null;
        $currentVideo = $currentVideo ?? $allVideos->first(fn ($v) => !in_array($v->id, $watchedIds)) ?? $allVideos->first();

        $currentIndex = $allVideos->search(fn ($v) => $v->id === $currentVideo->id);
        $nextVideo = $allVideos->get($currentIndex + 1);

        return view('student.learn', compact('course', 'allVideos', 'watchedIds', 'currentVideo', 'nextVideo'));
    }

    public function markWatched(Request $request, Video $video)
    {
        $user = $request->user();

        $this->authorizeEnrollment($user, $video->course);

        $validated = $request->validate(['watched_seconds' => ['nullable', 'integer', 'min:0']]);
        $watchedSeconds = min($validated['watched_seconds'] ?? $video->duration_seconds, $video->duration_seconds ?: PHP_INT_MAX);

        VideoProgress::updateOrCreate(
            ['user_id' => $user->id, 'video_id' => $video->id],
            ['completed' => true, 'watched_seconds' => $watchedSeconds, 'last_watched_at' => now()]
        );

        $next = $request->integer('next_video_id');
        $redirectParams = ['course' => $video->course_id];
        if ($next) {
            $redirectParams['video'] = $next;
        }

        return redirect()->route('student.courses.learn', $redirectParams)->with('status', 'Marked as watched.');
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
