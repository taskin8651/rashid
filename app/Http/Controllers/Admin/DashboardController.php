<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = User::role('student')->count();
        $activeCourses = Course::where('status', 'active')->count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $certificatesIssued = Certificate::where('status', 'issued')->count();

        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->where('status', 'paid')
            ->latest('enrolled_at')
            ->take(10)
            ->get();

        $coursePopularity = Course::withCount(['enrollments' => fn ($q) => $q->where('status', 'paid')])->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'activeCourses', 'totalRevenue', 'certificatesIssued', 'recentEnrollments', 'coursePopularity'
        ));
    }
}
