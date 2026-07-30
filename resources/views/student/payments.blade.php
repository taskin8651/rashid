@extends('layouts.student')

@section('title', 'My Fees')

@section('content')
  <div class="shead"><h4>My Fees &amp; Payments</h4><p>Track your course fees and installment payments</p></div>

  @if ($enrollments->isNotEmpty())
    <div class="row g-3 mb-4">
      <div class="col-4">
        <div class="card-rt text-center" style="padding:18px">
          <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Total Fee</div>
          <div style="font-size:20px;font-weight:700">₹{{ number_format($totalFee, 0) }}</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card-rt text-center" style="padding:18px">
          <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Total Paid</div>
          <div style="font-size:20px;font-weight:700;color:var(--ok)">₹{{ number_format($totalPaid, 0) }}</div>
        </div>
      </div>
      <div class="col-4">
        <div class="card-rt text-center" style="padding:18px">
          <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px">Balance Due</div>
          <div style="font-size:20px;font-weight:700;color:{{ $totalFee - $totalPaid > 0 ? 'var(--danger)' : 'var(--ok)' }}">₹{{ number_format($totalFee - $totalPaid, 0) }}</div>
        </div>
      </div>
    </div>
  @endif

  <div class="row g-3">
    @forelse ($enrollments as $e)
      <div class="col-md-6">
        <div class="card-rt">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 style="font-size:14px;font-weight:700;margin:0">{{ $e->course->name }}</h6>
            <span class="badge-rt {{ $e->balance_due > 0 ? 'bg-pending' : 'bg-active' }}">{{ $e->balance_due > 0 ? 'Balance Due' : 'Fully Paid' }}</span>
          </div>
          <div class="d-flex justify-content-between" style="font-size:12px;color:var(--muted)">
            <span>Total Fee</span><span style="font-weight:700;color:var(--text)">₹{{ number_format($e->final_amount, 0) }}</span>
          </div>
          <div class="d-flex justify-content-between" style="font-size:12px;color:var(--muted)">
            <span>Paid So Far</span><span style="font-weight:700;color:var(--ok)">₹{{ number_format($e->amount_paid, 0) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2" style="font-size:12px;color:var(--muted)">
            <span>Balance Due</span><span style="font-weight:700;color:{{ $e->balance_due > 0 ? 'var(--danger)' : 'var(--ok)' }}">₹{{ number_format($e->balance_due, 0) }}</span>
          </div>
          <div class="ptrack mb-3"><div class="pfill" style="width:{{ $e->final_amount > 0 ? min(100, round(($e->amount_paid / $e->final_amount) * 100)) : 100 }}%"></div></div>

          <p style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:8px">Installment History</p>
          @if ($e->payments->isNotEmpty())
            <div style="max-height:170px;overflow-y:auto">
              @foreach ($e->payments as $p)
                <div class="d-flex justify-content-between align-items-start py-2" style="border-bottom:1px solid var(--border);font-size:12px">
                  <div>
                    <div style="font-weight:600">{{ optional($p->paid_at)->format('d M Y') ?? $p->created_at->format('d M Y') }}</div>
                    <div style="color:var(--muted)">{{ ucfirst(str_replace('_', ' ', $p->method ?? 'online')) }}{{ $p->note ? ' · '.$p->note : '' }}</div>
                  </div>
                  <div style="font-weight:700;color:var(--ok);white-space:nowrap">₹{{ number_format($p->amount, 0) }}</div>
                </div>
              @endforeach
            </div>
          @else
            <p style="font-size:12px;color:var(--muted);margin:0">No payments recorded yet.</p>
          @endif
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">You are not enrolled in any course yet. <a href="{{ route('courses') }}" style="color:var(--orange)">Browse courses →</a></p></div>
    @endforelse
  </div>
@endsection
