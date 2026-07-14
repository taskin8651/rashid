<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\FranchiseBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()->franchiseBookings()->with(['courses.category', 'courses.modules'])->latest()->get();
        $bookings->flatMap->courses->loadCount('videos');
        $categories = Category::where('status', 'active')->get();

        return view('franchise.courses', compact('bookings', 'categories'));
    }

    public function store(Request $request)
    {
        $booking = FranchiseBooking::where('id', $request->input('franchise_booking_id'))
            ->where('user_id', $request->user()->id)
            ->where('status', 'paid')
            ->firstOrFail();

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_text' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'modules_text' => ['nullable', 'string'],
        ]);

        $modules = $this->splitModules($validated['modules_text'] ?? '');
        unset($validated['modules_text']);

        $validated['franchise_booking_id'] = $booking->id;
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::slug($booking->city) . '-' . Str::random(4);
        $validated['status'] = 'active';

        $course = Course::create($validated);

        foreach ($modules as $i => $title) {
            CourseModule::create(['course_id' => $course->id, 'title' => $title, 'sort_order' => $i]);
        }

        return back()->with('status', 'Course added! It is now live on the R-Tech website.');
    }

    public function update(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $validated = $request->validate([
            'category_id' => ['sometimes', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'duration_text' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
            'modules_text' => ['nullable', 'string'],
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

        return back()->with('status', 'Course updated.');
    }

    public function destroy(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $course->delete();

        return back()->with('status', 'Course removed.');
    }

    protected function authorizeOwnership(Request $request, Course $course): void
    {
        abort_unless(
            $course->franchise_booking_id && $course->franchiseBooking->user_id === $request->user()->id,
            403
        );
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
}
