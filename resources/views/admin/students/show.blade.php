@extends('layouts.admin')

@section('title', 'Student Profile')

@section('content')
  <div class="ov-banner">
    <div class="ov-ribbon"><i class="bi bi-person-fill"></i>Student Profile</div>
    <h4>{{ $student->name }}</h4>
    <p>{{ $student->email }} @if ($student->phone) &middot; {{ $student->phone }} @endif &middot; Joined {{ $student->created_at->format('d M Y') }}</p>
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
      <a href="{{ route('admin.students.id-card.view', $student) }}" target="_blank" class="bghost" style="text-decoration:none;font-size:12px;padding:8px 16px"><i class="bi bi-eye-fill me-1"></i>View ID Card</a>
      <a href="{{ route('admin.students.id-card.download', $student) }}" class="bsave" style="text-decoration:none;font-size:12px;padding:8px 16px"><i class="bi bi-download me-1"></i>Download</a>
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
                <span style="font-size:11px;color:{{ $cert->hasUploadedPdf() ? 'var(--ok)' : 'var(--muted)' }}" title="Certificate PDF {{ $cert->hasUploadedPdf() ? 'uploaded' : 'not uploaded' }}"><i class="bi bi-file-earmark-check-fill"></i></span>
                <span style="font-size:11px;color:{{ $cert->hasUploadedMarksheet() ? 'var(--ok)' : 'var(--muted)' }}" title="Marksheet {{ $cert->hasUploadedMarksheet() ? 'uploaded' : 'not uploaded' }}"><i class="bi bi-file-earmark-text-fill"></i></span>
                <button class="action-btn" style="border:none;background:none" title="Upload/Replace Documents" data-bs-toggle="modal" data-bs-target="#certDocs{{ $cert->id }}"><i class="bi bi-upload"></i></button>
              @endif
              <span class="badge-rt {{ $cert->status === 'issued' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($cert->status) }}</span>
            </div>
          </div>

          @if ($cert->status === 'issued')
            <div class="modal fade" id="certDocs{{ $cert->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">Documents — {{ $cert->course->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST" action="{{ route('admin.certificates.documents.update', $cert) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                      <label class="flbl">Certificate PDF {{ $cert->hasUploadedPdf() ? '(replace)' : '(upload)' }}</label>
                      <input class="fctrl mb-3" type="file" name="certificate_pdf" accept="application/pdf"/>
                      <label class="flbl">Marksheet {{ $cert->hasUploadedMarksheet() ? '(replace)' : '(upload)' }}</label>
                      <input class="fctrl" type="file" name="marksheet" accept="application/pdf"/>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="bsave">Save Documents</button>
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
@endsection
