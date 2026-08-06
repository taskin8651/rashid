@extends('layouts.franchise')

@section('title', 'Payments')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Payments</h4><p>Payments received from your students</p></div>
    <a href="{{ route('franchise.payments.export') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-download me-1"></i>Export CSV</a>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card-rt text-center" style="padding:20px">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px">Total Income</div>
        <div style="font-size:24px;font-weight:700;color:var(--ok)">₹{{ number_format($totalIncome, 0) }}</div>
      </div>
    </div>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Transaction ID</th><th>Student</th><th>Course</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse ($payments as $p)
          @php $payable = $p->payable(); @endphp
          <tr>
            <td>{{ $p->razorpay_payment_id ?? '—' }}</td>
            <td>{{ $p->user->name ?? '—' }}</td>
            <td>{{ $payable->course->name ?? '—' }}</td>
            <td>₹{{ number_format($p->amount, 0) }}</td>
            <td>{{ $p->method ?? '—' }}</td>
            <td>{{ $p->created_at->format('d M Y') }}</td>
            <td>
              @php $badge = ['paid' => 'bg-paid', 'created' => 'bg-pending', 'failed' => 'bg-failed', 'refunded' => 'bg-inactive'][$p->status] ?? 'bg-inactive'; @endphp
              <span class="badge-rt {{ $badge }}">{{ ucfirst($p->status) }}</span>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No payments yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $payments->links() }}</div>
@endsection
