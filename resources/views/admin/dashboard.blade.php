@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-speedometer2"></i>Admin Panel</div>
    <h4>Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4>
    <p>Here's what's happening across R-Tech Computer today.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $totalStudents }}</b><span>Students</span></div>
      <div><b>{{ $activeCourses }}</b><span>Active Courses</span></div>
      <div><b>₹{{ number_format($totalRevenue / 100000, 1) }}L</b><span>Total Revenue</span></div>
      <div><b>{{ $certificatesIssued }}</b><span>Certificates</span></div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card-rt mb-4">
        <div class="card-title">Recent Enrollments</div>
        <div class="table-wrap"><table class="table-rt">
          <thead><tr><th>Student</th><th>Course</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
            @forelse ($recentEnrollments as $e)
              <tr><td>{{ $e->user->name }}</td><td>{{ $e->course->name }}</td><td>₹{{ number_format($e->final_amount, 0) }}</td><td><span class="badge-rt bg-paid">{{ ucfirst($e->status) }}</span></td></tr>
            @empty
              <tr><td colspan="4" style="color:var(--muted)">No enrollments yet.</td></tr>
            @endforelse
          </tbody>
        </table></div>
      </div>

      <div class="card-rt">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="card-title mb-0">Recent Payments</div>
          <a href="{{ route('admin.payments.index') }}" style="font-size:12px;color:var(--orange);text-decoration:none;font-weight:600">View All &rarr;</a>
        </div>
        <div class="table-wrap"><table class="table-rt">
          <thead><tr><th>User</th><th>Type</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>
          <tbody>
            @forelse ($recentPayments as $p)
              <tr>
                <td>{{ $p->user->name ?? '—' }}</td>
                <td>{{ $p->payable_type === 'course_enrollment' ? 'Course' : 'Franchise' }}</td>
                <td>₹{{ number_format($p->amount, 0) }}</td>
                <td>{{ $p->method ? ucfirst($p->method) : '—' }}</td>
                <td>
                  @php $badge = ['paid' => 'bg-paid', 'created' => 'bg-pending', 'failed' => 'bg-failed', 'refunded' => 'bg-inactive'][$p->status] ?? 'bg-inactive'; @endphp
                  <span class="badge-rt {{ $badge }}">{{ ucfirst($p->status) }}</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" style="color:var(--muted)">No payments yet.</td></tr>
            @endforelse
          </tbody>
        </table></div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card-rt mb-4">
        <div class="card-title">Revenue by Source</div>
        @include('partials.charts.donut', [
          'segments' => $revenueSegments,
          'centerValue' => '₹' . number_format(($revenueSegments[0]['value'] + $revenueSegments[1]['value']) / 100000, 1) . 'L',
          'centerLabel' => 'Total',
        ])
      </div>

      <div class="card-rt">
        <div class="card-title">Course Popularity</div>
        <div class="bar-chart">
          @php $max = max(1, $coursePopularity->max('enrollments_count')); @endphp
          @foreach ($coursePopularity as $c)
            <div class="bar-col"><div class="bar-fill" style="height:{{ round(($c->enrollments_count / $max) * 100) }}%"></div><div class="bar-lbl">{{ \Illuminate\Support\Str::limit($c->name, 8, '') }}</div></div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endsection
