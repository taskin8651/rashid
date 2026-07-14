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

  <p class="mb-4"><a href="{{ route('admin.students.index') }}" style="color:var(--muted);text-decoration:none;font-size:12px"><i class="bi bi-arrow-left me-1"></i>Back to Student Management</a></p>

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
            <span class="badge-rt {{ $cert->status === 'issued' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($cert->status) }}</span>
          </div>
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
