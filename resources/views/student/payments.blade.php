@extends('layouts.student')

@section('title', 'Payment History')

@section('content')
  <div class="shead"><h4>Payment History</h4><p>Your transaction records</p></div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden">
    <div class="prow" style="background:rgba(var(--text-rgb),.03);font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase"><div style="flex:2">Course</div><div style="flex:1">Date</div><div style="flex:1">Amount</div><div style="flex:1">Status</div></div>
    @forelse ($payments as $p)
      <div class="prow">
        <div style="flex:2"><div style="font-size:14px;font-weight:700">{{ $p['course_name'] }}</div><div style="font-size:11px;color:var(--muted)">TXN: {{ $p['transaction_id'] ?? '—' }}</div></div>
        <div style="flex:1;font-size:13px;color:rgba(var(--text-rgb),.7)">{{ $p['date'] }}</div>
        <div style="flex:1;font-size:15px;font-weight:700;color:var(--orange)">₹{{ number_format($p['amount'], 0) }}</div>
        <div style="flex:1"><span style="font-size:11px;background:rgba(40,180,90,.15);color:var(--ok);padding:3px 10px;border-radius:10px;border:1px solid rgba(125,220,160,.28)">{{ $p['status'] }}</span></div>
      </div>
    @empty
      <div class="prow"><div style="flex:2;font-size:13px;color:var(--muted)">No payments yet.</div></div>
    @endforelse
  </div>
@endsection
