@extends('layouts.franchise')

@section('title', 'Certificates')

@section('content')
  <div class="shead mb-4"><h4>Certificates</h4><p>Certificate applications and issued certificates for your students</p></div>

  <div class="shead mb-3"><h4 style="font-size:16px">Applications</h4></div>
  <div class="card-rt mb-4">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Course</th><th>Completion Date</th><th>Status</th></tr></thead>
      <tbody>
        @forelse ($applications as $a)
          <tr>
            <td>{{ $a->user->name ?? '—' }}</td>
            <td>{{ $a->course->name ?? '—' }}</td>
            <td>{{ optional($a->completion_date)->format('d M Y') ?? '—' }}</td>
            <td>
              @php $badge = ['pending' => 'bg-pending', 'approved' => 'bg-active', 'rejected' => 'bg-failed'][$a->status] ?? 'bg-inactive'; @endphp
              <span class="badge-rt {{ $badge }}">{{ ucfirst($a->status) }}</span>
              @if ($a->proof_path)
                <a href="{{ route('franchise.certificate-applications.proof', $a) }}" class="ms-2" style="font-size:11px" title="Download Proof"><i class="bi bi-paperclip"></i></a>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="4" style="color:var(--muted)">No certificate applications yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mb-4">{{ $applications->links() }}</div>

  <div class="shead mb-3"><h4 style="font-size:16px">Issued Certificates</h4></div>
  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Course</th><th>Cert Code</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($certificates as $cert)
          <tr>
            <td>{{ $cert->user->name }}</td>
            <td>{{ $cert->course->name }}</td>
            <td>{{ $cert->cert_code }}</td>
            <td><span class="badge-rt {{ $cert->status === 'issued' ? 'bg-active' : 'bg-pending' }}">{{ ucfirst($cert->status) }}</span></td>
            <td>
              @if ($cert->status === 'issued')
                @if ($cert->include_certificate)
                  <a href="{{ route('franchise.certificates.download', $cert) }}" class="action-btn" title="Download Certificate"><i class="bi bi-award-fill"></i></a>
                @endif
                @if ($cert->include_marksheet && $cert->subjects->isNotEmpty())
                  <a href="{{ route('franchise.certificates.marksheet', $cert) }}" class="action-btn" title="Download Marksheet"><i class="bi bi-file-earmark-text-fill"></i></a>
                @endif
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:var(--muted)">No certificates issued yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $certificates->links() }}</div>
@endsection
