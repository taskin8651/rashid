<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'franchiseBooking'])->withCount('videos')->get();
        $categories = Category::all();

        return view('admin.courses.index', compact('courses', 'categories'));
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
            'modules_text' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $modules = $this->splitModules($validated['modules_text'] ?? '');
        unset($validated['modules_text']);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(4);

        $course = Course::create($validated);

        foreach ($modules as $i => $title) {
            CourseModule::create(['course_id' => $course->id, 'title' => $title, 'sort_order' => $i]);
        }

        if ($request->hasFile('thumbnail')) {
            $course->addMediaFromRequest('thumbnail')->toMediaCollection('thumbnail');
        }

        return redirect()->route('admin.courses.index')->with('status', 'Course created.');
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
            'modules_text' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        $modules = $this->splitModules($validated['modules_text'] ?? null);
        unset($validated['modules_text']);

        $course->update($validated);

        if ($modules !== null) {
            $course->modules()->delete();
            foreach ($modules as $i => $title) {
                CourseModule::create(['course_id' => $course->id, 'title' => $title, 'sort_order' => $i]);
            }
        }

        if ($request->hasFile('thumbnail')) {
            $course->addMediaFromRequest('thumbnail')->toMediaCollection('thumbnail');
        }

        return redirect()->route('admin.courses.index')->with('status', 'Course updated.');
    }

    protected function splitModules(?string $text): ?array
    {
        if ($text === null) {
            return null;
        }

        return collect(explode("\n", $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return back()->with('status', 'Course deleted.');
    }
}
