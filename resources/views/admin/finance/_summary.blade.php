@php $net = $totalIncome - $totalExpenses; @endphp
<div class="row g-3 mb-3">
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

<div class="d-flex gap-2 mb-4">
  <a href="{{ route('admin.payments.index') }}" class="badge-rt {{ request()->routeIs('admin.payments.*') ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none;padding:8px 18px"><i class="bi bi-cash-coin me-1"></i>Income</a>
  <a href="{{ route('admin.expenses.index') }}" class="badge-rt {{ request()->routeIs('admin.expenses.*') ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none;padding:8px 18px"><i class="bi bi-receipt-cutoff me-1"></i>Expenses</a>
</div>
