@extends('layouts.franchise')

@section('title', 'Placements')

@section('content')
  <div class="shead"><h4>Student Placements</h4><p>Report where your students got placed &mdash; approved placements are featured on the public website</p></div>

  @if ($bookings->isEmpty())
    <div class="card-rt" style="padding:24px">
      <p style="font-size:13px;color:var(--muted);margin:0">You need a paid franchise booking before you can submit placements.</p>
    </div>
  @else
    <div class="card-rt mb-4">
      <form method="POST" action="{{ route('franchise.placements.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-2 mb-1">
          <div class="col-md-4">
            <label class="flbl">Centre</label>
            <select class="fctrl" name="franchise_booking_id" required>
              @foreach ($bookings as $b)
                <option value="{{ $b->id }}">{{ $b->city }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="flbl">Student</label>
            <select class="fctrl" name="user_id" required>
              <option value="">Select student</option>
              @foreach ($students as $s)
                <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="flbl">Related Course</label>
            <select class="fctrl" name="course_id">
              <option value="">Select course</option>
              @foreach ($courses as $course)
                <option value="{{ $course->id }}">{{ $course->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4"><label class="flbl">Company Name</label><input class="fctrl" type="text" name="company_name" required placeholder="e.g. Infosys" /></div>
          <div class="col-md-4"><label class="flbl">Job Title</label><input class="fctrl" type="text" name="job_title" required placeholder="e.g. Junior Web Developer" /></div>
          <div class="col-md-4">
            <label class="flbl">Job Type</label>
            <select class="fctrl" name="job_type">
              <option value="">Select type</option>
              @foreach (['Full-time', 'Part-time', 'Internship', 'Freelance'] as $type)
                <option value="{{ $type }}">{{ $type }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="flbl">Work Mode</label>
            <select class="fctrl" name="work_mode">
              <option value="">Select mode</option>
              @foreach (['Onsite', 'Remote', 'Hybrid'] as $mode)
                <option value="{{ $mode }}">{{ $mode }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3"><label class="flbl">Location</label><input class="fctrl" type="text" name="location" placeholder="e.g. Bengaluru" /></div>
          <div class="col-md-3"><label class="flbl">Package / Salary</label><input class="fctrl" type="text" name="package" placeholder="e.g. 3.5 LPA" /></div>
          <div class="col-md-3"><label class="flbl">Joining Date</label><input class="fctrl" type="date" name="joining_date" /></div>
          <div class="col-md-6"><label class="flbl">LinkedIn Profile (optional)</label><input class="fctrl" type="url" name="linkedin_url" placeholder="https://linkedin.com/in/..." /></div>
          <div class="col-md-6"><label class="flbl">Offer Letter / Proof (optional)</label><input class="fctrl" type="file" name="offer_letter" accept=".jpg,.jpeg,.png,.pdf" /></div>
          <div class="col-md-12"><label class="flbl">Success Story (optional)</label><textarea class="fctrl" name="testimonial" rows="2" maxlength="1000" placeholder="A short quote from the student about their journey"></textarea></div>
        </div>
        <button class="bsave mt-2" type="submit"><i class="bi bi-send-fill me-1"></i>Submit for Approval</button>
      </form>
    </div>

    <div class="card-rt">
      <div class="table-wrap"><table class="table-rt">
        <thead><tr><th>Student</th><th>Company</th><th>Role</th><th>Package</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          @forelse ($placements as $p)
            <tr>
              <td>{{ optional($p->user)->name ?: '—' }}<div style="font-size:11px;color:var(--muted)">{{ optional($p->course)->name ?: '—' }}</div></td>
              <td>{{ $p->company_name }}<div style="font-size:11px;color:var(--muted)">{{ $p->location ?: '—' }}</div></td>
              <td>{{ $p->job_title }}<div style="font-size:11px;color:var(--muted)">{{ $p->job_type ?: '—' }}{{ $p->work_mode ? ' · ' . $p->work_mode : '' }}</div></td>
              <td>{{ $p->package ?: '—' }}</td>
              <td>
                <span class="badge-rt {{ $p->status === 'approved' ? 'bg-active' : ($p->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($p->status) }}</span>
                @if ($p->status === 'rejected' && $p->admin_notes)
                  <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $p->admin_notes }}</div>
                @endif
              </td>
              <td>
                @if ($p->status === 'pending')
                  <form method="POST" action="{{ route('franchise.placements.destroy', $p) }}" onsubmit="return confirm('Remove this submission?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="action-btn danger" style="border:none;background:none" title="Remove"><i class="bi bi-trash-fill"></i></button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" style="color:var(--muted)">No placements submitted yet.</td></tr>
          @endforelse
        </tbody>
      </table></div>
    </div>
  @endif
@endsection
