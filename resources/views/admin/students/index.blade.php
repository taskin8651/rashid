@extends('layouts.admin')

@section('title', 'Student Management')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0"><h4>Student Management</h4><p>{{ $students->total() }} registered students</p></div>
    <div class="d-flex gap-2 flex-wrap">
      <form method="GET" action="{{ route('admin.students.index') }}" class="d-flex gap-2">
        <input class="fctrl" type="text" name="search" value="{{ $search }}" placeholder="Search students…" style="width:220px"/>
        <button class="bsave" type="submit">Search</button>
      </form>
      <a href="{{ route('admin.students.export') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-download me-1"></i>Export CSV</a>
      <button class="bghost" style="font-size:14px;padding:11px 20px" data-bs-toggle="modal" data-bs-target="#addStudent"><i class="bi bi-person-plus-fill me-1"></i>Add Student</button>
      <button class="bsave" style="font-size:14px;padding:11px 20px" data-bs-toggle="modal" data-bs-target="#offlineEnroll"><i class="bi bi-mortarboard-fill me-1"></i>Register &amp; Allot Course</button>
    </div>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Email</th><th>Phone</th><th>Courses</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($students as $s)
          <tr>
            <td>
              <a href="{{ route('admin.students.show', $s) }}" style="color:var(--text);text-decoration:none;display:flex;align-items:center;gap:8px">
                @if ($s->photoUrl())
                  <img src="{{ $s->photoUrl() }}" alt="{{ $s->name }}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;flex-shrink:0">
                @else
                  <span style="width:28px;height:28px;border-radius:50%;background:var(--border);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;flex-shrink:0">{{ strtoupper(substr($s->name, 0, 1)) }}</span>
                @endif
                <span style="font-weight:600">{{ $s->name }}</span>
              </a>
            </td>
            <td>{{ $s->email }}</td>
            <td>{{ $s->phone ?? '—' }}</td>
            <td>
              @forelse ($s->enrollments as $e)
                <span class="badge-rt bg-pending" style="margin:1px">{{ $e->course->name ?? '—' }}</span>
              @empty
                —
              @endforelse
            </td>
            <td>{{ $s->created_at->format('d M Y') }}</td>
            <td><span class="badge-rt {{ $s->is_active ? 'bg-active' : 'bg-inactive' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <a href="{{ route('admin.students.show', $s) }}" class="action-btn" title="View"><i class="bi bi-eye-fill"></i></a>
              <button class="action-btn" style="border:none;background:none" title="Edit Profile" data-bs-toggle="modal" data-bs-target="#editStudent{{ $s->id }}"><i class="bi bi-pencil-fill"></i></button>
              <button class="action-btn" style="border:none;background:none" title="Allot Course" data-bs-toggle="modal" data-bs-target="#allotCourse{{ $s->id }}"><i class="bi bi-mortarboard-fill"></i></button>
              <form method="POST" action="{{ route('admin.students.destroy', $s) }}" onsubmit="return confirm('Delete this student? This cannot be undone.')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>

          <!-- Edit Student Modal -->
          <div class="modal fade" id="editStudent{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Profile — {{ $s->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.students.update', $s) }}" enctype="multipart/form-data">
                  @csrf
                  <div class="modal-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                      @if ($s->photoUrl())
                        <img src="{{ $s->photoUrl() }}" alt="{{ $s->name }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;flex-shrink:0">
                      @else
                        <span style="width:56px;height:56px;border-radius:50%;background:var(--border);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;flex-shrink:0">{{ strtoupper(substr($s->name, 0, 1)) }}</span>
                      @endif
                      <div class="flex-grow-1">
                        <label class="flbl">Profile Photo</label>
                        <input class="fctrl" type="file" name="photo" accept="image/*"/>
                      </div>
                    </div>
                    <div class="mb-3"><label class="flbl">Full Name</label><input class="fctrl" name="name" value="{{ $s->name }}" required/></div>
                    <div class="row g-3 mb-3">
                      <div class="col-6"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" value="{{ $s->email }}" required/></div>
                      <div class="col-6"><label class="flbl">Phone</label><input class="fctrl" name="phone" value="{{ $s->phone }}"/></div>
                    </div>
                    <div class="row g-3 mb-3">
                      <div class="col-6"><label class="flbl">Date of Birth</label><input class="fctrl" type="date" name="date_of_birth" value="{{ optional($s->date_of_birth)->format('Y-m-d') }}"/></div>
                      <div class="col-6">
                        <label class="flbl">Blood Group</label>
                        <select class="fctrl" name="blood_group">
                          <option value="">Select</option>
                          @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                            <option value="{{ $bg }}" @selected($s->blood_group === $bg)>{{ $bg }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                    <div class="mb-3"><label class="flbl">Address</label><textarea class="fctrl" name="address" rows="2">{{ $s->address }}</textarea></div>
                    <div class="row g-3 mb-3">
                      <div class="col-6"><label class="flbl">Guardian / Father's Name</label><input class="fctrl" name="guardian_name" value="{{ $s->guardian_name }}"/></div>
                      <div class="col-6"><label class="flbl">Emergency Contact</label><input class="fctrl" name="emergency_contact" value="{{ $s->emergency_contact }}"/></div>
                    </div>
                    <p style="font-size:11px;color:var(--muted)">Student ID: {{ $s->student_code ?? 'Not yet issued' }}</p>
                    <div class="form-check">
                      <input type="hidden" name="is_active" value="0">
                      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editActive{{ $s->id }}" {{ $s->is_active ? 'checked' : '' }}>
                      <label class="form-check-label" for="editActive{{ $s->id }}">Active</label>
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

          <!-- Allot Course Modal -->
          <div class="modal fade" id="allotCourse{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Allot Course — {{ $s->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.students.allot-course', $s) }}">
                  @csrf
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="flbl">Course</label>
                      <select class="fctrl allot-course-select" name="course_id" data-target="allotFee{{ $s->id }}" required>
                        <option value="">Select course…</option>
                        @foreach ($courses as $c)
                          <option value="{{ $c->id }}" data-price="{{ $c->price }}">{{ $c->name }} — ₹{{ number_format($c->price, 0) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="mb-3"><label class="flbl">Total Fee Agreed (₹)</label><input class="fctrl" type="number" step="0.01" min="0" name="total_fee" id="allotFee{{ $s->id }}" required/></div>
                    <hr style="border-color:var(--border)">
                    <p style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:8px">First Installment (optional)</p>
                    <div class="row g-3 mb-3">
                      <div class="col-6"><label class="flbl">Amount Paid Now (₹)</label><input class="fctrl" type="number" step="0.01" min="0" name="first_payment_amount"/></div>
                      <div class="col-6">
                        <label class="flbl">Method</label>
                        <select class="fctrl" name="first_payment_method">
                          <option value="cash">Cash</option>
                          <option value="upi">UPI</option>
                          <option value="bank_transfer">Bank Transfer</option>
                          <option value="cheque">Cheque</option>
                          <option value="card">Card</option>
                        </select>
                      </div>
                    </div>
                    <div class="mb-1"><label class="flbl">Note</label><input class="fctrl" name="first_payment_note" placeholder="e.g. 1st installment paid at center"/></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="bsave">Allot Course</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No students found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
 <div class="d-flex justify-content-center mt-4">
    {{ $students->links('pagination::bootstrap-5') }}
</div>

  <!-- Add Student Modal -->
  <div class="modal fade" id="addStudent" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Student</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.students.store') }}">
          @csrf
          <div class="modal-body">
            <p style="font-size:12px;color:var(--muted)">Creates a plain student account with no course — allot one anytime from the list below.</p>
            <div class="mb-3"><label class="flbl">Full Name</label><input class="fctrl" name="name" required/></div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" required/></div>
              <div class="col-6"><label class="flbl">Phone</label><input class="fctrl" name="phone"/></div>
            </div>
            <div class="mb-1"><label class="flbl">Password</label><input class="fctrl" type="password" name="password" minlength="6" required/></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Add Student</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Register Offline Student Modal -->
  <div class="modal fade" id="offlineEnroll" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Register Offline Student</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.students.offline-enroll') }}">
          @csrf
          <div class="modal-body">
            <p style="font-size:12px;color:var(--muted)">If the email already belongs to a student, they'll be allotted this course on their existing account — no new account is created.</p>
            <div class="mb-3"><label class="flbl">Full Name</label><input class="fctrl" name="name" required/></div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" required/></div>
              <div class="col-6"><label class="flbl">Phone</label><input class="fctrl" name="phone"/></div>
            </div>
            <div class="mb-3"><label class="flbl">Password (only for a new student)</label><input class="fctrl" type="password" name="password" minlength="6" placeholder="Leave blank if already registered"/></div>
            <div class="mb-3">
              <label class="flbl">Allot Course</label>
              <select class="fctrl" name="course_id" id="offlineCourse" required>
                <option value="">Select course…</option>
                @foreach ($courses as $c)
                  <option value="{{ $c->id }}" data-price="{{ $c->price }}">{{ $c->name }} — ₹{{ number_format($c->price, 0) }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3"><label class="flbl">Total Fee Agreed (₹)</label><input class="fctrl" type="number" step="0.01" min="0" name="total_fee" id="offlineTotalFee" required/></div>
            <hr style="border-color:var(--border)">
            <p style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:8px">First Installment (optional)</p>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Amount Paid Now (₹)</label><input class="fctrl" type="number" step="0.01" min="0" name="first_payment_amount"/></div>
              <div class="col-6">
                <label class="flbl">Method</label>
                <select class="fctrl" name="first_payment_method">
                  <option value="cash">Cash</option>
                  <option value="upi">UPI</option>
                  <option value="bank_transfer">Bank Transfer</option>
                  <option value="cheque">Cheque</option>
                  <option value="card">Card</option>
                </select>
              </div>
            </div>
            <div class="mb-1"><label class="flbl">Note</label><input class="fctrl" name="first_payment_note" placeholder="e.g. 1st installment paid at center"/></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Register &amp; Allot Course</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('offlineCourse')?.addEventListener('change', function () {
      const price = this.options[this.selectedIndex]?.dataset.price;
      if (price) { document.getElementById('offlineTotalFee').value = price; }
    });
    document.querySelectorAll('.allot-course-select').forEach(function (sel) {
      sel.addEventListener('change', function () {
        const price = this.options[this.selectedIndex]?.dataset.price;
        const target = document.getElementById(this.dataset.target);
        if (price && target) { target.value = price; }
      });
    });
  </script>
@endsection
