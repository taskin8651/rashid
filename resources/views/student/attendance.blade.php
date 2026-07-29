@extends('layouts.student')

@section('title', 'Attendance')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-qr-code-scan"></i>Attendance</div>
    <h4>My Attendance</h4>
    <p>Scan the QR code at your institute to check in each day.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['total'] }}</b><span>Total Check-ins</span></div>
      <div><b>{{ $stats['this_month'] }}</b><span>This Month</span></div>
    </div>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Date</th><th>Location</th><th>Time</th><th>Method</th></tr></thead>
      <tbody>
        @forelse ($attendances as $a)
          <tr>
            <td>{{ $a->date->format('d M Y') }}</td>
            <td>{{ $a->location->name }}</td>
            <td>{{ $a->marked_at->format('h:i A') }}</td>
            <td><span class="badge-rt {{ $a->method === 'gps' ? 'bg-active' : 'bg-pending' }}">{{ strtoupper($a->method) }}</span></td>
          </tr>
        @empty
          <tr><td colspan="4" style="color:var(--muted)">No attendance marked yet. Scan the QR code at your institute to check in.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $attendances->links() }}</div>
@endsection
