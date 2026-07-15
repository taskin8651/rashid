<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category');

        $courses = Course::with(['category', 'franchiseBooking'])
            ->where('status', 'active')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->get();

        $categories = Category::where('status', 'active')->get();

        return view('courses', compact('courses', 'categories', 'search', 'categoryId'));
    }

    public function show(Course $course)
    {
        abort_unless($course->status === 'active', 404);

        $course->load([
            'category',
            'franchiseBooking',
            'modules.videos' => fn ($q) => $q->where('status', 'active'),
        ]);

        $demoVideos = $course->videos()->where('type', 'demo')->where('status', 'active')->whereHas('media')->get();

        $batches = $course->batches()->where('status', '!=', 'closed')->get();

        $reviews = $course->approvedReviews()->with('user')->latest()->take(10)->get();

        $ratingBreakdown = [];
        for ($star = 5; $star >= 1; $star--) {
            $ratingBreakdown[$star] = $course->approvedReviews()->where('rating', $star)->count();
        }

        $relatedCourses = Course::where('status', 'active')
            ->where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->take(3)
            ->get();

        $isEnrolled = auth()->check()
            ? $course->enrollments()->where('user_id', auth()->id())->whereIn('status', ['paid', 'completed'])->exists()
            : false;

        return view('course-show', compact('course', 'demoVideos', 'batches', 'reviews', 'ratingBreakdown', 'relatedCourses', 'isEnrolled'));
    }
}
