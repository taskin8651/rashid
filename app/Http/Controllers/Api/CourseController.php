<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('category')
            ->where('status', 'active')
            ->get()
            ->map(fn (Course $c) => $this->coursePayload($c));

        return response()->json(['courses' => $courses]);
    }

    public function show(string $slug)
    {
        $course = Course::with(['category', 'modules', 'batches'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return response()->json(['course' => $this->coursePayload($course, true)]);
    }

    public function batches(Course $course)
    {
        $batches = $course->batches()
            ->where('status', '!=', 'closed')
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'label' => $b->label,
                'schedule_text' => $b->schedule_text,
                'start_date' => $b->start_date->format('d M Y'),
                'mode' => $b->mode,
                'seats_left' => $b->seats_total ? max(0, $b->seats_total - $b->seats_filled) : null,
            ]);

        return response()->json(['batches' => $batches]);
    }

    protected function coursePayload(Course $course, bool $detailed = false): array
    {
        $payload = [
            'id' => $course->id,
            'slug' => $course->slug,
            'name' => $course->name,
            'category' => $course->category->name ?? null,
            'price' => (float) $course->price,
            'duration_text' => $course->duration_text,
            'description' => $course->description,
            'rating' => $course->rating,
            'rating_count' => $course->rating_count,
            'thumbnail_url' => $course->thumbnailUrl(),
        ];

        if ($detailed) {
            $payload['modules'] = $course->modules->pluck('title');
            $payload['batches'] = $course->batches->map(fn ($b) => [
                'id' => $b->id,
                'label' => $b->label,
                'schedule_text' => $b->schedule_text,
                'start_date' => $b->start_date->format('d M Y'),
            ]);
        }

        return $payload;
    }
}
