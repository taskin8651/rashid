@extends('layouts.franchise')

@section('title', $lead->name)

@php
  $statusBadge = ['new' => 'bg-pending', 'contacted' => 'bg-pending', 'follow_up' => 'bg-pending', 'interested' => 'bg-active', 'converted' => 'bg-paid', 'lost' => 'bg-failed'];
  $statusLabel = fn ($s) => ucwords(str_replace('_', ' ', $s));
@endphp

@section('content')
  <a href="{{ route('franchise.leads.index') }}" style="color:var(--muted);text-decoration:none;font-size:12px" class="d-inline-block mb-3"><i class="bi bi-arrow-left me-1"></i>Back to Leads</a>

  <div class="shead d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div>
      <h4>{{ $lead->name }}</h4>
      <p>{{ $lead->phone }}{{ $lead->email ? ' · '.$lead->email : '' }} &middot; Received {{ $lead->created_at->format('d M Y') }}</p>
    </div>
    <span class="badge-rt {{ $statusBadge[$lead->status] }}" style="font-size:13px">{{ $statusLabel($lead->status) }}</span>
  </div>

  <div class="row g-4 mt-1">
    <div class="col-lg-7">
      <div class="card-rt mb-4">
        <div class="card-title">Follow-up</div>
        @if ($lead->status === 'converted')
          <p style="font-size:13px;color:var(--ok);margin:0"><i class="bi bi-check-circle-fill me-1"></i>Converted{{ $lead->convertedEnrollment ? ' — enrolled in '.$lead->convertedEnrollment->course->name : '' }}</p>
        @else
          <form method="POST" action="{{ route('franchise.leads.status.update', $lead) }}" class="d-flex gap-2 align-items-end mb-3 flex-wrap">
            @csrf
            <div>
              <label class="flbl">Status</label>
              <select name="status" class="fctrl" id="statusSelect" onchange="this.value === 'lost' ? new bootstrap.Modal(document.getElementById('lostReason')).show() : null">
                @foreach (\App\Models\StudentLead::STATUSES as $s)
                  @if ($s !== 'converted')
                    <option value="{{ $s }}" @selected($lead->status === $s)>{{ $statusLabel($s) }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <button type="submit" class="bsave" style="font-size:12px;padding:9px 16px" onclick="return document.getElementById('statusSelect').value !== 'lost'">Update Status</button>
          </form>

          <div class="modal fade" id="lostReason" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Mark as Lost</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('franchise.leads.status.update', $lead) }}">
                  @csrf
                  <input type="hidden" name="status" value="lost"/>
                  <div class="modal-body">
                    <label class="flbl">Reason (optional)</label>
                    <input class="fctrl" name="lost_reason" placeholder="e.g. Joined elsewhere, budget mismatch"/>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="bsave" style="background:var(--danger)">Mark Lost</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif

        <hr style="border-color:var(--border)">
        <p style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;margin-bottom:8px">Notes</p>
        <div style="max-height:260px;overflow-y:auto;margin-bottom:12px">
          @forelse ($lead->notes as $note)
            <div class="py-2" style="border-bottom:1px solid var(--border)">
              <div style="font-size:13px">{{ $note->note }}</div>
              <div style="font-size:11px;color:var(--muted)">{{ $note->author->name ?? 'System' }} &middot; {{ $note->created_at->format('d M Y, h:i A') }}</div>
            </div>
          @empty
            <p style="font-size:12px;color:var(--muted);margin:0">No notes yet.</p>
          @endforelse
        </div>
        <form method="POST" action="{{ route('franchise.leads.notes.store', $lead) }}">
          @csrf
          <textarea class="fctrl mb-2" name="note" rows="2" placeholder="What happened on this call/visit? Any next step?" required></textarea>
          <div class="d-flex gap-2 align-items-end flex-wrap">
            <div class="flex-grow-1"><label class="flbl">Set Next Follow-up (optional)</label><input class="fctrl" type="date" name="next_follow_up_date"/></div>
            <button type="submit" class="bsave" style="font-size:12px;padding:9px 16px">Add Note</button>
          </div>
        </form>
      </div>

      @if ($canManage && $lead->status !== 'converted')
        <div class="card-rt">
          <div class="card-title">Convert to Enrolled Student</div>
          <p style="font-size:12px;color:var(--muted)">If this email already belongs to a student, they'll be allotted the course on their existing account.</p>
          <form method="POST" action="{{ route('franchise.leads.convert', $lead) }}">
            @csrf
            <div class="row g-3">
              <div class="col-6"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" value="{{ $lead->email }}" required/></div>
              <div class="col-6"><label class="flbl">Password (new student only)</label><input class="fctrl" type="password" name="password" minlength="6" placeholder="Leave blank if already registered"/></div>
            </div>
            <input type="hidden" name="name" value="{{ $lead->name }}"/>
            <input type="hidden" name="phone" value="{{ $lead->phone }}"/>
            <div class="row g-3 mt-1">
              <div class="col-6">
                <label class="flbl">Course</label>
                <select class="fctrl" name="course_id" id="convertCourse" data-target="convertFee" required>
                  <option value="">Select course…</option>
                  @foreach ($courses as $c)
                    <option value="{{ $c->id }}" data-price="{{ $c->price }}" @selected($lead->course_id === $c->id)>{{ $c->name }} — ₹{{ number_format($c->price, 0) }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-6"><label class="flbl">Total Fee Agreed (₹)</label><input class="fctrl" type="number" step="0.01" min="0" name="total_fee" id="convertFee" value="{{ $lead->course->price ?? '' }}" required/></div>
            </div>
            <div class="row g-3 mt-1">
              <div class="col-6"><label class="flbl">First Payment (₹, optional)</label><input class="fctrl" type="number" step="0.01" min="0" name="first_payment_amount"/></div>
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
            <button type="submit" class="bsave mt-3" style="font-size:12px;padding:9px 16px">Convert &amp; Enroll</button>
          </form>
        </div>
      @endif
    </div>

    <div class="col-lg-5">
      @if ($canManage)
        <div class="card-rt mb-4">
          <div class="card-title">Edit Details</div>
          <form method="POST" action="{{ route('franchise.leads.update', $lead) }}">
            @csrf
            <div class="mb-3"><label class="flbl">Name</label><input class="fctrl" name="name" value="{{ $lead->name }}" required/></div>
            <div class="mb-3"><label class="flbl">Phone</label><input class="fctrl" name="phone" value="{{ $lead->phone }}" required/></div>
            <div class="mb-3"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" value="{{ $lead->email }}"/></div>
            <div class="mb-3">
              <label class="flbl">Course Interested</label>
              <select class="fctrl" name="course_id">
                <option value="">—</option>
                @foreach ($courses as $c)
                  <option value="{{ $c->id }}" @selected($lead->course_id === $c->id)>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="flbl">Assigned To</label>
              <select class="fctrl" name="assigned_to">
                <option value="">Unassigned</option>
                @foreach ($staff as $st)
                  <option value="{{ $st->id }}" @selected($lead->assigned_to === $st->id)>{{ $st->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3"><label class="flbl">Next Follow-up</label><input class="fctrl" type="date" name="next_follow_up_date" value="{{ optional($lead->next_follow_up_date)->format('Y-m-d') }}"/></div>
            <button type="submit" class="bsave" style="font-size:12px;padding:9px 16px">Save Details</button>
          </form>
        </div>

        <form method="POST" action="{{ route('franchise.leads.destroy', $lead) }}" onsubmit="return confirm('Delete this lead? This cannot be undone.')">
          @csrf @method('DELETE')
          <button type="submit" class="bghost w-100" style="font-size:12px;padding:9px 16px;color:var(--danger);border-color:rgba(239,68,68,.3)"><i class="bi bi-trash-fill me-1"></i>Delete Lead</button>
        </form>
      @else
        <div class="card-rt">
          <p style="font-size:12px;color:var(--muted);margin:0">You have follow-up access to this lead — add notes and update its status. Editing details, converting, and deleting are restricted to full lead managers.</p>
        </div>
      @endif
    </div>
  </div>

  <script>
    document.getElementById('convertCourse')?.addEventListener('change', function () {
      const price = this.options[this.selectedIndex]?.dataset.price;
      const target = document.getElementById(this.dataset.target);
      if (price && target) { target.value = price; }
    });
  </script>
@endsection
