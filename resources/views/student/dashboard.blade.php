@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-mortarboard-fill"></i>Student Dashboard</div>
    <h4>Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4>
    <p>Continue your learning journey from where you left off.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['courses_enrolled'] }}</b><span>Courses</span></div>
      <div><b>{{ $stats['videos_watched'] }}</b><span>Videos Watched</span></div>
      <div><b>{{ $stats['quizzes_passed'] }}</b><span>Quizzes Passed</span></div>
      <div><b>{{ $stats['certificates'] }}</b><span>Certificates</span></div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-6">
      <div class="card-rt h-100">
        <div class="card-title">My Learning Progress</div>
        @if ($stats['courses_enrolled'])
          @include('partials.charts.donut', [
            'segments' => $progressSegments,
            'centerValue' => $stats['courses_enrolled'],
            'centerLabel' => \Illuminate\Support\Str::plural('Course', $stats['courses_enrolled']),
          ])
        @else
          <p style="font-size:13px;color:var(--muted);margin:0">Enroll in a course to see your progress here.</p>
        @endif
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card-rt h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="card-title mb-0">Recent Payments</div>
          <a href="{{ route('student.payments.index') }}" style="font-size:12px;color:var(--orange);text-decoration:none;font-weight:600">View All &rarr;</a>
        </div>
        <div class="table-wrap"><table class="table-rt">
          <thead><tr><th>Course</th><th>Fee</th><th>Balance</th></tr></thead>
          <tbody>
            @forelse ($recentPayments as $e)
              <tr>
                <td>{{ \Illuminate\Support\Str::limit($e->course->name, 22) }}</td>
                <td>₹{{ number_format($e->final_amount, 0) }}</td>
                <td>
                  <span class="badge-rt {{ $e->balance_due > 0 ? 'bg-pending' : 'bg-active' }}">{{ $e->balance_due > 0 ? '₹'.number_format($e->balance_due, 0).' due' : 'Fully Paid' }}</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="3" style="color:var(--muted)">No payments yet.</td></tr>
            @endforelse
          </tbody>
        </table></div>
        @if ($stats['courses_enrolled'])
          <p style="font-size:12px;color:var(--muted);margin:14px 0 0">Paid <strong style="color:var(--ok)">₹{{ number_format($totalPaid, 0) }}</strong> of <strong style="color:var(--text)">₹{{ number_format($totalSpent, 0) }}</strong> total fee</p>
        @endif
      </div>
    </div>
  </div>

  <h5 style="font-size:16px;font-weight:700;margin-bottom:16px">Your Enrolled Courses</h5>
  @forelse ($myCourses as $mc)
    <div class="mcc mb-3">
      <div class="cthumb" style="background:linear-gradient(135deg,#0d1e3c,#142a52)">🎓</div>
      <div class="cinfo">
        <div class="d-flex justify-content-between mb-1"><h6 style="font-size:16px;font-weight:700;margin:0">{{ $mc['course']->name }}</h6><span style="font-size:12px;color:var(--orange);font-weight:700">{{ $mc['percent_complete'] }}% Complete</span></div>
        <p style="font-size:13px;color:var(--muted);margin-bottom:12px">{{ $mc['videos_watched'] }}/{{ $mc['total_videos'] }} videos watched · Enrolled {{ $mc['enrolled_at'] }}</p>
        <div class="ptrack"><div class="pfill" style="width:{{ $mc['percent_complete'] }}%"></div></div>
      </div>
    </div>
  @empty
    <p style="font-size:13px;color:var(--muted)">You are not enrolled in any course yet. <a href="{{ route('courses') }}" style="color:var(--orange)">Browse courses →</a></p>
  @endforelse
@endsection
