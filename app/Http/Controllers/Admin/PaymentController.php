<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Support\Facades\Response;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('user')->latest()->paginate(20);

        $totalIncome = Payment::where('status', 'paid')->sum('amount');
        $totalExpenses = Expense::sum('amount');

        return view('admin.payments.index', compact('payments', 'totalIncome', 'totalExpenses'));
    }

    public function export()
    {
        $payments = Payment::with('user')->latest()->get();

        $rows = ["Transaction ID,Student,Type,Amount,Method,Status,Date\n"];
        foreach ($payments as $p) {
            $payable = $p->payable();
            $type = $p->payable_type === 'course_enrollment' ? ($payable->course->name ?? '—') : 'Franchise Booking';
            $rows[] = implode(',', [
                $p->razorpay_payment_id ?? '—',
                '"' . str_replace('"', '""', $p->user->name ?? ($payable->name ?? '—')) . '"',
                '"' . str_replace('"', '""', $type) . '"',
                $p->amount,
                $p->method ?? '—',
                $p->status,
                $p->created_at->format('Y-m-d'),
            ]) . "\n";
        }

        return Response::make(implode('', $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function refund(Payment $payment, RazorpayService $razorpay)
    {
        abort_unless($payment->status === 'paid', 422);

        if (! $payment->razorpay_payment_id) {
            return back()->withErrors(['refund' => 'This was an offline payment — refund it manually and update the fee record instead.']);
        }

        try {
            $refundId = $razorpay->refund($payment->razorpay_payment_id, (float) $payment->amount);
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['refund' => 'Refund failed: ' . $e->getMessage()]);
        }

        $payment->update([
            'status' => 'refunded',
            'razorpay_refund_id' => $refundId,
            'refunded_at' => now(),
        ]);

        return back()->with('status', 'Payment refunded via Razorpay.');
    }
}
