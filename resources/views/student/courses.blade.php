@extends('layouts.student')

@section('title', 'My Courses')

@section('content')
  <div class="shead"><h4>My Courses</h4><p>Courses you're enrolled in</p></div>
  <div class="row g-4">
    @forelse ($myCourses as $mc)
      <div class="col-md-6 col-lg-4">
        <div class="mcc">
          <div class="cthumb" style="background:linear-gradient(135deg,#0d1e3c,#142a52)">🎓</div>
          <div class="cinfo">
            <div class="d-flex justify-content-between mb-1"><h6 style="font-size:15px;font-weight:700;margin:0">{{ $mc['course']->name }}</h6><span style="font-size:11px;background:rgba(40,180,90,.15);color:var(--ok);padding:2px 8px;border-radius:8px;border:1px solid rgba(125,220,160,.28)">Active</span></div>
            <p style="font-size:12px;color:var(--muted);margin:4px 0 10px">{{ $mc['percent_complete'] }}% Completed · {{ $mc['videos_watched'] }}/{{ $mc['total_videos'] }} Videos</p>
            <div class="ptrack"><div class="pfill" style="width:{{ $mc['percent_complete'] }}%"></div></div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">You are not enrolled in any course yet. <a href="{{ route('courses') }}" style="color:var(--orange)">Browse courses →</a></p></div>
    @endforelse
  </div>
@endsection
