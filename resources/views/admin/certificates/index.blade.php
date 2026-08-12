@extends('layouts.admin')

@section('title', 'Certificates')

@section('content')
  <div class="cert-hero">
    <div class="cert-hero-top">
      <div>
        <div class="cert-hero-badge"><i class="bi bi-patch-check-fill"></i>Certification Center</div>
        <div class="d-flex align-items-center gap-3">
          <div class="cert-hero-icon"><i class="bi bi-award-fill"></i></div>
          <div>
            <h4>Certificates</h4>
            <p>All issued certificates &mdash; from applications, course completion, or created manually</p>
          </div>
        </div>
      </div>
      <button type="button" class="cert-add-btn" data-bs-toggle="modal" data-bs-target="#createManualCertificate"><i class="bi bi-plus-lg"></i>Add Certificate</button>
    </div>
    <div class="cert-hero-stats">
      <div class="cert-hero-stat"><b>{{ number_format($totalIssued) }}</b><span>Total Issued</span></div>
      <div class="cert-hero-stat"><b>{{ number_format($issuedThisMonth) }}</b><span>Issued This Month</span></div>
      <div class="cert-hero-stat"><b>{{ number_format($withMarksheet) }}</b><span>With Marksheet</span></div>
      <div class="cert-hero-stat"><b>{{ number_format($certificates->total()) }}</b><span>Matching Search</span></div>
    </div>
  </div>

  <div class="cert-toolbar">
    <form method="GET" class="cert-search">
      <i class="bi bi-search"></i>
      <input class="fctrl" type="text" name="search" value="{{ $search }}" placeholder="Search name, email, or cert no." />
    </form>
  </div>

  <div class="modal fade cert-modal" id="createManualCertificate" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title"><i class="bi bi-award-fill"></i>Add Certificate Manually</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.certificate-applications.manual-store') }}">
          @csrf
          <div class="modal-body">
            <div class="cert-section-lbl"><i class="bi bi-person-fill"></i>Student Details</div>
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

            <div class="cert-section-lbl"><i class="bi bi-file-earmark-check-fill"></i>Documents to Generate</div>
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

            <div class="cert-section-lbl"><i class="bi bi-clipboard-data-fill"></i>Subjects &amp; Marks (for Marksheet)</div>
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
            <button type="submit" class="bsave"><i class="bi bi-award-fill me-1"></i>Create Certificate</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="cert-card">
    <div class="cert-card-head"><i class="bi bi-list-check"></i><h6>Certificate Records</h6><span>{{ $certificates->total() }} total</span></div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Course</th><th>Cert No.</th><th>Issued</th><th>Source</th><th>Documents</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($certificates as $cert)
          @php
            $displayName = optional($cert->user)->name ?: ($cert->student_name ?: 'Unknown');
            $initials = collect(explode(' ', trim($displayName)))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('');
          @endphp
          <tr>
            <td>
              <div class="cert-name-cell">
                <div class="cert-avatar">{{ strtoupper($initials ?: 'S') }}</div>
                <div>
                  <div class="nm">{{ $displayName }}</div>
                  <div class="em">{{ optional($cert->user)->email ?: ($cert->student_email ?: '—') }}</div>
                </div>
              </div>
            </td>
            <td>{{ optional($cert->course)->name ?: ($cert->course_name ?: '—') }}</td>
            <td><span style="font-family:'Space Grotesk',sans-serif;font-weight:700;letter-spacing:.3px">{{ $cert->cert_code }}</span></td>
            <td>
              @if ($cert->status === 'issued')
                <span class="badge-cert-issued"><i class="bi bi-patch-check-fill"></i>Issued</span>
                @if ($cert->issued_date)
                  <div style="font-size:11px;color:var(--muted);margin-top:3px">{{ $cert->issued_date->format('d M Y') }}</div>
                @endif
              @else
                <span class="badge-rt bg-pending">{{ ucfirst($cert->status) }}</span>
              @endif
            </td>
            <td><span style="font-size:11px;color:var(--muted)">{{ ucfirst($cert->source) }}</span></td>
            <td>
              @if ($cert->status === 'issued')
                <div class="d-flex align-items-center gap-1 flex-wrap">
                  @if ($cert->include_certificate)
                    <a href="{{ route('admin.certificates.view', $cert) }}" class="cert-icon-btn navy" title="View Certificate" target="_blank"><i class="bi bi-eye-fill"></i></a>
                    <a href="{{ route('admin.certificates.download', $cert) }}" class="cert-icon-btn gold" title="Download Certificate"><i class="bi bi-award-fill"></i></a>
                  @endif
                  @if ($cert->include_marksheet)
                    @if ($cert->hasMarksheetData())
                      <a href="{{ route('admin.certificates.marksheet.view', $cert) }}" class="cert-icon-btn navy" title="View Marksheet" target="_blank"><i class="bi bi-eye-fill"></i></a>
                      <a href="{{ route('admin.certificates.marksheet', $cert) }}" class="cert-icon-btn gold" title="Download Marksheet"><i class="bi bi-file-earmark-text-fill"></i></a>
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
                <button class="cert-icon-btn gold" style="border:1px solid var(--border)" title="Edit Certificate" data-bs-toggle="modal" data-bs-target="#certEdit{{ $cert->id }}"><i class="bi bi-pencil-square"></i></button>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="padding:0">
            <div class="cert-empty">
              <i class="bi bi-award"></i>
              <b>No certificates found</b>
              <span>Try a different search, or add one manually above.</span>
            </div>
          </td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $certificates->links() }}</div>

  @foreach ($certificates as $cert)
    @if ($cert->status === 'issued')
      <div class="modal fade cert-modal" id="certEdit{{ $cert->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title"><i class="bi bi-pencil-square"></i>Edit Certificate &mdash; {{ optional($cert->user)->name ?: ($cert->student_name ?: 'Student') }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('admin.certificates.documents.update', $cert) }}">
              @csrf
              <div class="modal-body">
                <div class="cert-section-lbl"><i class="bi bi-person-fill"></i>Student Details</div>
                <div class="row g-2 mb-1">
                  <div class="col-md-4"><label class="flbl">Certificate Code</label><input class="fctrl" type="text" value="{{ $cert->cert_code }}" disabled></div>
                  <div class="col-md-4"><label class="flbl">Student Name</label><input class="fctrl" type="text" name="student_name" value="{{ optional($cert->user)->name ?: $cert->student_name }}" required /></div>
                  <div class="col-md-4"><label class="flbl">Student Email</label><input class="fctrl" type="email" name="student_email" value="{{ optional($cert->user)->email ?: $cert->student_email }}" /></div>
                  <div class="col-md-4"><label class="flbl">Student Phone</label><input class="fctrl" type="text" name="student_phone" value="{{ optional($cert->user)->phone ?: $cert->student_phone }}" /></div>
                  <div class="col-md-4"><label class="flbl">Course Name</label><input class="fctrl" type="text" name="course_name" value="{{ $cert->course_name }}" /></div>
                  <div class="col-md-4"><label class="flbl">Course Duration</label><input class="fctrl" type="text" name="course_duration_text" value="{{ $cert->course_duration_text }}" placeholder="e.g. 3 Months" /></div>
                  <div class="col-md-4"><label class="flbl">Issued Date</label><input class="fctrl" type="date" name="issued_date" value="{{ optional($cert->issued_date)->format('Y-m-d') }}" /></div>
                  <div class="col-md-4"><label class="flbl">Roll No.</label><input class="fctrl" type="text" name="roll_no" value="{{ $cert->roll_no }}" /></div>
                  <div class="col-md-4"><label class="flbl">Father's Name</label><input class="fctrl" type="text" name="father_name" value="{{ $cert->father_name }}" /></div>
                  <div class="col-md-4"><label class="flbl">Batch</label><input class="fctrl" type="text" name="batch_name" value="{{ $cert->batch_name }}" /></div>
                </div>

                <div class="cert-section-lbl"><i class="bi bi-file-earmark-check-fill"></i>Documents to Generate</div>
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

                <div class="cert-section-lbl"><i class="bi bi-clipboard-data-fill"></i>Subjects &amp; Marks (for Marksheet)</div>
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
                <button type="submit" class="bsave"><i class="bi bi-check-lg me-1"></i>Save</button>
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
