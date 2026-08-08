@extends('layouts.student')

@section('title', 'My Placements')

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>My Placements</h4><p>Got a job? Share your placement details &mdash; once approved, it's featured on our website.</p></div>
    <button type="button" class="bsave" data-bs-toggle="modal" data-bs-target="#addPlacement"><i class="bi bi-briefcase-fill me-1"></i>Add Placement</button>
  </div>

  <div class="modal fade" id="addPlacement" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Share Your Placement</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('student.placements.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="row g-2 mb-1">
              <div class="col-md-6"><label class="flbl">Company Name</label><input class="fctrl" type="text" name="company_name" required placeholder="e.g. Infosys" /></div>
              <div class="col-md-6"><label class="flbl">Job Title / Designation</label><input class="fctrl" type="text" name="job_title" required placeholder="e.g. Junior Web Developer" /></div>
              <div class="col-md-4">
                <label class="flbl">Related Course</label>
                <select class="fctrl" name="course_id">
                  <option value="">Select course</option>
                  @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="flbl">Job Type</label>
                <select class="fctrl" name="job_type">
                  <option value="">Select type</option>
                  @foreach (['Full-time', 'Part-time', 'Internship', 'Freelance'] as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="flbl">Work Mode</label>
                <select class="fctrl" name="work_mode">
                  <option value="">Select mode</option>
                  @foreach (['Onsite', 'Remote', 'Hybrid'] as $mode)
                    <option value="{{ $mode }}">{{ $mode }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4"><label class="flbl">Location</label><input class="fctrl" type="text" name="location" placeholder="e.g. Bengaluru" /></div>
              <div class="col-md-4"><label class="flbl">Package / Salary</label><input class="fctrl" type="text" name="package" placeholder="e.g. 3.5 LPA" /></div>
              <div class="col-md-4"><label class="flbl">Joining Date</label><input class="fctrl" type="date" name="joining_date" /></div>
              <div class="col-md-6"><label class="flbl">LinkedIn Profile (optional)</label><input class="fctrl" type="url" name="linkedin_url" placeholder="https://linkedin.com/in/..." /></div>
              <div class="col-md-6"><label class="flbl">Offer Letter / Proof (optional)</label><input class="fctrl" type="file" name="offer_letter" accept=".jpg,.jpeg,.png,.pdf" /></div>
              <div class="col-md-12"><label class="flbl">Your Success Story (optional)</label><textarea class="fctrl" name="testimonial" rows="3" maxlength="1000" placeholder="How did R-Tech Computer help you land this job?"></textarea></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Submit for Approval</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card-rt mt-3">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Company</th><th>Role</th><th>Course</th><th>Package</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($placements as $p)
          <tr>
            <td>{{ $p->company_name }}<div style="font-size:11px;color:var(--muted)">{{ $p->location ?: '—' }}</div></td>
            <td>{{ $p->job_title }}<div style="font-size:11px;color:var(--muted)">{{ $p->job_type ?: '—' }}{{ $p->work_mode ? ' · ' . $p->work_mode : '' }}</div></td>
            <td>{{ optional($p->course)->name ?: '—' }}</td>
            <td>{{ $p->package ?: '—' }}</td>
            <td>
              @if ($p->status === 'approved')
                <span class="badge-rt bg-active">Approved</span>
              @elseif ($p->status === 'rejected')
                <span class="badge-rt bg-failed">Rejected</span>
                @if ($p->admin_notes)
                  <div style="font-size:11px;color:var(--muted);margin-top:2px">{{ $p->admin_notes }}</div>
                @endif
              @else
                <span class="badge-rt bg-pending">Pending Review</span>
              @endif
            </td>
            <td>
              @if ($p->status === 'pending')
                <form method="POST" action="{{ route('student.placements.destroy', $p) }}" onsubmit="return confirm('Withdraw this submission?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="action-btn danger" style="border:none;background:none" title="Withdraw"><i class="bi bi-trash"></i></button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">You haven't shared any placement yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
