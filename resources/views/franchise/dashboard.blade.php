@extends('layouts.franchise')

@section('title', 'Overview')

@section('content')
  <div class="shead mb-4"><h4>Welcome, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4><p>Track your franchise application progress.</p></div>

  @forelse ($bookings as $booking)
    <div class="mcc mb-4" style="flex-direction:column;align-items:stretch">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
          <h5 style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:2px">{{ $booking->city }}</h5>
          <p style="font-size:12px;color:var(--muted);margin:0">Booking #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }} · ₹{{ number_format($booking->amount, 0) }}</p>
        </div>
        <span class="badge-rt {{ $booking->status === 'paid' ? 'bg-paid' : ($booking->status === 'failed' ? 'bg-failed' : 'bg-pending') }}">{{ ucfirst($booking->status) }}</span>
      </div>

      @if ($booking->status === 'paid')
        <h6 style="font-size:13px;font-weight:700;color:var(--text);margin-bottom:14px">Application Progress</h6>
        @foreach ($stages as $key => $label)
          <div class="d-flex align-items-center gap-3 mb-2">
            <div style="width:26px;height:26px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;
              @if($key === $booking->stage) background:var(--grad);color:#fff;
              @else background:rgba(var(--text-rgb),.06);color:var(--muted);
              @endif">
              @if($key === $booking->stage)<i class="bi bi-check-lg"></i>@else {{ $loop->iteration }} @endif
            </div>
            <span style="font-size:13px;color:{{ $key === $booking->stage ? '#fff' : 'var(--muted)' }}">{{ $label }}</span>
          </div>
        @endforeach
      @else
        <p style="font-size:13px;color:var(--muted)">Payment for this registration is not yet complete.</p>
      @endif
    </div>
  @empty
    <p style="font-size:13px;color:var(--muted)">You haven't registered a franchise yet. <a href="{{ route('franchise') }}" style="color:var(--orange)">Reserve your city →</a></p>
  @endforelse

  <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;text-align:center">
    <p style="font-size:13px;color:var(--muted);margin-bottom:8px">Questions about your application?</p>
    <a href="https://wa.me/919117744925" style="color:var(--orange);text-decoration:none;font-size:13px"><i class="bi bi-whatsapp me-1"></i>Chat with our franchise team</a>
  </div>
@endsection
