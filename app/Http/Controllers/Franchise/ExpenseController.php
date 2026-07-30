<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    use AuthorizesFranchiseAccess;

    protected function permittedBookingIds(Request $request)
    {
        $user = $request->user();

        return $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'manage-expenses'))
            ->values();
    }

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'manage-expenses');

        $bookingIds = $this->permittedBookingIds($request);

        $query = Expense::whereIn('franchise_booking_id', $bookingIds)->with(['franchiseBooking', 'recordedBy']);

        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        if ($from = $request->query('from')) {
            $query->whereDate('expense_date', '>=', $from);
        }

        if ($to = $request->query('to')) {
            $query->whereDate('expense_date', '<=', $to);
        }

        $expenses = $query->latest('expense_date')->paginate(20)->withQueryString();

        $totalExpenses = Expense::whereIn('franchise_booking_id', $bookingIds)->sum('amount');

        $courseIds = Course::whereIn('franchise_booking_id', $bookingIds)->pluck('id');
        $enrollmentIds = Enrollment::whereIn('course_id', $courseIds)->pluck('id');
        $totalIncome = Payment::where('payable_type', 'course_enrollment')
            ->whereIn('payable_id', $enrollmentIds)
            ->where('status', 'paid')
            ->sum('amount');

        $bookings = $request->user()->accessibleFranchiseBookingsQuery()->whereIn('id', $bookingIds)->get();

        return view('franchise.expenses.index', [
            'expenses' => $expenses,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'bookings' => $bookings,
            'category' => $category,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function store(Request $request)
    {
        $bookingIds = $this->permittedBookingIds($request);

        $validated = $request->validate([
            'category' => ['required', Rule::in(Expense::CATEGORIES)],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:500'],
            'franchise_booking_id' => ['required', Rule::in($bookingIds->all())],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($request->hasFile('receipt')) {
            $validated['receipt_path'] = $request->file('receipt')->store('expense-receipts', 'local');
        }
        unset($validated['receipt']);

        $validated['recorded_by'] = $request->user()->id;

        Expense::create($validated);

        return back()->with('status', 'Expense recorded.');
    }

    public function destroy(Request $request, Expense $expense)
    {
        abort_unless($this->permittedBookingIds($request)->contains($expense->franchise_booking_id), 403);

        if ($expense->receipt_path) {
            Storage::disk('local')->delete($expense->receipt_path);
        }

        $expense->delete();

        return back()->with('status', 'Expense deleted.');
    }

    public function downloadReceipt(Request $request, Expense $expense)
    {
        abort_unless($this->permittedBookingIds($request)->contains($expense->franchise_booking_id), 403);
        abort_unless($expense->receipt_path, 404);

        return Storage::disk('local')->download($expense->receipt_path);
    }
}
