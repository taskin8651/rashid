@extends('layouts.admin')

@section('title', 'Course Management')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0"><h4>Course Management</h4><p>Manage all courses on the platform</p></div>
  </div>

  <details class="card-rt mb-4">
    <summary class="bsave" style="display:inline-block;cursor:pointer;list-style:none"><i class="bi bi-plus-circle-fill me-1"></i>Add New Course</summary>
    <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data" class="mt-4">
      @csrf
      <div class="row g-3">
        <div class="col-md-6"><label class="flbl">Course Name</label><input class="fctrl" type="text" name="name" placeholder="e.g. Web Development" required/></div>
        <div class="col-md-3"><label class="flbl">Price (₹)</label><input class="fctrl" type="number" name="price" value="15000" required/></div>
        <div class="col-md-3"><label class="flbl">Duration</label><input class="fctrl" type="text" name="duration_text" placeholder="e.g. 6 Months"/></div>
        <div class="col-12"><label class="flbl">Description</label><textarea class="fctrl" name="description" rows="2" placeholder="Course description…"></textarea></div>
        <div class="col-12"><label class="flbl">Modules (one per line)</label><textarea class="fctrl" name="modules_text" rows="4" placeholder="HTML &amp; CSS Fundamentals&#10;JavaScript Essentials"></textarea></div>
        <div class="col-md-4"><label class="flbl">Category</label>
          <select class="fctrl" name="category_id" required>
            @foreach ($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4"><label class="flbl">Status</label><select class="fctrl" name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
        <div class="col-md-4"><label class="flbl">Course Thumbnail</label><input class="fctrl" type="file" name="thumbnail"/></div>
      </div>
      <button class="bsave mt-4" type="submit"><i class="bi bi-check2-circle me-1"></i>Save Course</button>
    </form>
  </details>

  <div class="row g-3">
    @foreach ($courses as $course)
      <div class="col-md-6 col-lg-4">
        <div class="card-rt">
          <h6 style="font-size:14px;font-weight:700;margin-bottom:4px">{{ $course->name }}</h6>
          <p style="font-size:12px;color:var(--muted);margin-bottom:6px">{{ $course->category->name ?? '—' }} · {{ $course->videos_count }} videos</p>
          <p style="font-size:11px;margin-bottom:10px">
            @if ($course->franchise_booking_id)
              <span style="color:var(--orange)">🏙 {{ $course->franchiseBooking->city }} Franchise</span>
            @else
              <span style="color:var(--ok)">R-Tech Official</span>
            @endif
          </p>
          <div style="font-size:18px;font-weight:700;color:var(--orange);margin-bottom:12px">₹{{ number_format($course->price, 0) }}</div>
          <div class="d-flex gap-2 align-items-center mb-2">
            <span class="badge-rt {{ $course->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ $course->status }}</span>
            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course? This cannot be undone.')" class="ms-auto">
              @csrf @method('DELETE')
              <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
            </form>
          </div>
          <details>
            <summary style="cursor:pointer;font-size:12px;color:var(--orange)">Edit course</summary>
            <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data" class="mt-3">
              @csrf
              <div class="row g-2">
                <div class="col-12"><input class="fctrl" type="text" name="name" value="{{ $course->name }}"/></div>
                <div class="col-6"><input class="fctrl" type="number" name="price" value="{{ $course->price }}"/></div>
                <div class="col-6"><input class="fctrl" type="text" name="duration_text" value="{{ $course->duration_text }}"/></div>
                <div class="col-12"><textarea class="fctrl" name="description" rows="2">{{ $course->description }}</textarea></div>
                <div class="col-12"><textarea class="fctrl" name="modules_text" rows="3">{{ $course->modules->pluck('title')->implode("\n") }}</textarea></div>
                <div class="col-6">
                  <select class="fctrl" name="category_id">
                    @foreach ($categories as $cat)
                      <option value="{{ $cat->id }}" @selected($cat->id === $course->category_id)>{{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
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
    @endforeach
  </div>
@endsection
