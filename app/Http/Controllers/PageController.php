<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use App\Models\FranchiseBooking;
use App\Models\Review;

class PageController extends Controller
{
    public function about()
    {
        $testimonials = Review::where('status', 'approved')->where('is_featured', true)
            ->with(['user', 'course'])->latest()->take(6)->get();

        $stats = [
            'courses' => Course::where('status', 'active')->count(),
            'franchises' => FranchiseBooking::where('status', 'paid')->count(),
        ];

        return view('about', compact('testimonials', 'stats'));
    }

    public function freeDemo()
    {
        $categories = Category::where('status', 'active')
            ->with(['courses' => fn ($q) => $q->where('status', 'active')->with([
                'videos' => fn ($v) => $v->where('type', 'demo')->where('status', 'active'),
                'modules',
            ])])
            ->get();

        $totalDemoVideos = $categories->flatMap->courses->flatMap->videos->count();

        return view('free-demo', compact('categories', 'totalDemoVideos'));
    }

    public function whyRtech()
    {
        return view('why-rtech');
    }

    public function privacyPolicy()
    {
        return view('legal.privacy-policy');
    }

    public function terms()
    {
        return view('legal.terms');
    }

    public function refundPolicy()
    {
        return view('legal.refund-policy');
    }
}
