@extends('layouts.admin')

@section('title', 'Certificates')

@section('content')
  <div class="shead mb-4"><h4>Certificates</h4><p>All issued certificates &mdash; from applications, course completion, or created manually</p></div>

  <div class="d-flex gap-2 mb-3 flex-wrap justify-content-between align-items-center">
    <form method="GET" class="d-flex gap-2">
      <input class="fctrl" type="text" name="search" value="{{ $search }}" placeholder="Search name, email, or cert no." style="min-width:260px" />
      <button type="submit" class="bghost">Search</button>
    </form>
    <button type="button" class="bsave" data-bs-toggle="modal" data-bs-target="#createManualCertificate"><i class="bi bi-plus-lg me-1"></i>Add Certificate</button>
  </div>

  <div class="modal fade" id="createManualCertificate" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Certificate Manually</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.certificate-applications.manual-store') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-2 mb-1">
              <div class="col-md-6"><label class="flbl">Student Name</label><input class="fctrl" type="text" name="student_name" required placeholder="e.g. John Doe" /></div>
              <div class="col-md-6"><label class="flbl">Student Email</label><input class="fctrl" type="email" name="student_email" placeholder="e.g. student@example.com" /></div>
              <div class="col-md-6"><label class="flbl">Student Phone</label><input class="fctrl" type="text" name="student_phone" placeholder="e.g. 9876543210" /></div>
              <div class="col-md-6"><label class="flbl">Course</label>
                <select class="fctrl" name="course_id" required>
                  <option value="">Select course</option>
                  @foreach ($courses ?? [] as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6"><label class="flbl">Course Name</label><input class="fctrl" type="text" name="course_name" placeholder="Optional custom course name" /></div>
              <div class="col-md-6"><label class="flbl">Course Duration</label><input class="fctrl" type="text" name="course_duration_text" placeholder="e.g. 3 Months" /></div>
              <div class="col-md-6"><label class="flbl">Roll No.</label><input class="fctrl" type="text" name="roll_no" placeholder="e.g. RTC24001" /></div>
              <div class="col-md-6"><label class="flbl">Father's Name</label><input class="fctrl" type="text" name="father_name" placeholder="e.g. Ahmed Khan" /></div>
              <div class="col-md-6"><label class="flbl">Batch</label><input class="fctrl" type="text" name="batch_name" placeholder="e.g. Morning Batch" /></div>
            </div>

            <label class="flbl mt-2">Documents to Generate</label>
            <div class="d-flex gap-3 mb-2">
              <div class="form-check">
                <input type="hidden" name="include_certificate" value="0">
                <input class="form-check-input" type="checkbox" name="include_certificate" value="1" id="manualIncludeCert" checked>
                <label class="form-check-label" for="manualIncludeCert">Certificate</label>
              </div>
              <div class="form-check">
                <input type="hidden" name="include_marksheet" value="0">
                <input class="form-check-input" type="checkbox" name="include_marksheet" value="1" id="manualIncludeMarksheet" checked>
                <label class="form-check-label" for="manualIncludeMarksheet">Marksheet</label>
              </div>
            </div>

            <label class="flbl mt-2">Subjects &amp; Marks (for Marksheet)</label>
            <div id="subj-manual" data-next-index="0">
              <div class="d-flex gap-2 mb-2 subj-row">
                <input class="fctrl" style="flex:2" type="text" name="subjects[0][subject]" placeholder="Subject" />
                <input class="fctrl" style="flex:1" type="number" min="1" name="subjects[0][max_marks]" value="100" placeholder="Max Marks" />
                <input class="fctrl" style="flex:1" type="number" min="0" name="subjects[0][marks_obtained]" placeholder="Obtained" />
                <button type="button" class="action-btn danger" style="border:none;background:none" onclick="this.closest('.subj-row').remove()"><i class="bi bi-trash"></i></button>
              </div>
            </div>
            <button type="button" class="bghost" style="font-size:12px;padding:6px 12px" onclick="addSubjectRow('subj-manual')"><i class="bi bi-plus-lg me-1"></i>Add Subject</button>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Create Certificate</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Course</th><th>Cert No.</th><th>Issued</th><th>Source</th><th>Documents</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($certificates as $cert)
          <tr>
            <td>{{ optional($cert->user)->name ?: ($cert->student_name ?: 'Unknown') }}<div style="font-size:11px;color:var(--muted)">{{ optional($cert->user)->email ?: ($cert->student_email ?: '—') }}</div></td>
            <td>{{ optional($cert->course)->name ?: ($cert->course_name ?: '—') }}</td>
            <td>{{ $cert->cert_code }}</td>
            <td>
              <span class="badge-rt {{ $cert->status === 'issued' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($cert->status) }}</span>
              @if ($cert->status === 'issued' && $cert->issued_date)
                <div style="font-size:11px;color:var(--muted)">{{ $cert->issued_date->format('d M Y') }}</div>
              @endif
            </td>
            <td><span style="font-size:11px;color:var(--muted)">{{ ucfirst($cert->source) }}</span></td>
            <td>
              @if ($cert->status === 'issued')
                <div class="d-flex align-items-center gap-1 flex-wrap">
                  @if ($cert->include_certificate)
                    <a href="{{ route('admin.certificates.download', $cert) }}" class="btn btn-sm btn-outline-primary" title="Download Certificate"><i class="bi bi-award-fill"></i></a>
                  @endif
                  @if ($cert->include_marksheet)
                    @if ($cert->hasMarksheetData())
                      <a href="{{ route('admin.certificates.marksheet', $cert) }}" class="btn btn-sm btn-outline-secondary" title="Download Marksheet"><i class="bi bi-file-earmark-text-fill"></i></a>
                    @else
                      <span class="badge-rt bg-pending" title="Marksheet marks not added yet">No marks yet</span>
                    @endif
                  @endif
                  @if (!$cert->include_certificate && !$cert->include_marksheet)
                    <span style="font-size:11px;color:var(--muted)">None selected</span>
                  @endif
                </div>
              @else
                <span style="font-size:11px;color:var(--muted)">—</span>
              @endif
            </td>
            <td>
              @if ($cert->status === 'issued')
                <button class="action-btn" style="border:none;background:none" title="Edit Certificate" data-bs-toggle="modal" data-bs-target="#certEdit{{ $cert->id }}"><i class="bi bi-pencil-square"></i></button>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No certificates found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $certificates->links() }}</div>

  @foreach ($certificates as $cert)
    @if ($cert->status === 'issued')
      <div class="modal fade" id="certEdit{{ $cert->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Certificate &mdash; {{ optional($cert->user)->name ?: ($cert->student_name ?: 'Student') }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('admin.certificates.documents.update', $cert) }}">
              @csrf
              <div class="modal-body">
                <div class="row g-2 mb-1">
                  <div class="col-md-4"><label class="flbl">Roll No.</label><input class="fctrl" type="text" name="roll_no" value="{{ $cert->roll_no }}" /></div>
                  <div class="col-md-4"><label class="flbl">Father's Name</label><input class="fctrl" type="text" name="father_name" value="{{ $cert->father_name }}" /></div>
                  <div class="col-md-4"><label class="flbl">Batch</label><input class="fctrl" type="text" name="batch_name" value="{{ $cert->batch_name }}" /></div>
                </div>

                <label class="flbl mt-2">Documents to Generate</label>
                <div class="d-flex gap-3 mb-2">
                  <div class="form-check">
                    <input type="hidden" name="include_certificate" value="0">
                    <input class="form-check-input" type="checkbox" name="include_certificate" value="1" id="editIncludeCert{{ $cert->id }}" {{ $cert->include_certificate ? 'checked' : '' }}>
                    <label class="form-check-label" for="editIncludeCert{{ $cert->id }}">Certificate</label>
                  </div>
                  <div class="form-check">
                    <input type="hidden" name="include_marksheet" value="0">
                    <input class="form-check-input" type="checkbox" name="include_marksheet" value="1" id="editIncludeMarksheet{{ $cert->id }}" {{ $cert->include_marksheet ? 'checked' : '' }}>
                    <label class="form-check-label" for="editIncludeMarksheet{{ $cert->id }}">Marksheet</label>
                  </div>
                </div>

                <label class="flbl mt-2">Subjects &amp; Marks (for Marksheet)</label>
                @php
                  $existingSubjects = $cert->subjects->isNotEmpty() ? $cert->subjects : optional($cert->course)->modules?->map(fn ($m) => (object) ['subject' => $m->title, 'max_marks' => 100, 'marks_obtained' => null]) ?? collect();
                  if ($existingSubjects->isEmpty()) { $existingSubjects = collect([(object) ['subject' => '', 'max_marks' => 100, 'marks_obtained' => null]]); }
                @endphp
                <div id="subj-certedit-{{ $cert->id }}" data-next-index="{{ $existingSubjects->count() }}">
                  @foreach ($existingSubjects as $i => $s)
                    <div class="d-flex gap-2 mb-2 subj-row">
                      <input class="fctrl" style="flex:2" type="text" name="subjects[{{ $i }}][subject]" value="{{ $s->subject }}" placeholder="Subject" />
                      <input class="fctrl" style="flex:1" type="number" min="1" name="subjects[{{ $i }}][max_marks]" value="{{ $s->max_marks }}" placeholder="Max Marks" />
                      <input class="fctrl" style="flex:1" type="number" min="0" name="subjects[{{ $i }}][marks_obtained]" value="{{ $s->marks_obtained }}" placeholder="Obtained" />
                      <button type="button" class="action-btn danger" style="border:none;background:none" onclick="this.closest('.subj-row').remove()"><i class="bi bi-trash"></i></button>
                    </div>
                  @endforeach
                </div>
                <button type="button" class="bghost" style="font-size:12px;padding:6px 12px" onclick="addSubjectRow('subj-certedit-{{ $cert->id }}')"><i class="bi bi-plus-lg me-1"></i>Add Subject</button>
              </div>
              <div class="modal-footer">
                <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="bsave">Save</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @endif
  @endforeach

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
