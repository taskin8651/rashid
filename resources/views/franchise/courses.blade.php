@extends('layouts.franchise')

@section('title', 'My Courses')

@section('content')
  <div class="shead"><h4>My Courses</h4><p>Add and manage the courses you run at your center — they'll show up on the R-Tech Computer website tagged with your city.</p></div>

  @php $paidBookings = $bookings->where('status', 'paid'); @endphp

  @if ($paidBookings->isEmpty())
    <p style="font-size:13px;color:var(--muted)">Your franchise registration must be paid before you can add courses.</p>
  @else
    <details class="mb-4">
      <summary class="bsave" style="display:inline-block;cursor:pointer;list-style:none"><i class="bi bi-plus-circle-fill me-1"></i>Add New Course</summary>
      <div style="background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;margin-top:12px">
        <form method="POST" action="{{ route('franchise.courses.store') }}">
          @csrf
          <div class="row g-3">
            @if ($paidBookings->count() > 1)
              <div class="col-md-4">
                <label class="flbl">City / Center</label>
                <select class="fctrl" name="franchise_booking_id" required>
                  @foreach ($paidBookings as $b)
                    <option value="{{ $b->id }}">{{ $b->city }}</option>
                  @endforeach
                </select>
              </div>
            @else
              <input type="hidden" name="franchise_booking_id" value="{{ $paidBookings->first()->id }}">
            @endif
            <div class="col-md-4"><label class="flbl">Category</label>
              <select class="fctrl" name="category_id" required>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4"><label class="flbl">Price (₹)</label><input class="fctrl" type="number" name="price" value="15000" required/></div>
            <div class="col-md-6"><label class="flbl">Course Name</label><input class="fctrl" type="text" name="name" placeholder="e.g. Web Development" required/></div>
            <div class="col-md-6"><label class="flbl">Duration</label><input class="fctrl" type="text" name="duration_text" placeholder="e.g. 6 Months"/></div>
            <div class="col-12"><label class="flbl">Description</label><textarea class="fctrl" name="description" rows="2" placeholder="Course description…"></textarea></div>
            <div class="col-12"><label class="flbl">Modules (one per line)</label><textarea class="fctrl" name="modules_text" rows="3" placeholder="HTML &amp; CSS Fundamentals&#10;JavaScript Essentials"></textarea></div>
          </div>
          <button class="bsave mt-3" type="submit"><i class="bi bi-check2-circle me-1"></i>Add Course</button>
        </form>
      </div>
    </details>
  @endif

  <div class="row g-3">
    @forelse ($bookings->flatMap->courses as $course)
      <div class="col-md-6 col-lg-4">
        <div class="card-rt">
          <h6 style="font-size:14px;font-weight:700;margin-bottom:4px">{{ $course->name }}</h6>
          <p style="font-size:12px;color:var(--muted);margin-bottom:10px">{{ $course->category->name ?? '—' }} · {{ $course->duration_text }}</p>
          <div style="font-size:18px;font-weight:700;color:var(--orange);margin-bottom:12px">₹{{ number_format($course->price, 0) }}</div>
          <div class="d-flex gap-2 align-items-center mb-2">
            <span class="badge-rt {{ $course->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ $course->status }}</span>
            <form method="POST" action="{{ route('franchise.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')" class="ms-auto">
              @csrf @method('DELETE')
              <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
            </form>
          </div>
          <a href="{{ route('franchise.courses.videos.index', $course) }}" class="bsave d-block text-center mb-2" style="text-decoration:none;font-size:12px;padding:8px"><i class="bi bi-camera-reels-fill me-1"></i>Manage Videos ({{ $course->videos_count }})</a>
          <details>
            <summary style="cursor:pointer;font-size:12px;color:var(--orange)">Edit course</summary>
            <form method="POST" action="{{ route('franchise.courses.update', $course) }}" class="mt-3">
              @csrf
              <div class="row g-2">
                <div class="col-12"><input class="fctrl" type="text" name="name" value="{{ $course->name }}"/></div>
                <div class="col-6"><input class="fctrl" type="number" name="price" value="{{ $course->price }}"/></div>
                <div class="col-6"><input class="fctrl" type="text" name="duration_text" value="{{ $course->duration_text }}"/></div>
                <div class="col-12"><textarea class="fctrl" name="description" rows="2">{{ $course->description }}</textarea></div>
                <div class="col-12"><textarea class="fctrl" name="modules_text" rows="3">{{ $course->modules->pluck('title')->implode("\n") }}</textarea></div>
                <div class="col-6">
                  <select class="fctrl" name="status">
                    <option value="active" @selected($course->status === 'active')>Active</option>
                    <option value="inactive" @selected($course->status === 'inactive')>Inactive</option>
                  </select>
                </div>
              </div>
              <button class="bsave mt-2" type="submit" style="font-size:12px;padding:6px 14px">Update</button>
            </form>
          </details>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">You haven't added any courses yet.</p></div>
    @endforelse
  </div>
@endsection
