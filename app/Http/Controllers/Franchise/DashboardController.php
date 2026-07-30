<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\FranchiseBooking;
use App\Models\FranchiseResource;
use App\Models\Payment;
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

        return view('franchise.dashboard', compact('bookings', 'stages', 'stats', 'revenueSegments', 'totalRevenue', 'recentPayments'));
    }
}
