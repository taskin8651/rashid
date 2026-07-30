@extends('layouts.admin')

@section('title', 'Payment Management')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Payment Management</h4><p>Income, expenses and net profit at a glance</p></div>
    <a href="{{ route('admin.payments.export') }}" class="bghost" style="text-decoration:none;font-size:12px;padding:8px 16px"><i class="bi bi-download me-1"></i>Export CSV</a>
  </div>

  @include('admin.finance._summary')

  @if ($errors->has('refund'))
    <div class="alert alert-danger mb-3">{{ $errors->first('refund') }}</div>
  @endif

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Transaction ID</th><th>Student</th><th>Type</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th><th></th></tr></thead>
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
            <td>
              @php $badge = ['paid' => 'bg-paid', 'created' => 'bg-pending', 'failed' => 'bg-failed', 'refunded' => 'bg-inactive'][$p->status] ?? 'bg-inactive'; @endphp
              <span class="badge-rt {{ $badge }}">{{ ucfirst($p->status) }}</span>
            </td>
            <td>
              @if ($p->status === 'paid' && $p->razorpay_payment_id)
                <form method="POST" action="{{ route('admin.payments.refund', $p) }}" onsubmit="return confirm('Refund ₹{{ number_format($p->amount, 0) }} to {{ $p->user->name ?? 'this customer' }} via Razorpay?')">
                  @csrf
                  <button type="submit" class="bghost" style="font-size:11px;padding:5px 10px;color:var(--danger);border-color:rgba(239,68,68,.3)"><i class="bi bi-arrow-counterclockwise me-1"></i>Refund</button>
                </form>
              @elseif ($p->status === 'paid')
                <span style="font-size:11px;color:var(--muted)">Offline</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" style="color:var(--muted)">No payments yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $payments->links() }}</div>
@endsection
