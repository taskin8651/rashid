<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $totalStudents = User::role('student')->count();
        $activeCourses = Course::where('status', 'active')->count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');
        $certificatesIssued = Certificate::where('status', 'issued')->count();

        $recentEnrollments = Enrollment::with(['user', 'course'])
            ->where('status', 'paid')
            ->latest('enrolled_at')
            ->take(10)
            ->get()
            ->map(fn (Enrollment $e) => [
                'student' => $e->user->name,
                'course' => $e->course->name,
                'amount' => (float) $e->final_amount,
                'status' => ucfirst($e->status),
            ]);

        $coursePopularity = Course::withCount(['enrollments' => fn ($q) => $q->where('status', 'paid')])
            ->get()
            ->map(fn (Course $c) => ['course' => $c->name, 'enrollments' => $c->enrollments_count]);

        return response()->json([
            'total_students' => $totalStudents,
            'active_courses' => $activeCourses,
            'total_revenue' => (float) $totalRevenue,
            'certificates_issued' => $certificatesIssued,
            'recent_enrollments' => $recentEnrollments,
            'course_popularity' => $coursePopularity,
        ]);
    }
}
