<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\Faq;
use App\Models\FranchiseBooking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->filled('ref') && User::where('referral_code', $request->query('ref'))->exists()) {
            $request->session()->put('ref_code', $request->query('ref'));
        }

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

        $testimonials = Review::where('status', 'approved')->where('is_featured', true)
            ->with(['user', 'course'])
            ->latest()
            ->take(6)
            ->get();

        $faqs = Faq::where('status', 'active')->orderBy('sort_order')->orderBy('id')->get();

        return view('home', compact('courses', 'categories', 'totalDemoVideos', 'franchises', 'testimonials', 'faqs'));
    }
}
