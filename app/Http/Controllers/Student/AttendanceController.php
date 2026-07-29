<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = $request->user()
            ->attendances()
            ->with('location')
            ->latest('date')
            ->paginate(20);

        $stats = [
            'total' => $request->user()->attendances()->count(),
            'this_month' => $request->user()->attendances()->whereMonth('date', now()->month)->whereYear('date', now()->year)->count(),
        ];

        return view('student.attendance', compact('attendances', 'stats'));
    }
}
