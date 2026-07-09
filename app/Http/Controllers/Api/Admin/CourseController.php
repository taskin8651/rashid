<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('category')->withCount('videos')->get()->map(fn (Course $c) => [
            'id' => $c->id,
            'name' => $c->name,
            'category' => $c->category->name ?? null,
            'modules_count' => $c->modules()->count(),
            'videos_count' => $c->videos_count,
            'price' => (float) $c->price,
            'status' => $c->status,
            'thumbnail_url' => $c->thumbnailUrl(),
        ]);

        return response()->json(['courses' => $courses]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_text' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        $course = Course::create($validated);

        foreach ($request->input('modules', []) as $i => $title) {
            CourseModule::create(['course_id' => $course->id, 'title' => $title, 'sort_order' => $i]);
        }

        if ($request->hasFile('thumbnail')) {
            $course->addMediaFromRequest('thumbnail')->toMediaCollection('thumbnail');
        }

        return response()->json(['message' => 'Course created.', 'course_id' => $course->id], 201);
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'duration_text' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
            'modules' => ['nullable', 'array'],
            'modules.*' => ['string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $course->update($validated);

        if ($request->has('modules')) {
            $course->modules()->delete();
            foreach ($request->input('modules', []) as $i => $title) {
                CourseModule::create(['course_id' => $course->id, 'title' => $title, 'sort_order' => $i]);
            }
        }

        if ($request->hasFile('thumbnail')) {
            $course->addMediaFromRequest('thumbnail')->toMediaCollection('thumbnail');
        }

        return response()->json(['message' => 'Course updated.']);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(['message' => 'Course deleted.']);
    }
}
