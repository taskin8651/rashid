<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\FranchiseBooking;

class HomeController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'franchiseBooking'])->where('status', 'active')->get();

        $categories = Category::where('status', 'active')
            ->with(['courses' => fn ($q) => $q->where('status', 'active')->with([
                'videos' => fn ($v) => $v->where('type', 'demo')->where('status', 'active'),
                'modules',
            ])])
            ->get();

        $totalDemoVideos = $categories->flatMap->courses->flatMap->videos->count();

        $franchises = FranchiseBooking::where('status', 'paid')
            ->withCount([
                'courses' => fn ($q) => $q->where('status', 'active'),
                'enrollments',
            ])
            ->withMin(['courses as min_course_price' => fn ($q) => $q->where('status', 'active')], 'price')
            ->latest()
            ->get();

        return view('home', compact('courses', 'categories', 'totalDemoVideos', 'franchises'));
    }
}
