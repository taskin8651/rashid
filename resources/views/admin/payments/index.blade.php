@extends('layouts.admin')

@section('title', 'Payment Management')

@section('content')
  <div class="shead mb-4"><h4>Payment Management</h4><p>View all transactions processed via Razorpay</p></div>
  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Transaction ID</th><th>Student</th><th>Type</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse ($payments as $p)
          @php $payable = $p->payable(); @endphp
          <tr>
            <td>{{ $p->razorpay_payment_id ?? '—' }}</td>
            <td>{{ $p->user->name ?? ($payable->name ?? '—') }}</td>
            <td>{{ $p->payable_type === 'course_enrollment' ? ($payable->course->name ?? '—') : 'Franchise Booking' }}</td>
            <td>₹{{ number_format($p->amount, 0) }}</td>
            <td>{{ $p->method ?? '—' }}</td>
            <td>{{ $p->created_at->format('d M Y') }}</td>
            <td><span class="badge-rt {{ $p->status === 'paid' ? 'bg-paid' : ($p->status === 'failed' ? 'bg-failed' : 'bg-pending') }}">{{ ucfirst($p->status) }}</span></td>
          </tr>
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No payments yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $payments->links() }}</div>
@endsection
