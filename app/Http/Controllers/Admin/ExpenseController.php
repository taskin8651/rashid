<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\FranchiseBooking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['franchiseBooking', 'recordedBy']);

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

        $totalIncome = Payment::where('status', 'paid')->sum('amount');
        $totalExpenses = Expense::sum('amount');
        $bookings = FranchiseBooking::where('status', 'paid')->orderBy('city')->get();

        return view('admin.expenses.index', [
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
        $validated = $request->validate([
            'category' => ['required', Rule::in(Expense::CATEGORIES)],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'expense_date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:500'],
            'franchise_booking_id' => ['nullable', 'exists:franchise_bookings,id'],
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

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) {
            Storage::disk('local')->delete($expense->receipt_path);
        }

        $expense->delete();

        return back()->with('status', 'Expense deleted.');
    }

    public function downloadReceipt(Expense $expense)
    {
        abort_unless($expense->receipt_path, 404);

        return Storage::disk('local')->download($expense->receipt_path);
    }

    public function export()
    {
        $expenses = Expense::with(['franchiseBooking', 'recordedBy'])->latest('expense_date')->get();

        $rows = ["Date,Category,Title,Franchise,Amount,Method,Recorded By,Note\n"];
        foreach ($expenses as $e) {
            $rows[] = implode(',', [
                $e->expense_date->format('Y-m-d'),
                ucfirst($e->category),
                '"' . str_replace('"', '""', $e->title) . '"',
                '"' . str_replace('"', '""', $e->franchiseBooking->city ?? 'Head Office') . '"',
                $e->amount,
                $e->method ?? '—',
                '"' . str_replace('"', '""', $e->recordedBy->name ?? '—') . '"',
                '"' . str_replace('"', '""', $e->note ?? '') . '"',
            ]) . "\n";
        }

        return Response::make(implode('', $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="expenses-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
