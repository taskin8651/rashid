@extends('layouts.franchise')

@section('title', 'Expenses')

@php
  $categoryIcon = ['rent' => 'bi-building', 'salary' => 'bi-people-fill', 'marketing' => 'bi-megaphone-fill', 'utilities' => 'bi-lightning-charge-fill', 'equipment' => 'bi-pc-display', 'maintenance' => 'bi-tools', 'other' => 'bi-three-dots'];
  $net = $totalIncome - $totalExpenses;
@endphp

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div><h4>Expenses</h4><p>Your institute's income, expenses and net profit</p></div>
    <button class="bsave" data-bs-toggle="modal" data-bs-target="#addExpense"><i class="bi bi-plus-lg me-1"></i>Add Expense</button>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card-rt text-center" style="padding:20px">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Total Income</div>
        <div style="font-size:24px;font-weight:700;color:var(--ok)">₹{{ number_format($totalIncome, 0) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-rt text-center" style="padding:20px">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Total Expenses</div>
        <div style="font-size:24px;font-weight:700;color:var(--danger)">₹{{ number_format($totalExpenses, 0) }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-rt text-center" style="padding:20px">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Net Profit</div>
        <div style="font-size:24px;font-weight:700;color:{{ $net >= 0 ? 'var(--ok)' : 'var(--danger)' }}">₹{{ number_format($net, 0) }}</div>
      </div>
    </div>
  </div>

  <form method="GET" action="{{ route('franchise.expenses.index') }}" class="d-flex gap-2 flex-wrap mb-3">
    <select class="fctrl" name="category" style="width:auto" onchange="this.form.submit()">
      <option value="">All Categories</option>
      @foreach (\App\Models\Expense::CATEGORIES as $c)
        <option value="{{ $c }}" @selected($category === $c)>{{ ucfirst($c) }}</option>
      @endforeach
    </select>
    <input class="fctrl" type="date" name="from" value="{{ $from }}" style="width:auto"/>
    <input class="fctrl" type="date" name="to" value="{{ $to }}" style="width:auto"/>
    <button class="bghost" type="submit" style="font-size:12px;padding:9px 16px">Filter</button>
    @if ($category || $from || $to)
      <a href="{{ route('franchise.expenses.index') }}" class="bghost" style="text-decoration:none;font-size:12px;padding:9px 16px">Clear</a>
    @endif
  </form>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Date</th><th>Category</th><th>Title</th>@if ($bookings->count() > 1)<th>Institute</th>@endif<th>Amount</th><th>Method</th><th>Recorded By</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($expenses as $e)
          <tr>
            <td>{{ $e->expense_date->format('d M Y') }}</td>
            <td><span class="badge-rt bg-inactive"><i class="bi {{ $categoryIcon[$e->category] ?? 'bi-tag-fill' }} me-1"></i>{{ ucfirst($e->category) }}</span></td>
            <td>{{ $e->title }}{{ $e->note ? ' — '.$e->note : '' }}</td>
            @if ($bookings->count() > 1)<td>{{ $e->franchiseBooking->city ?? '—' }}</td>@endif
            <td style="font-weight:700;color:var(--danger)">₹{{ number_format($e->amount, 0) }}</td>
            <td>{{ $e->method ? ucfirst(str_replace('_', ' ', $e->method)) : '—' }}</td>
            <td>{{ $e->recordedBy->name ?? '—' }}</td>
            <td>
              @if ($e->receipt_path)
                <a href="{{ route('franchise.expenses.receipt', $e) }}" class="action-btn" title="Download Receipt"><i class="bi bi-paperclip"></i></a>
              @endif
              <form method="POST" action="{{ route('franchise.expenses.destroy', $e) }}" onsubmit="return confirm('Delete this expense?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="color:var(--muted)">No expenses recorded yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $expenses->links() }}</div>

  <!-- Add Expense Modal -->
  <div class="modal fade" id="addExpense" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Expense</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('franchise.expenses.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            @if ($bookings->count() > 1)
              <div class="mb-3">
                <label class="flbl">Institute</label>
                <select class="fctrl" name="franchise_booking_id" required>
                  @foreach ($bookings as $b)
                    <option value="{{ $b->id }}">{{ $b->city }}</option>
                  @endforeach
                </select>
              </div>
            @else
              <input type="hidden" name="franchise_booking_id" value="{{ $bookings->first()->id ?? '' }}"/>
            @endif
            <div class="row g-3 mb-3">
              <div class="col-6">
                <label class="flbl">Category</label>
                <select class="fctrl" name="category" required>
                  @foreach (\App\Models\Expense::CATEGORIES as $c)
                    <option value="{{ $c }}">{{ ucfirst($c) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6"><label class="flbl">Date</label><input class="fctrl" type="date" name="expense_date" value="{{ now()->format('Y-m-d') }}" required/></div>
            </div>
            <div class="mb-3"><label class="flbl">Title</label><input class="fctrl" name="title" placeholder="e.g. Office rent — July" required/></div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Amount (₹)</label><input class="fctrl" type="number" step="0.01" min="0.01" name="amount" required/></div>
              <div class="col-6">
                <label class="flbl">Paid Via</label>
                <select class="fctrl" name="method">
                  <option value="cash">Cash</option>
                  <option value="upi">UPI</option>
                  <option value="bank_transfer">Bank Transfer</option>
                  <option value="cheque">Cheque</option>
                  <option value="card">Card</option>
                </select>
              </div>
            </div>
            <div class="mb-3"><label class="flbl">Note (optional)</label><input class="fctrl" name="note" placeholder="Additional detail"/></div>
            <div class="mb-1"><label class="flbl">Receipt (optional)</label><input class="fctrl" type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"/></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Save Expense</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
