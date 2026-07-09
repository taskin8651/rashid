<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = $request->user()->enrollments()->with(['course', 'payment'])->get()->map(fn ($e) => [
            'course_name' => $e->course->name,
            'transaction_id' => $e->payment->razorpay_payment_id ?? null,
            'date' => $e->enrolled_at ? $e->enrolled_at->format('d M Y') : $e->created_at->format('d M Y'),
            'amount' => (float) $e->final_amount,
            'status' => $e->status === 'paid' ? 'Paid' : ucfirst($e->status),
        ]);

        return view('student.payments', compact('payments'));
    }
}
