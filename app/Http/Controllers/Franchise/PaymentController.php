<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PaymentController extends Controller
{
    use AuthorizesFranchiseAccess;

    protected function ownEnrollmentIds(Request $request)
    {
        $user = $request->user();
        $bookingIds = $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'view-students'))
            ->values();

        $courseIds = Course::whereIn('franchise_booking_id', $bookingIds)->pluck('id');

        return Enrollment::whereIn('course_id', $courseIds)->pluck('id');
    }

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');

        $enrollmentIds = $this->ownEnrollmentIds($request);

        $payments = Payment::with('user')
            ->where('payable_type', 'course_enrollment')
            ->whereIn('payable_id', $enrollmentIds)
            ->latest()
            ->paginate(20);

        $totalIncome = Payment::where('payable_type', 'course_enrollment')
            ->whereIn('payable_id', $enrollmentIds)
            ->where('status', 'paid')
            ->sum('amount');

        return view('franchise.payments.index', compact('payments', 'totalIncome'));
    }

    public function export(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');

        $enrollmentIds = $this->ownEnrollmentIds($request);

        $payments = Payment::with('user')
            ->where('payable_type', 'course_enrollment')
            ->whereIn('payable_id', $enrollmentIds)
            ->latest()
            ->get();

        $rows = ["Transaction ID,Student,Course,Amount,Method,Status,Date\n"];
        foreach ($payments as $p) {
            $payable = $p->payable();
            $rows[] = implode(',', [
                $p->razorpay_payment_id ?? '—',
                '"' . str_replace('"', '""', $p->user->name ?? '—') . '"',
                '"' . str_replace('"', '""', $payable->course->name ?? '—') . '"',
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
}
