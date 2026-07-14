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
}
