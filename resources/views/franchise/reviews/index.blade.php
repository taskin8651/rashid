@extends('layouts.franchise')

@section('title', 'Reviews')

@section('content')
  <div class="shead mb-4"><h4>Reviews</h4><p>Feedback your students left on your courses</p></div>

  <div class="row g-4">
    @forelse ($reviews as $r)
      <div class="col-md-6 col-lg-4">
        <div class="tc">
          <div class="stars">{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}</div>
          <p>"{{ $r->comment }}"</p>
          <div class="rr">
            <div class="ra" style="background:var(--grad)">{{ strtoupper(substr($r->user->name ?? '?', 0, 1)) }}</div>
            <div><div class="rname">{{ $r->user->name ?? '—' }}</div><div class="rrole">{{ $r->course->name ?? '—' }}</div></div>
          </div>
          <div class="mt-2">
            @php $badge = ['pending' => 'bg-pending', 'approved' => 'bg-active', 'rejected' => 'bg-failed'][$r->status] ?? 'bg-inactive'; @endphp
            <span class="badge-rt {{ $badge }}">{{ ucfirst($r->status) }}</span>
            @if ($r->is_featured)
              <span class="badge-rt bg-active" title="Shown on the public website"><i class="bi bi-star-fill"></i> Featured</span>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">No reviews on your courses yet.</p></div>
    @endforelse
  </div>
  <div class="mt-4">{{ $reviews->links() }}</div>
@endsection
