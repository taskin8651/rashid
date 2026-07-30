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
      <button class="bsave" style="font-size:14px;padding:11px 20px" data-bs-toggle="modal" data-bs-target="#offlineEnroll"><i class="bi bi-person-plus-fill me-1"></i>Register Offline Student</button>
    </div>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Email</th><th>Course</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($students as $s)
          <tr>
            <td><a href="{{ route('admin.students.show', $s) }}" style="color:var(--text);text-decoration:none;font-weight:600">{{ $s->name }}</a></td>
            <td>{{ $s->email }}</td>
            <td>{{ optional($s->enrollments->first())->course->name ?? '—' }}</td>
            <td>{{ $s->created_at->format('d M Y') }}</td>
            <td><span class="badge-rt {{ $s->is_active ? 'bg-active' : 'bg-inactive' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <a href="{{ route('admin.students.show', $s) }}" class="action-btn" title="View"><i class="bi bi-eye-fill"></i></a>
              <form method="POST" action="{{ route('admin.students.destroy', $s) }}" onsubmit="return confirm('Delete this student? This cannot be undone.')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">No students found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $students->links() }}</div>

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
  </script>
@endsection
