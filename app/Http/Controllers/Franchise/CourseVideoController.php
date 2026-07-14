<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseVideoController extends Controller
{
    public function index(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $course->load(['modules.videos' => fn ($q) => $q->orderBy('sort_order')]);
        $unassigned = $course->videos()->whereNull('module_id')->orderBy('sort_order')->get();

        return view('franchise.courses-videos', compact('course', 'unassigned'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $validated = $request->validate([
            'module_id' => ['nullable', 'exists:course_modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['demo', 'premium'])],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'file' => ['required', 'file', 'mimes:mp4,webm,ogg,mov', 'max:97280'],
        ]);

        if ($validated['module_id'] ?? null) {
            abort_unless(
                $course->modules()->whereKey($validated['module_id'])->exists(),
                422,
                'That module does not belong to this course.'
            );
        }

        $video = Video::create([
            'course_id' => $course->id,
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'sort_order' => $course->videos()->count(),
            'status' => $validated['status'],
        ]);

        $video->addMediaFromRequest('file')->toMediaCollection('file');

        return back()->with('status', 'Video uploaded.');
    }

    public function update(Request $request, Course $course, Video $video)
    {
        $this->authorizeOwnership($request, $course);
        abort_unless($video->course_id === $course->id, 404);

        $validated = $request->validate([
            'module_id' => ['nullable', 'exists:course_modules,id'],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['demo', 'premium'])],
            'duration_seconds' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'file' => ['nullable', 'file', 'mimes:mp4,webm,ogg,mov', 'max:97280'],
        ]);

        if ($validated['module_id'] ?? null) {
            abort_unless(
                $course->modules()->whereKey($validated['module_id'])->exists(),
                422,
                'That module does not belong to this course.'
            );
        }

        $video->update([
            'module_id' => $validated['module_id'] ?? null,
            'title' => $validated['title'],
            'type' => $validated['type'],
            'duration_seconds' => $validated['duration_seconds'] ?? 0,
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('file')) {
            $video->addMediaFromRequest('file')->toMediaCollection('file');
        }

        return back()->with('status', 'Video updated.');
    }

    public function destroy(Request $request, Course $course, Video $video)
    {
        $this->authorizeOwnership($request, $course);
        abort_unless($video->course_id === $course->id, 404);

        $video->delete();

        return back()->with('status', 'Video deleted.');
    }

    protected function authorizeOwnership(Request $request, Course $course): void
    {
        abort_unless(
            $course->franchise_booking_id && $course->franchiseBooking->user_id === $request->user()->id,
            403
        );
    }
}
