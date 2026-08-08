<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Placement;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function index(Request $request)
    {
        $courseId = $request->query('course');

        $placements = Placement::approved()
            ->with(['user', 'course'])
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->orderByDesc('is_featured')
            ->latest('joining_date')
            ->get();

        $stats = [
            'placed' => Placement::approved()->count(),
            'companies' => Placement::approved()->distinct('company_name')->count('company_name'),
        ];

        $courses = Course::whereIn('id', Placement::approved()->pluck('course_id'))->orderBy('name')->get();

        return view('placements', compact('placements', 'stats', 'courses', 'courseId'));
    }
}
