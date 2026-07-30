<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Concerns\EnrollsStudentsOffline;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    use AuthorizesFranchiseAccess;
    use EnrollsStudentsOffline;

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');

        $user = $request->user();
        $bookingIds = $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'view-students'))
            ->values();

        $students = Enrollment::with(['user', 'course', 'payments'])
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->where('status', 'paid')
            ->latest('enrolled_at')
            ->get();

        $manageableBookingIds = $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'manage-students'))
            ->values();

        $canManage = $manageableBookingIds->isNotEmpty();
        $courses = Course::whereIn('franchise_booking_id', $manageableBookingIds)->where('status', 'active')->orderBy('name')->get();

        return view('franchise.students', compact('students', 'canManage', 'courses'));
    }

    public function storeOffline(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
            'course_id' => ['required', 'exists:courses,id'],
            'total_fee' => ['required', 'numeric', 'min:0'],
            'first_payment_amount' => ['nullable', 'numeric', 'min:0.01'],
            'first_payment_method' => ['nullable', 'string', 'max:30'],
            'first_payment_note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $bookingIds = $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'manage-students'))
            ->values();

        $course = Course::whereIn('franchise_booking_id', $bookingIds)->findOrFail($validated['course_id']);

        $student = $this->createOrFindStudent($validated);

        $this->enrollStudentOffline($student, $course, (float) $validated['total_fee'], [
            'amount' => $validated['first_payment_amount'] ?? null,
            'method' => $validated['first_payment_method'] ?? null,
            'note' => $validated['first_payment_note'] ?? null,
        ], $request->user()->id);

        return back()->with('status', 'Student registered and allotted to ' . $course->name . '.');
    }

    public function storePayment(Request $request, Enrollment $enrollment)
    {
        $this->authorizeFranchisePermission($request, $enrollment->course->franchise_booking_id ?? 0, 'manage-students');

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
        ]);

        if ($validated['amount'] > $enrollment->balance_due + 0.01) {
            return back()->withErrors(['amount' => 'Amount exceeds the remaining balance of ₹' . number_format($enrollment->balance_due, 2) . '.']);
        }

        Payment::create([
            'payable_type' => 'course_enrollment',
            'payable_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'note' => $validated['note'] ?? null,
            'recorded_by' => $request->user()->id,
            'paid_at' => $validated['paid_at'] ?? now(),
            'status' => 'paid',
        ]);

        return back()->with('status', 'Payment recorded.');
    }

    public function updateFee(Request $request, Enrollment $enrollment)
    {
        $this->authorizeFranchisePermission($request, $enrollment->course->franchise_booking_id ?? 0, 'manage-students');

        $validated = $request->validate([
            'total_fee' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validated['total_fee'] < $enrollment->amount_paid) {
            return back()->withErrors(['total_fee' => 'Total fee cannot be less than the amount already paid (₹' . number_format($enrollment->amount_paid, 2) . ').']);
        }

        $enrollment->update([
            'final_amount' => $validated['total_fee'],
            'discount_amount' => max(0, $enrollment->base_price - $validated['total_fee']),
        ]);

        return back()->with('status', 'Fee updated.');
    }
}
