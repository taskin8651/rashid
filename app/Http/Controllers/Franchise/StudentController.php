<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $bookingIds = $request->user()->franchiseBookings()->where('status', 'paid')->pluck('id');

        $students = Enrollment::with(['user', 'course'])
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->where('status', 'paid')
            ->latest('enrolled_at')
            ->get();

        return view('franchise.students', compact('students'));
    }
}
