<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;

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

        return view('home', compact('courses', 'categories', 'totalDemoVideos'));
    }
}
