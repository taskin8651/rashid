@extends('layouts.student')

@section('title', 'Dashboard')

@section('content')
  <div class="shead mb-4"><h4>Welcome back, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h4><p>Continue your learning journey from where you left off.</p></div>
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="stat"><div class="sicon" style="background:rgba(var(--accent-rgb),.15);color:var(--orange)"><i class="bi bi-collection-play-fill"></i></div><div><div class="snum">{{ $stats['courses_enrolled'] }}</div><div class="slbl">Course Enrolled</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat"><div class="sicon" style="background:rgba(40,120,220,.15);color:var(--info)"><i class="bi bi-play-fill"></i></div><div><div class="snum">{{ $stats['videos_watched'] }}</div><div class="slbl">Videos Watched</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat"><div class="sicon" style="background:rgba(40,180,90,.15);color:var(--ok)"><i class="bi bi-trophy-fill"></i></div><div><div class="snum">{{ $stats['quizzes_passed'] }}</div><div class="slbl">Quizzes Passed</div></div></div></div>
    <div class="col-6 col-md-3"><div class="stat"><div class="sicon" style="background:rgba(160,50,200,.15);color:var(--purple)"><i class="bi bi-award-fill"></i></div><div><div class="snum">{{ $stats['certificates'] }}</div><div class="slbl">Certificates</div></div></div></div>
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
