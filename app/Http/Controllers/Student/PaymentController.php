<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $enrollments = $request->user()->enrollments()
            ->with(['course', 'payments' => fn ($q) => $q->where('status', 'paid')->orderByDesc('paid_at')->orderByDesc('created_at')])
            ->whereIn('status', ['paid', 'completed'])
            ->latest('enrolled_at')
            ->get();

        $totalFee = $enrollments->sum('final_amount');
        $totalPaid = $enrollments->sum('amount_paid');

        return view('student.payments', compact('enrollments', 'totalFee', 'totalPaid'));
    }
}
