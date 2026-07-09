<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with('user')->latest()->paginate(20)->through(function (Payment $p) {
            $payable = $p->payable();
            return [
                'id' => $p->id,
                'transaction_id' => $p->razorpay_payment_id,
                'student' => $p->user->name ?? ($payable->name ?? '—'),
                'type' => $p->payable_type === 'course_enrollment' ? ($payable->course->name ?? '—') : 'Franchise Booking',
                'amount' => (float) $p->amount,
                'method' => $p->method,
                'date' => $p->created_at->format('d M Y'),
                'status' => ucfirst($p->status),
            ];
        });

        return response()->json($payments);
    }

    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate(['status' => ['required', Rule::in(['paid', 'failed', 'refunded'])]]);
        $payment->update(['status' => $request->status]);

        return response()->json(['message' => 'Payment status updated.']);
    }
}
