@extends('layouts.admin')

@section('title', 'Reviews')

@section('content')
  <div class="shead mb-4"><h4>Course Reviews</h4><p>Approve reviews before they count toward a course's rating. Feature the best ones as homepage testimonials.</p></div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.reviews.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="badge-rt {{ $status === 'pending' ? 'bg-pending' : 'bg-inactive' }}" style="text-decoration:none">Pending ({{ $pendingCount }})</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'approved']) }}" class="badge-rt {{ $status === 'approved' ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">Approved</a>
    <a href="{{ route('admin.reviews.index', ['status' => 'rejected']) }}" class="badge-rt {{ $status === 'rejected' ? 'bg-failed' : 'bg-inactive' }}" style="text-decoration:none">Rejected</a>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Course</th><th>Rating</th><th>Comment</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($reviews as $r)
          <tr>
            <td>{{ $r->user->name }}</td>
            <td>{{ $r->course->name }}</td>
            <td>{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5 - $r->rating) }}</td>
            <td style="max-width:280px">{{ $r->comment ?: '—' }}</td>
            <td>
              <span class="badge-rt {{ $r->status === 'approved' ? 'bg-active' : ($r->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($r->status) }}</span>
              @if ($r->is_featured)<span class="badge-rt bg-active ms-1"><i class="bi bi-star-fill"></i></span>@endif
            </td>
            <td class="d-flex gap-1 flex-wrap">
              @if ($r->status !== 'approved')
                <form method="POST" action="{{ route('admin.reviews.approve', $r) }}">
                  @csrf
                  <button type="submit" class="action-btn" title="Approve"><i class="bi bi-check-lg"></i></button>
                </form>
              @endif
              @if ($r->status !== 'rejected')
                <form method="POST" action="{{ route('admin.reviews.reject', $r) }}">
                  @csrf
                  <button type="submit" class="action-btn" title="Reject"><i class="bi bi-x-lg"></i></button>
                </form>
              @endif
              @if ($r->status === 'approved')
                <form method="POST" action="{{ route('admin.reviews.feature', $r) }}">
                  @csrf
                  <button type="submit" class="action-btn {{ $r->is_featured ? 'danger' : '' }}" title="{{ $r->is_featured ? 'Remove from testimonials' : 'Feature as testimonial' }}"><i class="bi bi-star{{ $r->is_featured ? '-fill' : '' }}"></i></button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.reviews.destroy', $r) }}" onsubmit="return confirm('Delete this review?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">No reviews found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $reviews->links() }}</div>
@endsection
