@extends('layouts.admin')

@section('title', 'Certificate Applications')

@section('content')
  <div class="shead mb-4"><h4>Certificate Applications</h4><p>Offline students applying for a completion certificate</p></div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.certificate-applications.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All</a>
    <a href="{{ route('admin.certificate-applications.index', ['status' => 'pending']) }}" class="badge-rt {{ $status === 'pending' ? 'bg-pending' : 'bg-inactive' }}" style="text-decoration:none">Pending ({{ $pendingCount }})</a>
    <a href="{{ route('admin.certificate-applications.index', ['status' => 'approved']) }}" class="badge-rt {{ $status === 'approved' ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">Approved</a>
    <a href="{{ route('admin.certificate-applications.index', ['status' => 'rejected']) }}" class="badge-rt {{ $status === 'rejected' ? 'bg-failed' : 'bg-inactive' }}" style="text-decoration:none">Rejected</a>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Applicant</th><th>Course</th><th>Trained At</th><th>Completed On</th><th>Proof</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($applications as $app)
          <tr>
            <td>{{ $app->user->name }}<div style="font-size:11px;color:var(--muted)">{{ $app->user->email }}</div></td>
            <td>{{ $app->course->name }}</td>
            <td>{{ $app->franchiseBooking->city ?? 'R-Tech Direct' }}</td>
            <td>{{ $app->completion_date->format('d M Y') }}</td>
            <td>
              @if ($app->proof_path)
                <a href="{{ route('admin.certificate-applications.proof', $app) }}" class="action-btn" title="View Proof"><i class="bi bi-file-earmark-check-fill"></i></a>
              @else
                —
              @endif
            </td>
            <td><span class="badge-rt {{ $app->status === 'approved' ? 'bg-active' : ($app->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($app->status) }}</span></td>
            <td>
              @if ($app->status === 'pending')
                <button class="action-btn" style="border:none;background:none" title="Approve &amp; Issue Certificate" data-bs-toggle="modal" data-bs-target="#approveApp{{ $app->id }}"><i class="bi bi-check-lg"></i></button>
                <button class="action-btn danger" style="border:none;background:none" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectApp{{ $app->id }}"><i class="bi bi-x-lg"></i></button>
              @elseif ($app->status === 'approved' && $app->certificate)
                <div class="d-flex align-items-center gap-2">
                  <span style="font-size:11px;color:var(--muted)">Cert: {{ $app->certificate->cert_code }}</span>
                  <button class="action-btn" style="border:none;background:none" title="Edit Marksheet Details" data-bs-toggle="modal" data-bs-target="#docs{{ $app->certificate->id }}"><i class="bi bi-pencil-square"></i></button>
                </div>
              @elseif ($app->admin_notes)
                <span style="font-size:11px;color:var(--muted)">{{ $app->admin_notes }}</span>
              @endif
            </td>
          </tr>

          @if ($app->status === 'pending')
            <div class="modal fade" id="approveApp{{ $app->id }}" tabindex="-1">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Approve &amp; Issue Certificate</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST" action="{{ route('admin.certificate-applications.approve', $app) }}">
                    @csrf
                    <div class="modal-body">
                      <p style="font-size:13px;color:var(--muted)">Issuing a certificate to <b style="color:var(--text)">{{ $app->user->name }}</b> for <b style="color:var(--text)">{{ $app->course->name }}</b>. Certificate number, certificate PDF &amp; marksheet PDF are all generated automatically.</p>

                      <div class="row g-2 mb-1">
                        <div class="col-md-4"><label class="flbl">Roll No.</label><input class="fctrl" type="text" name="roll_no" placeholder="e.g. RTC24001" /></div>
                        <div class="col-md-4"><label class="flbl">Father's Name</label><input class="fctrl" type="text" name="father_name" value="{{ $app->user->guardian_name }}" /></div>
                        <div class="col-md-4"><label class="flbl">Batch</label><input class="fctrl" type="text" name="batch_name" placeholder="e.g. Morning Batch" /></div>
                      </div>

                      <label class="flbl mt-2">Subjects &amp; Marks (for Marksheet)</label>
                      @php $moduleTitles = $app->course->modules->pluck('title')->all(); if (empty($moduleTitles)) { $moduleTitles = ['']; } @endphp
                      <div id="subj-approve-{{ $app->id }}" data-next-index="{{ count($moduleTitles) }}">
                        @foreach ($moduleTitles as $i => $title)
                          <div class="d-flex gap-2 mb-2 subj-row">
                            <input class="fctrl" style="flex:2" type="text" name="subjects[{{ $i }}][subject]" value="{{ $title }}" placeholder="Subject" />
                            <input class="fctrl" style="flex:1" type="number" min="1" name="subjects[{{ $i }}][max_marks]" value="100" placeholder="Max Marks" />
                            <input class="fctrl" style="flex:1" type="number" min="0" name="subjects[{{ $i }}][marks_obtained]" placeholder="Obtained" />
                            <button type="button" class="action-btn danger" style="border:none;background:none" onclick="this.closest('.subj-row').remove()"><i class="bi bi-trash"></i></button>
                          </div>
                        @endforeach
                      </div>
                      <button type="button" class="bghost" style="font-size:12px;padding:6px 12px" onclick="addSubjectRow('subj-approve-{{ $app->id }}')"><i class="bi bi-plus-lg me-1"></i>Add Subject</button>
                      <p style="font-size:11px;color:var(--muted);margin:8px 0 0"><i class="bi bi-info-circle me-1"></i>Leave marks blank to fill in later from the same page. Subjects with no name are ignored.</p>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="bsave">Approve &amp; Issue</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <div class="modal fade" id="rejectApp{{ $app->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Reject Application</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST" action="{{ route('admin.certificate-applications.reject', $app) }}">
                    @csrf
                    <div class="modal-body">
                      <p style="font-size:13px;color:var(--muted)">Rejecting {{ $app->user->name }}'s application for {{ $app->course->name }}.</p>
                      <label class="flbl">Reason (shown to applicant)</label>
                      <textarea class="fctrl" name="admin_notes" rows="3" placeholder="e.g. Could not verify training details"></textarea>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="bsave" style="background:var(--danger)">Reject Application</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @elseif ($app->status === 'approved' && $app->certificate)
            <div class="modal fade" id="docs{{ $app->certificate->id }}" tabindex="-1">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Marksheet Details — {{ $app->user->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST" action="{{ route('admin.certificates.documents.update', $app->certificate) }}">
                    @csrf
                    <div class="modal-body">
                      <div class="row g-2 mb-1">
                        <div class="col-md-4"><label class="flbl">Roll No.</label><input class="fctrl" type="text" name="roll_no" value="{{ $app->certificate->roll_no }}" /></div>
                        <div class="col-md-4"><label class="flbl">Father's Name</label><input class="fctrl" type="text" name="father_name" value="{{ $app->certificate->father_name }}" /></div>
                        <div class="col-md-4"><label class="flbl">Batch</label><input class="fctrl" type="text" name="batch_name" value="{{ $app->certificate->batch_name }}" /></div>
                      </div>

                      <label class="flbl mt-2">Subjects &amp; Marks (for Marksheet)</label>
                      @php
                        $existingSubjects = $app->certificate->subjects->isNotEmpty() ? $app->certificate->subjects : $app->course->modules->map(fn ($m) => (object) ['subject' => $m->title, 'max_marks' => 100, 'marks_obtained' => null]);
                        if ($existingSubjects->isEmpty()) { $existingSubjects = collect([(object) ['subject' => '', 'max_marks' => 100, 'marks_obtained' => null]]); }
                      @endphp
                      <div id="subj-docs-{{ $app->certificate->id }}" data-next-index="{{ $existingSubjects->count() }}">
                        @foreach ($existingSubjects as $i => $s)
                          <div class="d-flex gap-2 mb-2 subj-row">
                            <input class="fctrl" style="flex:2" type="text" name="subjects[{{ $i }}][subject]" value="{{ $s->subject }}" placeholder="Subject" />
                            <input class="fctrl" style="flex:1" type="number" min="1" name="subjects[{{ $i }}][max_marks]" value="{{ $s->max_marks }}" placeholder="Max Marks" />
                            <input class="fctrl" style="flex:1" type="number" min="0" name="subjects[{{ $i }}][marks_obtained]" value="{{ $s->marks_obtained }}" placeholder="Obtained" />
                            <button type="button" class="action-btn danger" style="border:none;background:none" onclick="this.closest('.subj-row').remove()"><i class="bi bi-trash"></i></button>
                          </div>
                        @endforeach
                      </div>
                      <button type="button" class="bghost" style="font-size:12px;padding:6px 12px" onclick="addSubjectRow('subj-docs-{{ $app->certificate->id }}')"><i class="bi bi-plus-lg me-1"></i>Add Subject</button>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="bsave">Save Marksheet</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @endif
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No applications found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $applications->links() }}</div>

  <script>
    function addSubjectRow(containerId, subject, maxMarks, obtained) {
      const container = document.getElementById(containerId);
      const index = parseInt(container.dataset.nextIndex || '0', 10);
      container.dataset.nextIndex = index + 1;
      const row = document.createElement('div');
      row.className = 'd-flex gap-2 mb-2 subj-row';
      row.innerHTML = `
        <input class="fctrl" style="flex:2" type="text" name="subjects[${index}][subject]" value="${subject ?? ''}" placeholder="Subject" />
        <input class="fctrl" style="flex:1" type="number" min="1" name="subjects[${index}][max_marks]" value="${maxMarks ?? 100}" placeholder="Max Marks" />
        <input class="fctrl" style="flex:1" type="number" min="0" name="subjects[${index}][marks_obtained]" value="${obtained ?? ''}" placeholder="Obtained" />
        <button type="button" class="action-btn danger" style="border:none;background:none" onclick="this.closest('.subj-row').remove()"><i class="bi bi-trash"></i></button>
      `;
      container.appendChild(row);
    }
  </script>
@endsection
