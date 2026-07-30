<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\FranchiseBooking;
use App\Models\Payment;
use App\Models\StudentLead;
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

        $courseRevenue = Payment::where('status', 'paid')->where('payable_type', 'course_enrollment')->sum('amount');
        $franchiseRevenue = Payment::where('status', 'paid')->where('payable_type', 'franchise_booking')->sum('amount');

        $revenueSegments = [
            ['label' => 'Course Enrollments', 'value' => (float) $courseRevenue, 'color' => '37,99,235', 'display' => '₹' . number_format($courseRevenue, 0)],
            ['label' => 'Franchise Bookings', 'value' => (float) $franchiseRevenue, 'color' => '245,166,35', 'display' => '₹' . number_format($franchiseRevenue, 0)],
        ];

        $recentPayments = Payment::with('user')->latest()->take(6)->get();

        // Business-wide "everything at a glance" stats not covered above.
        $totalFranchises = FranchiseBooking::where('status', 'paid')->count();
        $pendingFranchiseApplications = FranchiseBooking::where('status', 'pending')->count();

        $totalLeads = StudentLead::count();
        $pendingLeads = StudentLead::whereIn('status', ['new', 'contacted', 'follow_up', 'interested'])->count();

        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        $pendingCertApplications = CertificateApplication::where('status', 'pending')->count();

        // Who's active right now — last_seen_at stamped by the track.active
        // middleware on every authenticated request, 5-minute presence window.
        $activeUsersNow = User::activeNow()->with('roles')->orderByDesc('last_seen_at')->take(12)->get();
        $activeUsersCount = User::activeNow()->count();

        // Today's attendance — who checked in vs who hasn't, platform-wide.
        // Attendance is opt-in (no fixed per-day roster in this system), so
        // "not marked" means "hasn't checked in anywhere today," not "absent
        // from a scheduled session."
        $presentTodayIds = Attendance::whereDate('date', today())
            ->whereHas('user', fn ($q) => $q->role('student'))
            ->distinct()
            ->pluck('user_id');
        $presentTodayCount = $presentTodayIds->count();
        $notMarkedTodayCount = max(0, $totalStudents - $presentTodayCount);
        $presentTodayList = User::whereIn('id', $presentTodayIds)->orderBy('name')->take(10)->get();
        $notMarkedTodayList = User::role('student')->whereNotIn('id', $presentTodayIds)->orderBy('name')->take(10)->get();

        return view('admin.dashboard', compact(
            'totalStudents', 'activeCourses', 'totalRevenue', 'certificatesIssued', 'recentEnrollments',
            'coursePopularity', 'revenueSegments', 'recentPayments',
            'totalFranchises', 'pendingFranchiseApplications', 'totalLeads', 'pendingLeads',
            'totalExpenses', 'netProfit', 'pendingCertApplications',
            'activeUsersNow', 'activeUsersCount',
            'presentTodayCount', 'notMarkedTodayCount', 'presentTodayList', 'notMarkedTodayList'
        ));
    }
}
