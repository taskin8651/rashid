@extends('layouts.franchise')

@section('title', 'Training & Marketing Kit')

@section('content')
  <div class="shead"><h4>Training &amp; Marketing Kit</h4><p>Curriculum, brand guidelines and marketing materials from R-Tech Computer</p></div>
  <div class="row g-3">
    @forelse ($resources as $r)
      <div class="col-md-6 col-lg-4">
        <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px">
          <div style="width:44px;height:44px;background:rgba(var(--accent-rgb),.12);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px">📁</div>
          <h6 style="font-size:14px;font-weight:700;margin-bottom:4px">{{ $r->title }}</h6>
          <p style="font-size:12px;color:var(--muted);margin-bottom:14px">{{ $r->description ?? $r->original_name }}</p>
          <a class="bsave d-block text-center" style="font-size:12px;padding:8px" href="{{ route('franchise.resources.download', $r) }}"><i class="bi bi-download me-1"></i>Download</a>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">No resources have been shared yet.</p></div>
    @endforelse
  </div>
@endsection
