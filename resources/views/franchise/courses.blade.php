@extends('layouts.franchise')

@section('title', 'My Courses')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-mortarboard-fill"></i>My Institute</div>
    <h4>My Courses</h4>
    <p>Add and manage the courses you run at your center — they'll show up on the R-Tech Computer website tagged with your city.</p>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['total'] }}</b><span>{{ \Illuminate\Support\Str::plural('Course', $stats['total']) }}</span></div>
      <div><b>{{ $stats['active'] }}</b><span>Active</span></div>
      <div><b>{{ $stats['students'] }}</b><span>{{ \Illuminate\Support\Str::plural('Student', $stats['students']) }}</span></div>
      <div><b>{{ $stats['videos'] }}</b><span>{{ \Illuminate\Support\Str::plural('Video', $stats['videos']) }}</span></div>
    </div>
  </div>

  @php $paidBookings = $bookings->where('status', 'paid'); @endphp

  @if ($paidBookings->isEmpty())
    <div class="card-rt text-center" style="padding:32px 24px">
      <i class="bi bi-lock-fill" style="font-size:26px;color:var(--muted);margin-bottom:10px;display:inline-block"></i>
      <p style="font-size:13px;color:var(--muted);margin:0">Your franchise registration must be paid before you can add courses.</p>
    </div>
  @else
    <details class="card-rt mb-4">
      <summary class="bsave" style="display:inline-block;cursor:pointer;list-style:none"><i class="bi bi-plus-circle-fill me-1"></i>Add New Course</summary>
      <form method="POST" action="{{ route('franchise.courses.store') }}" class="mt-4">
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
    </details>
  @endif

  @php
    $catColors = [
      'web-development' => ['59,130,246', '💻'],
      'graphic-design' => ['168,85,247', '🎨'],
      'digital-marketing' => ['16,185,129', '📈'],
      'video-editing' => ['249,115,22', '🎬'],
    ];
  @endphp

  <div class="row g-3">
    @forelse ($bookings->flatMap->courses as $course)
      @php [$catRgb, $catIcon] = $catColors[$course->category->slug ?? ''] ?? ['37,99,235', '🎓']; @endphp
      <div class="col-md-6 col-lg-4">
        <div class="mcc" style="display:flex;flex-direction:column;height:100%">
          <div class="cthumb" style="background:linear-gradient(135deg, rgba({{ $catRgb }},.9), rgba({{ $catRgb }},.55)), linear-gradient(135deg,#0d1e3c,#142a52);position:relative">
            <span class="badge-rt {{ $course->status === 'active' ? 'bg-active' : 'bg-inactive' }}" style="position:absolute;top:10px;right:10px">{{ $course->status }}</span>
            {{ $catIcon }}
          </div>
          <div class="cinfo" style="flex:1;display:flex;flex-direction:column">
            <div class="badge-rt bg-inactive mb-2" style="align-self:flex-start">{{ $course->category->name ?? '—' }}</div>
            <h6 style="font-size:14px;font-weight:700;margin-bottom:4px">{{ $course->name }}</h6>
            <p style="font-size:12px;color:var(--muted);margin-bottom:10px">{{ $course->duration_text ?: 'Duration not set' }} &middot; {{ $course->modules->count() }} {{ \Illuminate\Support\Str::plural('module', $course->modules->count()) }}</p>
            <div style="font-size:18px;font-weight:700;color:var(--orange);margin-bottom:12px">₹{{ number_format($course->price, 0) }}</div>

            <div class="d-flex gap-3 mb-3" style="font-size:11px;color:var(--muted)">
              <span><i class="bi bi-people-fill me-1" style="color:var(--ok)"></i>{{ $course->enrollments_count }} students</span>
              <span><i class="bi bi-camera-reels-fill me-1" style="color:var(--orange)"></i>{{ $course->videos_count }} videos</span>
            </div>

            <div class="row g-2 mb-2">
              <div class="col-6"><a href="{{ route('franchise.courses.videos.index', $course) }}" class="bghost d-block text-center" style="text-decoration:none;font-size:11px;padding:8px"><i class="bi bi-camera-reels-fill me-1"></i>Videos</a></div>
              <div class="col-6"><a href="{{ route('franchise.courses.quiz.index', $course) }}" class="bghost d-block text-center" style="text-decoration:none;font-size:11px;padding:8px"><i class="bi bi-patch-question-fill me-1"></i>Quiz</a></div>
              <div class="col-6"><a href="{{ route('franchise.courses.assignments.index', $course) }}" class="bghost d-block text-center" style="text-decoration:none;font-size:11px;padding:8px"><i class="bi bi-journal-text me-1"></i>Assignments</a></div>
              <div class="col-6"><a href="{{ route('franchise.courses.notes.index', $course) }}" class="bghost d-block text-center" style="text-decoration:none;font-size:11px;padding:8px"><i class="bi bi-file-earmark-pdf-fill me-1"></i>Notes</a></div>
            </div>

            <div class="d-flex gap-2 align-items-center mt-auto">
              <button type="button" class="action-btn flex-grow-1" style="width:auto;padding:0 14px;font-weight:600" data-bs-toggle="modal" data-bs-target="#editCourse{{ $course->id }}"><i class="bi bi-pencil-fill me-1"></i>Edit</button>
              <form method="POST" action="{{ route('franchise.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="editCourse{{ $course->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Course</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('franchise.courses.update', $course) }}">
              @csrf
              <div class="modal-body">
                <div class="row g-3">
                  <div class="col-md-8"><label class="flbl">Course Name</label><input class="fctrl" type="text" name="name" value="{{ $course->name }}"/></div>
                  <div class="col-md-4"><label class="flbl">Status</label>
                    <select class="fctrl" name="status">
                      <option value="active" @selected($course->status === 'active')>Active</option>
                      <option value="inactive" @selected($course->status === 'inactive')>Inactive</option>
                    </select>
                  </div>
                  <div class="col-md-4"><label class="flbl">Price (₹)</label><input class="fctrl" type="number" name="price" value="{{ $course->price }}"/></div>
                  <div class="col-md-4"><label class="flbl">Duration</label><input class="fctrl" type="text" name="duration_text" value="{{ $course->duration_text }}"/></div>
                  <div class="col-md-4"><label class="flbl">Category</label>
                    <select class="fctrl" name="category_id">
                      @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($cat->id === $course->category_id)>{{ $cat->name }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-12"><label class="flbl">Description</label><textarea class="fctrl" name="description" rows="3">{{ $course->description }}</textarea></div>
                  <div class="col-12"><label class="flbl">Modules (one per line)</label><textarea class="fctrl" name="modules_text" rows="4">{{ $course->modules->pluck('title')->implode("\n") }}</textarea></div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="bsave"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card-rt text-center" style="padding:40px 24px">
          <i class="bi bi-collection-play" style="font-size:28px;color:var(--orange);margin-bottom:10px;display:inline-block"></i>
          <p style="font-size:13px;color:var(--muted);margin:0">You haven't added any courses yet.</p>
        </div>
      </div>
    @endforelse
  </div>
@endsection
