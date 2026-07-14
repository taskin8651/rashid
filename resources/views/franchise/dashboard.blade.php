@extends('layouts.franchise')

@section('title', 'Overview')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-award-fill"></i>Franchise Partner</div>
    <h4>Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4>
    <p>Here's how your R-Tech Computer franchise is doing today.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $bookings->count() }}</b><span>{{ \Illuminate\Support\Str::plural('Center', $bookings->count()) }}</span></div>
      <div><b>{{ $stats['courses'] }}</b><span>{{ \Illuminate\Support\Str::plural('Course', $stats['courses']) }}</span></div>
      <div><b>{{ $stats['students'] }}</b><span>{{ \Illuminate\Support\Str::plural('Student', $stats['students']) }}</span></div>
      <div><b>₹{{ number_format($totalRevenue / 1000, 1) }}K</b><span>Revenue</span></div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <a href="{{ route('franchise.courses.index') }}" class="ov-action">
        <div class="ov-action-icon" style="background:rgba(37,99,235,.14);color:var(--orange)"><i class="bi bi-collection-play-fill"></i></div>
        <div><div class="ov-action-title">My Courses</div><div class="ov-action-sub">Manage &amp; upload</div></div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('franchise.students.index') }}" class="ov-action">
        <div class="ov-action-icon" style="background:rgba(40,180,90,.14);color:var(--ok)"><i class="bi bi-people-fill"></i></div>
        <div><div class="ov-action-title">My Students</div><div class="ov-action-sub">View enrollments</div></div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('franchise.documents.index') }}" class="ov-action">
        <div class="ov-action-icon" style="background:rgba(184,134,11,.14);color:#b8860b"><i class="bi bi-file-earmark-text-fill"></i></div>
        <div><div class="ov-action-title">Documents</div><div class="ov-action-sub">Agreements &amp; KYC</div></div>
      </a>
    </div>
    <div class="col-6 col-md-3">
      <a href="{{ route('franchise.resources.index') }}" class="ov-action">
        <div class="ov-action-icon" style="background:rgba(147,51,234,.14);color:var(--purple)"><i class="bi bi-folder2-open"></i></div>
        <div><div class="ov-action-title">Training Kit</div><div class="ov-action-sub">Marketing assets</div></div>
      </a>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="card-rt h-100">
        <div class="card-title">Revenue by Course</div>
        @if (count($revenueSegments))
          @include('partials.charts.donut', [
            'segments' => $revenueSegments,
            'centerValue' => '₹' . number_format($totalRevenue / 1000, 1) . 'K',
            'centerLabel' => 'Total',
          ])
        @else
          <p style="font-size:13px;color:var(--muted);margin:0">No course revenue yet.</p>
        @endif
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card-rt h-100">
        <div class="card-title">Recent Payments</div>
        <div class="table-wrap"><table class="table-rt">
          <thead><tr><th>From</th><th>Type</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
            @forelse ($recentPayments as $p)
              <tr>
                <td>{{ $p->user->name ?? '—' }}</td>
                <td>{{ $p->payable_type === 'course_enrollment' ? 'Enrollment' : 'Booking Fee' }}</td>
                <td>₹{{ number_format($p->amount, 0) }}</td>
                <td>
                  @php $badge = ['paid' => 'bg-paid', 'created' => 'bg-pending', 'failed' => 'bg-failed', 'refunded' => 'bg-inactive'][$p->status] ?? 'bg-inactive'; @endphp
                  <span class="badge-rt {{ $badge }}">{{ ucfirst($p->status) }}</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" style="color:var(--muted)">No payments yet.</td></tr>
            @endforelse
          </tbody>
        </table></div>
      </div>
    </div>
  </div>

  <div class="shead mb-3"><h4 style="font-size:16px">My Franchise Centers</h4></div>

  @forelse ($bookings as $booking)
    <div class="card-rt mb-4" style="position:relative;overflow:hidden">
      <div style="position:absolute;top:0;left:0;width:5px;height:100%;background:{{ $booking->status === 'paid' ? 'var(--grad)' : 'var(--border)' }}"></div>
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-3">
          <div class="uav" style="width:44px;height:44px;font-size:16px">{{ strtoupper(substr($booking->city, 0, 1)) }}</div>
          <div>
            <h5 style="font-family:'Space Grotesk',sans-serif;font-weight:700;margin-bottom:2px">{{ $booking->city }}</h5>
            <p style="font-size:12px;color:var(--muted);margin:0">Booking #{{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }} &middot; ₹{{ number_format($booking->amount, 0) }} &middot; {{ $booking->courses_count }} {{ \Illuminate\Support\Str::plural('course', $booking->courses_count) }}</p>
          </div>
        </div>
        <span class="badge-rt {{ $booking->status === 'paid' ? 'bg-paid' : ($booking->status === 'failed' ? 'bg-failed' : 'bg-pending') }}">{{ ucfirst($booking->status) }}</span>
      </div>

      @if ($booking->status === 'paid')
        <h6 style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:16px">Application Progress</h6>
        <div class="ov-steps">
          @foreach ($stages as $key => $label)
            <div class="ov-step {{ $loop->index <= $booking->stageIndex() ? 'done' : '' }} {{ $key === $booking->stage ? 'current' : '' }}">
              <div class="ov-step-dot">
                @if ($loop->index < $booking->stageIndex())
                  <i class="bi bi-check-lg"></i>
                @else
                  {{ $loop->iteration }}
                @endif
              </div>
              <div class="ov-step-lbl">{{ $label }}</div>
            </div>
          @endforeach
        </div>
        @if ($booking->documents->count())
          <p style="font-size:11px;color:var(--muted);margin:16px 0 0"><i class="bi bi-paperclip me-1"></i>{{ $booking->documents->count() }} {{ \Illuminate\Support\Str::plural('document', $booking->documents->count()) }} on file</p>
        @endif
      @else
        <p style="font-size:13px;color:var(--muted);margin:0">Payment for this registration is not yet complete.</p>
      @endif
    </div>
  @empty
    <div class="card-rt text-center" style="padding:40px 24px">
      <i class="bi bi-buildings" style="font-size:32px;color:var(--orange);margin-bottom:12px;display:inline-block"></i>
      <p style="font-size:13px;color:var(--muted);margin-bottom:16px">You haven't registered a franchise yet.</p>
      <a href="{{ route('franchise') }}" class="bsave" style="text-decoration:none;display:inline-block"><i class="bi bi-flag-fill me-1"></i>Reserve Your City</a>
    </div>
  @endforelse

  <div class="card-rt text-center">
    <p style="font-size:13px;color:var(--muted);margin-bottom:8px">Questions about your application?</p>
    <a href="https://wa.me/919117744925" style="color:var(--orange);text-decoration:none;font-size:13px;font-weight:600"><i class="bi bi-whatsapp me-1"></i>Chat with our franchise team</a>
  </div>
@endsection
