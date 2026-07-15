@extends('layouts.franchise')

@section('title', 'Gallery')

@section('content')
  <div class="shead"><h4>My Gallery</h4><p>Upload photos of your centre — they'll appear on the public gallery page once approved</p></div>

  @if ($bookings->isEmpty())
    <div class="card-rt" style="padding:24px">
      <p style="font-size:13px;color:var(--muted);margin:0">You need a paid franchise booking before you can upload gallery photos.</p>
    </div>
  @else
    <div class="card-rt mb-4">
      <form method="POST" action="{{ route('franchise.gallery.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="flbl">Centre</label>
            <select class="fctrl" name="franchise_booking_id" required>
              @foreach ($bookings as $b)
                <option value="{{ $b->id }}">{{ $b->city }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4"><label class="flbl">Caption (optional)</label><input class="fctrl" name="caption" placeholder="e.g. Our new lab setup"/></div>
          <div class="col-md-3"><label class="flbl">Image</label><input class="fctrl" type="file" name="image" accept="image/*" required/></div>
          <div class="col-md-1 d-flex align-items-end"><button class="bsave w-100" type="submit"><i class="bi bi-upload"></i></button></div>
        </div>
      </form>
    </div>

    <div class="row g-3">
      @forelse ($images as $img)
        <div class="col-md-4 col-lg-3">
          <div class="card-rt p-0" style="overflow:hidden">
            <img src="{{ $img->imageUrl() }}" style="width:100%;height:150px;object-fit:cover;display:block">
            <div class="p-3">
              <p style="font-size:12px;margin-bottom:6px">{{ $img->caption ?: '—' }}</p>
              <span class="badge-rt {{ $img->status === 'approved' ? 'bg-active' : ($img->status === 'pending' ? 'bg-pending' : 'bg-failed') }} mb-2 d-inline-block">{{ ucfirst($img->status) }}</span>
              <form method="POST" action="{{ route('franchise.gallery.destroy', $img) }}" onsubmit="return confirm('Delete this image?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12"><p style="font-size:13px;color:var(--muted)">No photos uploaded yet.</p></div>
      @endforelse
    </div>
  @endif
@endsection
