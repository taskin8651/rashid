@extends('layouts.admin')

@section('title', 'Student Profile')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-person-fill"></i>Student Profile</div>
    <div class="d-flex align-items-center gap-3">
      @if ($student->photoUrl())
        <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;flex-shrink:0">
      @else
        <span style="width:64px;height:64px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;flex-shrink:0">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
      @endif
      <div>
        <h4 class="mb-0">{{ $student->name }}</h4>
        <p class="mb-0">{{ $student->email }} @if ($student->phone) &middot; {{ $student->phone }} @endif &middot; Joined {{ $student->created_at->format('d M Y') }}</p>
      </div>
    </div>
    <div class="ov-banner-stats">
      <div><b>{{ $stats['courses'] }}</b><span>{{ \Illuminate\Support\Str::plural('Course', $stats['courses']) }}</span></div>
      <div><b>{{ $stats['videos_watched'] }}</b><span>Videos Watched</span></div>
      <div><b>{{ $stats['certificates'] }}</b><span>Certificates</span></div>
      <div><b>₹{{ number_format($stats['total_spent'], 0) }}</b><span>Total Spent</span></div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <a href="{{ route('admin.students.index') }}" style="color:var(--muted);text-decoration:none;font-size:12px"><i class="bi bi-arrow-left me-1"></i>Back to Student Management</a>
    <div class="d-flex gap-2">
      <button class="bghost" style="font-size:12px;padding:8px 16px" data-bs-toggle="modal" data-bs-target="#editStudent"><i class="bi bi-pencil-fill me-1"></i>Edit Profile</button>
      <a href="{{ route('admin.students.id-card.view', $student) }}" target="_blank" class="bghost" style="text-decoration:none;font-size:12px;padding:8px 16px"><i class="bi bi-eye-fill me-1"></i>View ID Card</a>
      <a href="{{ route('admin.students.id-card.download', $student) }}" class="bsave" style="text-decoration:none;font-size:12px;padding:8px 16px"><i class="bi bi-download me-1"></i>Download</a>
    </div>
  </div>

  <div class="shead mb-3"><h4 style="font-size:16px">Personal Information</h4></div>
  <div class="card-rt mb-4">
    <div class="row g-3">
      <div class="col-md-3"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Student ID</span><div style="font-size:13px;font-weight:600">{{ $student->student_code ?? 'Not yet issued' }}</div></div>
      <div class="col-md-3"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Date of Birth</span><div style="font-size:13px;font-weight:600">{{ optional($student->date_of_birth)->format('d M Y') ?? '—' }}</div></div>
      <div class="col-md-3"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Blood Group</span><div style="font-size:13px;font-weight:600">{{ $student->blood_group ?? '—' }}</div></div>
      <div class="col-md-3"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Emergency Contact</span><div style="font-size:13px;font-weight:600">{{ $student->emergency_contact ?? '—' }}</div></div>
      <div class="col-md-6"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Guardian / Father's Name</span><div style="font-size:13px;font-weight:600">{{ $student->guardian_name ?? '—' }}</div></div>
      <div class="col-md-6"><span style="font-size:11px;color:var(--muted);text-transform:uppercase">Address</span><div style="font-size:13px;font-weight:600">{{ $student->address ?? '—' }}</div></div>
      <div class="col-12">
        <span style="font-size:11px;color:var(--muted);text-transform:uppercase">Enrolled Courses</span><br>
        @forelse ($courses as $c)
          <span class="badge-rt bg-pending" style="margin:2px 4px 0 0">{{ $c['course']->name }}</span>
        @empty
          <span style="font-size:13px;color:var(--muted)">Not enrolled in any course.</span>
        @endforelse
      </div>
    </div>
  </div>

  <div class="shead mb-3"><h4 style="font-size:16px">Fee &amp; Installments</h4></div>
  <div class="row g-3 mb-4">
    @forelse ($courses as $c)
      @php $e = $c['enrollment']; @endphp
      <div class="col-md-6">
        <div class="card-rt">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 style="font-size:13px;font-weight:700;margin:0">{{ $c['course']->name }}</h6>
            <button class="action-btn" data-bs-toggle="modal" data-bs-target="#feePay{{ $e->id }}" title="Record Payment"><i class="bi bi-cash-coin"></i></button>
          </div>
          <div class="d-flex justify-content-between" style="font-size:12px;color:var(--muted)">
            <span>Total Fee</span><span style="font-weight:700;color:var(--text)">₹{{ number_format($e->final_amount, 0) }}</span>
          </div>
          <div class="d-flex justify-content-between" style="font-size:12px;color:var(--muted)">
            <span>Paid So Far</span><span style="font-weight:700;color:var(--ok)">₹{{ number_format($e->amount_paid, 0) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2" style="font-size:12px;color:var(--muted)">
            <span>Balance Due</span><span style="font-weight:700;color:{{ $e->balance_due > 0 ? 'var(--danger)' : 'var(--ok)' }}">₹{{ number_format($e->balance_due, 0) }}</span>
          </div>
          <div class="ptrack mb-2"><div class="pfill" style="width:{{ $e->final_amount > 0 ? min(100, round(($e->amount_paid / $e->final_amount) * 100)) : 100 }}%"></div></div>
          @if ($e->payments->isNotEmpty())
            <div style="font-size:11px;color:var(--muted);max-height:90px;overflow-y:auto">
              @foreach ($e->payments->where('status', 'paid')->sortByDesc('paid_at') as $p)
                <div class="d-flex justify-content-between py-1" style="border-bottom:1px solid var(--border)">
                  <span>{{ optional($p->paid_at)->format('d M Y') ?? $p->created_at->format('d M Y') }} &middot; {{ ucfirst(str_replace('_',' ', $p->method ?? '—')) }}</span>
                  <span style="font-weight:600">₹{{ number_format($p->amount, 0) }}</span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>

      <div class="modal fade" id="feePay{{ $e->id }}" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Record Payment — {{ $c['course']->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('admin.enrollments.payments.store', $e) }}">
              @csrf
              <div class="modal-body">
                <p style="font-size:12px;color:var(--muted)">Balance due: ₹{{ number_format($e->balance_due, 2) }}</p>
                <div class="mb-3"><label class="flbl">Amount (₹)</label><input class="fctrl" type="number" step="0.01" min="0.01" max="{{ $e->balance_due }}" name="amount" required/></div>
                <div class="mb-3">
                  <label class="flbl">Method</label>
                  <select class="fctrl" name="method">
                    <option value="cash">Cash</option>
                    <option value="upi">UPI</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cheque">Cheque</option>
                    <option value="card">Card</option>
                  </select>
                </div>
                <div class="mb-3"><label class="flbl">Date</label><input class="fctrl" type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}"/></div>
                <div class="mb-1"><label class="flbl">Note</label><input class="fctrl" name="note" placeholder="e.g. 2nd installment"/></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="bsave">Record Payment</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">Not enrolled in any course.</p></div>
    @endforelse
  </div>

  <div class="shead mb-3"><h4 style="font-size:16px">Course Progress</h4></div>
  <div class="row g-3 mb-4">
    @forelse ($courses as $c)
      <div class="col-md-6 col-lg-4">
        <div class="card-rt">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 style="font-size:13px;font-weight:700;margin:0">{{ $c['course']->name }}</h6>
            <span style="font-size:12px;font-weight:700;color:var(--orange)">{{ $c['percent'] }}%</span>
          </div>
          <div class="ptrack mb-2"><div class="pfill" style="width:{{ $c['percent'] }}%"></div></div>
          <span class="badge-rt {{ $c['enrollment']->status === 'completed' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($c['enrollment']->status) }}</span>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="font-size:13px;color:var(--muted)">Not enrolled in any course.</p></div>
    @endforelse
  </div>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card-rt mb-4">
        <div class="card-title">Certificates</div>
        @forelse ($certificates as $cert)
          <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--border)">
            <div>
              <div style="font-size:13px;font-weight:600">{{ $cert->course->name }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $cert->cert_code }}</div>
            </div>
            <div class="d-flex align-items-center gap-2">
              @if ($cert->status === 'issued')
                @if ($cert->include_certificate)
                  <span style="font-size:11px;color:var(--ok)" title="Certificate enabled"><i class="bi bi-award-fill"></i></span>
                @endif
                @if ($cert->include_marksheet)
                  <span style="font-size:11px;color:{{ $cert->subjects->isNotEmpty() ? 'var(--ok)' : 'var(--muted)' }}" title="Marksheet {{ $cert->subjects->isNotEmpty() ? 'ready' : 'not filled in yet' }}"><i class="bi bi-file-earmark-text-fill"></i></span>
                @endif
                <button class="action-btn" style="border:none;background:none" title="Edit Certificate" data-bs-toggle="modal" data-bs-target="#certDocs{{ $cert->id }}"><i class="bi bi-pencil-square"></i></button>
              @endif
              <span class="badge-rt {{ $cert->status === 'issued' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($cert->status) }}</span>
            </div>
          </div>

          @if ($cert->status === 'issued')
            <div class="modal fade" id="certDocs{{ $cert->id }}" tabindex="-1">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Marksheet Details — {{ $cert->course->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
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
                          <input class="form-check-input" type="checkbox" name="include_certificate" value="1" id="showIncludeCert{{ $cert->id }}" {{ $cert->include_certificate ? 'checked' : '' }}>
                          <label class="form-check-label" for="showIncludeCert{{ $cert->id }}">Certificate</label>
                        </div>
                        <div class="form-check">
                          <input type="hidden" name="include_marksheet" value="0">
                          <input class="form-check-input" type="checkbox" name="include_marksheet" value="1" id="showIncludeMarksheet{{ $cert->id }}" {{ $cert->include_marksheet ? 'checked' : '' }}>
                          <label class="form-check-label" for="showIncludeMarksheet{{ $cert->id }}">Marksheet</label>
                        </div>
                      </div>

                      <label class="flbl mt-2">Subjects &amp; Marks (for Marksheet)</label>
                      @php
                        $existingSubjects = $cert->subjects->isNotEmpty() ? $cert->subjects : $cert->course->modules->map(fn ($m) => (object) ['subject' => $m->title, 'max_marks' => 100, 'marks_obtained' => null]);
                        if ($existingSubjects->isEmpty()) { $existingSubjects = collect([(object) ['subject' => '', 'max_marks' => 100, 'marks_obtained' => null]]); }
                      @endphp
                      <div id="subj-certdocs-{{ $cert->id }}" data-next-index="{{ $existingSubjects->count() }}">
                        @foreach ($existingSubjects as $i => $s)
                          <div class="d-flex gap-2 mb-2 subj-row">
                            <input class="fctrl" style="flex:2" type="text" name="subjects[{{ $i }}][subject]" value="{{ $s->subject }}" placeholder="Subject" />
                            <input class="fctrl" style="flex:1" type="number" min="1" name="subjects[{{ $i }}][max_marks]" value="{{ $s->max_marks }}" placeholder="Max Marks" />
                            <input class="fctrl" style="flex:1" type="number" min="0" name="subjects[{{ $i }}][marks_obtained]" value="{{ $s->marks_obtained }}" placeholder="Obtained" />
                            <button type="button" class="action-btn danger" style="border:none;background:none" onclick="this.closest('.subj-row').remove()"><i class="bi bi-trash"></i></button>
                          </div>
                        @endforeach
                      </div>
                      <button type="button" class="bghost" style="font-size:12px;padding:6px 12px" onclick="addSubjectRow('subj-certdocs-{{ $cert->id }}')"><i class="bi bi-plus-lg me-1"></i>Add Subject</button>
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
          <p style="font-size:13px;color:var(--muted);margin:0">No certificates yet.</p>
        @endforelse
      </div>

      <div class="card-rt">
        <div class="card-title">Quiz Attempts</div>
        @forelse ($quizAttempts as $attempt)
          <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--border)">
            <div>
              <div style="font-size:13px;font-weight:600">{{ $attempt->course->name }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ optional($attempt->submitted_at)->format('d M Y') }}</div>
            </div>
            <span style="font-size:13px;font-weight:700;color:{{ $attempt->percentage >= 50 ? 'var(--ok)' : 'var(--danger)' }}">{{ $attempt->percentage }}%</span>
          </div>
        @empty
          <p style="font-size:13px;color:var(--muted);margin:0">No quiz attempts yet.</p>
        @endforelse
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card-rt">
        <div class="card-title">Payment History</div>
        <div class="table-wrap"><table class="table-rt">
          <thead><tr><th>Type</th><th>Amount</th><th>Method</th><th>Status</th></tr></thead>
          <tbody>
            @forelse ($payments as $p)
              <tr>
                <td>{{ $p->payable_type === 'course_enrollment' ? 'Course' : 'Franchise' }}</td>
                <td>₹{{ number_format($p->amount, 0) }}</td>
                <td>{{ $p->method ? ucfirst($p->method) : '—' }}</td>
                <td>
                  @php $badge = ['paid' => 'bg-paid', 'created' => 'bg-pending', 'failed' => 'bg-failed', 'refunded' => 'bg-inactive'][$p->status] ?? 'bg-inactive'; @endphp
                  <span class="badge-rt {{ $badge }}">{{ ucfirst($p->status) }}</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" style="color:var(--muted)">No payments yet.</td></tr>
            @endforelse
          </tbody>
        </table></div>
      </div>
    </div>
  </div>

  <!-- Edit Student Modal -->
  <div class="modal fade" id="editStudent" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Edit Profile — {{ $student->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.students.update', $student) }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="d-flex align-items-center gap-3 mb-3">
              @if ($student->photoUrl())
                <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;flex-shrink:0">
              @else
                <span style="width:56px;height:56px;border-radius:50%;background:var(--border);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
              @endif
              <div class="flex-grow-1">
                <label class="flbl">Profile Photo</label>
                <input class="fctrl" type="file" name="photo" accept="image/*"/>
              </div>
            </div>
            <div class="mb-3"><label class="flbl">Full Name</label><input class="fctrl" name="name" value="{{ $student->name }}" required/></div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" value="{{ $student->email }}" required/></div>
              <div class="col-6"><label class="flbl">Phone</label><input class="fctrl" name="phone" value="{{ $student->phone }}"/></div>
            </div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Date of Birth</label><input class="fctrl" type="date" name="date_of_birth" value="{{ optional($student->date_of_birth)->format('Y-m-d') }}"/></div>
              <div class="col-6">
                <label class="flbl">Blood Group</label>
                <select class="fctrl" name="blood_group">
                  <option value="">Select</option>
                  @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                    <option value="{{ $bg }}" @selected($student->blood_group === $bg)>{{ $bg }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="mb-3"><label class="flbl">Address</label><textarea class="fctrl" name="address" rows="2">{{ $student->address }}</textarea></div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Guardian / Father's Name</label><input class="fctrl" name="guardian_name" value="{{ $student->guardian_name }}"/></div>
              <div class="col-6"><label class="flbl">Emergency Contact</label><input class="fctrl" name="emergency_contact" value="{{ $student->emergency_contact }}"/></div>
            </div>
            <div class="form-check">
              <input type="hidden" name="is_active" value="0">
              <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActiveShow" {{ $student->is_active ? 'checked' : '' }}>
              <label class="form-check-label" for="editActiveShow">Active</label>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

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
