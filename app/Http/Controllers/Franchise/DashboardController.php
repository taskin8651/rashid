<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLocation;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\FranchiseBooking;
use App\Models\FranchiseResource;
use App\Models\FranchiseTeamMember;
use App\Models\Payment;
use App\Models\StudentLead;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected const PALETTE = ['37,99,235', '245,166,35', '16,185,129', '168,85,247', '239,68,68', '107,127,163'];

    public function index(Request $request)
    {
        $bookings = $request->user()->accessibleFranchiseBookingsQuery()
            ->withCount('courses')
            ->with('documents')
            ->latest()
            ->get();

        $paidBookingIds = $bookings->where('status', 'paid')->pluck('id');

        $courses = Course::whereIn('franchise_booking_id', $paidBookingIds)
            ->withCount(['enrollments' => fn ($q) => $q->where('status', 'paid')])
            ->withSum(['enrollments as revenue' => fn ($q) => $q->where('status', 'paid')], 'final_amount')
            ->orderByDesc('revenue')
            ->get();

        $totalStudents = $courses->sum('enrollments_count');
        $totalResources = FranchiseResource::count();

        $stats = [
            'courses' => $bookings->sum('courses_count'),
            'students' => $totalStudents,
            'resources' => $totalResources,
        ];

        $revenueSegments = $courses->take(5)->values()->map(fn ($c, $i) => [
            'label' => $c->name,
            'value' => (float) $c->revenue,
            'color' => self::PALETTE[$i % count(self::PALETTE)],
            'display' => '₹' . number_format($c->revenue, 0),
        ])->all();

        $totalRevenue = $courses->sum('revenue');

        $enrollmentIds = Enrollment::whereIn('course_id', $courses->pluck('id'))->pluck('id');

        $recentPayments = Payment::with('user')
            ->where(function ($q) use ($enrollmentIds, $paidBookingIds) {
                $q->where(fn ($q2) => $q2->where('payable_type', 'course_enrollment')->whereIn('payable_id', $enrollmentIds))
                    ->orWhere(fn ($q2) => $q2->where('payable_type', 'franchise_booking')->whereIn('payable_id', $paidBookingIds));
            })
            ->latest()
            ->take(6)
            ->get();

        $stages = FranchiseBooking::STAGES;

        // Leads and expenses — everything relevant to this franchise, not
        // gated by manage-leads/manage-expenses here since the dashboard is
        // a read-only overview; the dedicated pages still enforce permission
        // for actually acting on that data.
        $leadCounts = StudentLead::whereIn('franchise_booking_id', $paidBookingIds)
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
        $totalLeads = $leadCounts->sum();
        $pendingLeads = collect(['new', 'contacted', 'follow_up', 'interested'])->sum(fn ($s) => $leadCounts[$s] ?? 0);

        $totalExpenses = Expense::whereIn('franchise_booking_id', $paidBookingIds)->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        // Who's active right now among people tied to this franchise —
        // owner(s), team members, and their enrolled students.
        $studentIds = Enrollment::whereIn('course_id', $courses->pluck('id'))->where('status', 'paid')->pluck('user_id');
        $teamIds = FranchiseTeamMember::whereIn('franchise_booking_id', $paidBookingIds)->pluck('user_id');
        $ownerIds = $bookings->pluck('user_id');
        $relevantUserIds = $studentIds->merge($teamIds)->merge($ownerIds)->unique();

        $activeUsersNow = User::whereIn('id', $relevantUserIds)->activeNow()->orderByDesc('last_seen_at')->take(12)->get();
        $activeUsersCount = User::whereIn('id', $relevantUserIds)->activeNow()->count();

        // Today's attendance — who from this institute checked in today.
        // Attendance is opt-in (no fixed roster per session), so "not marked"
        // means "hasn't checked in yet today," not "absent from class."
        $attendanceLocationIds = AttendanceLocation::whereIn('franchise_booking_id', $paidBookingIds)->pluck('id');
        $hasAttendanceLocation = $attendanceLocationIds->isNotEmpty();

        $presentTodayIds = Attendance::whereIn('attendance_location_id', $attendanceLocationIds)
            ->whereIn('user_id', $studentIds)
            ->whereDate('date', today())
            ->distinct()
            ->pluck('user_id');
        $presentTodayCount = $presentTodayIds->count();
        $notMarkedTodayCount = max(0, $studentIds->count() - $presentTodayCount);
        $presentTodayList = User::whereIn('id', $presentTodayIds)->orderBy('name')->take(10)->get();
        $notMarkedTodayList = User::whereIn('id', $studentIds)->whereNotIn('id', $presentTodayIds)->orderBy('name')->take(10)->get();

        return view('franchise.dashboard', compact(
            'bookings', 'stages', 'stats', 'revenueSegments', 'totalRevenue', 'recentPayments',
            'totalLeads', 'pendingLeads', 'totalExpenses', 'netProfit',
            'activeUsersNow', 'activeUsersCount',
            'hasAttendanceLocation', 'presentTodayCount', 'notMarkedTodayCount', 'presentTodayList', 'notMarkedTodayList'
        ));
    }
}
