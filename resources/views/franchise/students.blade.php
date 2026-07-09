@extends('layouts.franchise')

@section('title', 'My Students')

@section('content')
  <div class="shead"><h4>My Students</h4><p>Students enrolled in your courses</p></div>
  <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden">
    <div class="prow" style="background:rgba(var(--text-rgb),.03);font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase"><div style="flex:2">Student</div><div style="flex:2">Course</div><div style="flex:1">Enrolled</div><div style="flex:1">Amount</div></div>
    @forelse ($students as $enrollment)
      <div class="prow">
        <div style="flex:2"><div style="font-size:14px;font-weight:700">{{ $enrollment->user->name }}</div><div style="font-size:11px;color:var(--muted)">{{ $enrollment->user->email }}</div></div>
        <div style="flex:2;font-size:13px">{{ $enrollment->course->name }}</div>
        <div style="flex:1;font-size:13px;color:rgba(var(--text-rgb),.7)">{{ optional($enrollment->enrolled_at)->format('d M Y') }}</div>
        <div style="flex:1;font-size:14px;font-weight:700;color:var(--orange)">₹{{ number_format($enrollment->final_amount, 0) }}</div>
      </div>
    @empty
      <div class="prow"><div style="flex:2;font-size:13px;color:var(--muted)">No students enrolled yet.</div></div>
    @endforelse
  </div>
@endsection
