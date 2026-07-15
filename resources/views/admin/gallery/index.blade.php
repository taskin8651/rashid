@extends('layouts.admin')

@section('title', 'Gallery')

@section('content')
  <div class="shead mb-4"><h4>Gallery</h4><p>Photos shown on the public gallery page. Franchise uploads need your approval.</p></div>

  <div class="card-rt mb-4">
    <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-6"><label class="flbl">Caption (optional)</label><input class="fctrl" name="caption" placeholder="e.g. Annual Tech Fest 2026"/></div>
        <div class="col-md-4"><label class="flbl">Image</label><input class="fctrl" type="file" name="image" accept="image/*" required/></div>
        <div class="col-md-2 d-flex align-items-end"><button class="bsave w-100" type="submit"><i class="bi bi-upload me-1"></i>Upload</button></div>
      </div>
    </form>
  </div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.gallery.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All</a>
    <a href="{{ route('admin.gallery.index', ['status' => 'pending']) }}" class="badge-rt {{ $status === 'pending' ? 'bg-pending' : 'bg-inactive' }}" style="text-decoration:none">Pending ({{ $pendingCount }})</a>
    <a href="{{ route('admin.gallery.index', ['status' => 'approved']) }}" class="badge-rt {{ $status === 'approved' ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">Approved</a>
    <a href="{{ route('admin.gallery.index', ['status' => 'rejected']) }}" class="badge-rt {{ $status === 'rejected' ? 'bg-failed' : 'bg-inactive' }}" style="text-decoration:none">Rejected</a>
  </div>

  <div class="row g-3">
    @forelse ($images as $img)
      <div class="col-md-4 col-lg-3">
        <div class="card-rt p-0" style="overflow:hidden">
          <img src="{{ $img->imageUrl() }}" style="width:100%;height:150px;object-fit:cover;display:block">
          <div class="p-3">
            <p style="font-size:12px;margin-bottom:4px">{{ $img->caption ?: '—' }}</p>
            <p style="font-size:11px;color:var(--muted);margin-bottom:8px">
              {{ $img->franchiseBooking->city ?? 'R-Tech HQ' }} &middot; {{ $img->uploader->name ?? 'Admin' }}
            </p>
            <span class="badge-rt {{ $img->status === 'approved' ? 'bg-active' : ($img->status === 'pending' ? 'bg-pending' : 'bg-failed') }} mb-2 d-inline-block">{{ ucfirst($img->status) }}</span>
            <div class="d-flex gap-2 flex-wrap">
              @if ($img->status !== 'approved')
                <form method="POST" action="{{ route('admin.gallery.approve', $img) }}">
                  @csrf
                  <button type="submit" class="action-btn" title="Approve"><i class="bi bi-check-lg"></i></button>
                </form>
              @endif
              @if ($img->status !== 'rejected')
                <form method="POST" action="{{ route('admin.gallery.reject', $img) }}">
                  @csrf
                  <button type="submit" class="action-btn" title="Reject"><i class="bi bi-x-lg"></i></button>
                </form>
              @endif
              <form method="POST" action="{{ route('admin.gallery.destroy', $img) }}" onsubmit="return confirm('Delete this image?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="color:var(--muted)">No images found.</p></div>
    @endforelse
  </div>

  <div class="mt-3">{{ $images->links() }}</div>
@endsection
