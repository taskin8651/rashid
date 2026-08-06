@extends('layouts.franchise')

@section('title', 'My Students')

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>My Students</h4><p>Students enrolled in your courses</p></div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('franchise.students.export') }}" class="bghost" style="text-decoration:none;font-size:14px;padding:11px 20px"><i class="bi bi-download me-1"></i>Export CSV</a>
      @if ($canManage)
        <button class="bsave" data-bs-toggle="modal" data-bs-target="#offlineEnroll"><i class="bi bi-person-plus-fill me-1"></i>Register Offline Student</button>
      @endif
    </div>
  </div>

  <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden">
    <div class="prow" style="background:rgba(var(--text-rgb),.03);font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase">
      <div style="flex:2">Student</div><div style="flex:2">Course</div><div style="flex:1">Enrolled</div><div style="flex:2">Fee / Paid / Balance</div>
      <div style="flex:1;text-align:right">Actions</div>
    </div>
    @forelse ($students as $enrollment)
      <div class="prow">
        <div style="flex:2"><div style="font-size:14px;font-weight:700">{{ $enrollment->user->name }}</div><div style="font-size:11px;color:var(--muted)">{{ $enrollment->user->email }}</div></div>
        <div style="flex:2;font-size:13px">{{ $enrollment->course->name }}</div>
        <div style="flex:1;font-size:13px;color:rgba(var(--text-rgb),.7)">{{ optional($enrollment->enrolled_at)->format('d M Y') }}</div>
        <div style="flex:2;font-size:12px">
          <div>Fee: <b>₹{{ number_format($enrollment->final_amount, 0) }}</b> &middot; Paid: <b style="color:var(--ok)">₹{{ number_format($enrollment->amount_paid, 0) }}</b></div>
          <div>Balance: <b style="color:{{ $enrollment->balance_due > 0 ? 'var(--danger)' : 'var(--ok)' }}">₹{{ number_format($enrollment->balance_due, 0) }}</b></div>
        </div>
        <div style="flex:1;text-align:right">
          <a href="{{ route('franchise.students.show', $enrollment->user) }}" class="action-btn" title="View Profile"><i class="bi bi-eye-fill"></i></a>
          @if ($canManage)
            <button class="action-btn" data-bs-toggle="modal" data-bs-target="#feePay{{ $enrollment->id }}" title="Record Payment"><i class="bi bi-cash-coin"></i></button>
            <button class="action-btn" data-bs-toggle="modal" data-bs-target="#allotCourse{{ $enrollment->user_id }}" title="Allot Another Course"><i class="bi bi-mortarboard-fill"></i></button>
          @endif
        </div>
        @if ($canManage)

          <div class="modal fade" id="allotCourse{{ $enrollment->user_id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Allot Course — {{ $enrollment->user->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('franchise.students.allot-course', $enrollment->user) }}">
                  @csrf
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="flbl">Course</label>
                      <select class="fctrl allot-course-select" name="course_id" data-target="allotFee{{ $enrollment->user_id }}" required>
                        <option value="">Select course…</option>
                        @foreach ($courses as $c)
                          <option value="{{ $c->id }}" data-price="{{ $c->price }}">{{ $c->name }} — ₹{{ number_format($c->price, 0) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="mb-3"><label class="flbl">Total Fee Agreed (₹)</label><input class="fctrl" type="number" step="0.01" min="0" name="total_fee" id="allotFee{{ $enrollment->user_id }}" required/></div>
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

          <div class="modal fade" id="feePay{{ $enrollment->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Record Payment — {{ $enrollment->user->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('franchise.enrollments.payments.store', $enrollment) }}">
                  @csrf
                  <div class="modal-body">
                    <p style="font-size:12px;color:var(--muted)">Balance due: ₹{{ number_format($enrollment->balance_due, 2) }}</p>
                    <div class="mb-3"><label class="flbl">Amount (₹)</label><input class="fctrl" type="number" step="0.01" min="0.01" max="{{ $enrollment->balance_due }}" name="amount" required/></div>
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
        @endif
      </div>
    @empty
      <div class="prow"><div style="flex:2;font-size:13px;color:var(--muted)">No students enrolled yet.</div></div>
    @endforelse
  </div>

  @if ($canManage)
    <!-- Register Offline Student Modal -->
    <div class="modal fade" id="offlineEnroll" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Register Offline Student</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('franchise.students.offline-enroll') }}">
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
                <select class="fctrl" name="course_id" id="fOfflineCourse" required>
                  <option value="">Select course…</option>
                  @foreach ($courses as $c)
                    <option value="{{ $c->id }}" data-price="{{ $c->price }}">{{ $c->name }} — ₹{{ number_format($c->price, 0) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3"><label class="flbl">Total Fee Agreed (₹)</label><input class="fctrl" type="number" step="0.01" min="0" name="total_fee" id="fOfflineTotalFee" required/></div>
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
      document.getElementById('fOfflineCourse')?.addEventListener('change', function () {
        const price = this.options[this.selectedIndex]?.dataset.price;
        if (price) { document.getElementById('fOfflineTotalFee').value = price; }
      });
    </script>
  @endif

  <script>
    document.querySelectorAll('.allot-course-select').forEach(function (sel) {
      sel.addEventListener('change', function () {
        const price = this.options[this.selectedIndex]?.dataset.price;
        const target = document.getElementById(this.dataset.target);
        if (price && target) { target.value = price; }
      });
    });
  </script>
@endsection
