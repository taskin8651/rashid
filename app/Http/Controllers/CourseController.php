<?php

namespace App\Http\Controllers;

use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['category', 'franchiseBooking'])->where('status', 'active')->get();

        return view('courses', compact('courses'));
    }
}
