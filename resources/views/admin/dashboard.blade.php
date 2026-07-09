@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
  <div class="shead mb-4"><h4>Welcome back, {{ explode(' ', auth()->user()->name)[0] }} 👋</h4><p>Here's what's happening with R-Tech Computer today.</p></div>
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="stat"><div class="sicon" style="background:rgba(var(--accent-rgb),.15);color:var(--orange)"><i class="bi bi-people-fill"></i></div><div><div class="snum">{{ $totalStudents }}</div><div class="slbl">Total Students</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="stat"><div class="sicon" style="background:rgba(40,120,220,.15);color:var(--info)"><i class="bi bi-collection-play-fill"></i></div><div><div class="snum">{{ $activeCourses }}</div><div class="slbl">Active Courses</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="stat"><div class="sicon" style="background:rgba(40,180,90,.15);color:var(--ok)"><i class="bi bi-cash-stack"></i></div><div><div class="snum">₹{{ number_format($totalRevenue / 100000, 1) }}L</div><div class="slbl">Total Revenue</div></div></div></div>
    <div class="col-6 col-lg-3"><div class="stat"><div class="sicon" style="background:rgba(160,50,200,.15);color:var(--purple)"><i class="bi bi-award-fill"></i></div><div><div class="snum">{{ $certificatesIssued }}</div><div class="slbl">Certificates Issued</div></div></div></div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
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
    </div>
    <div class="col-lg-4">
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
