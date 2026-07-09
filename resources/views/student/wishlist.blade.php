@extends('layouts.student')

@section('title', 'Wishlist')

@section('content')
  <div class="shead"><h4>Wishlist</h4><p>Courses you want to take</p></div>
  <div class="row g-3">
    @forelse ($wishlist as $w)
      <div class="col-md-6">
        <div class="wcard">
          <div class="wicon" style="background:rgba(160,50,200,.15)">🎓</div>
          <div style="flex:1"><h6 style="font-size:14px;font-weight:700;margin-bottom:2px">{{ $w->name }}</h6></div>
          <div style="text-align:right">
            <div style="font-size:16px;font-weight:700;color:var(--orange);margin-bottom:6px">₹{{ number_format($w->price, 0) }}</div>
            <form method="POST" action="{{ route('student.wishlist.toggle') }}">
              @csrf
              <input type="hidden" name="course_id" value="{{ $w->id }}">
              <button class="bsave" style="font-size:12px;padding:6px 14px" type="submit">Remove</button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">Your wishlist is empty. <a href="{{ route('courses') }}" style="color:var(--orange)">Browse courses →</a></p></div>
    @endforelse
  </div>
@endsection
