@extends('layouts.admin')

@section('title', 'Placements')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-start flex-wrap gap-2">
    <div><h4>Placements</h4><p>Student &amp; franchise-submitted job placements &mdash; approved ones are featured on the public website</p></div>
    <a href="{{ route('placements') }}" target="_blank" class="bghost" style="text-decoration:none"><i class="bi bi-globe2 me-1"></i>View Public Page</a>
  </div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.placements.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All</a>
    <a href="{{ route('admin.placements.index', ['status' => 'pending']) }}" class="badge-rt {{ $status === 'pending' ? 'bg-pending' : 'bg-inactive' }}" style="text-decoration:none">Pending ({{ $pendingCount }})</a>
    <a href="{{ route('admin.placements.index', ['status' => 'approved']) }}" class="badge-rt {{ $status === 'approved' ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">Approved</a>
    <a href="{{ route('admin.placements.index', ['status' => 'rejected']) }}" class="badge-rt {{ $status === 'rejected' ? 'bg-failed' : 'bg-inactive' }}" style="text-decoration:none">Rejected</a>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Company &amp; Role</th><th>Course</th><th>Source</th><th>Proof</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($placements as $p)
          <tr>
            <td>{{ optional($p->user)->name ?: 'Unknown' }}<div style="font-size:11px;color:var(--muted)">{{ optional($p->user)->email ?: '—' }}</div></td>
            <td>{{ $p->company_name }} &mdash; {{ $p->job_title }}<div style="font-size:11px;color:var(--muted)">{{ $p->job_type ?: '—' }}{{ $p->work_mode ? ' · ' . $p->work_mode : '' }}{{ $p->package ? ' · ' . $p->package : '' }}</div></td>
            <td>{{ optional($p->course)->name ?: '—' }}</td>
            <td><span style="font-size:11px;color:var(--muted)">{{ $p->franchiseBooking->city ?? 'R-Tech Direct' }}</span></td>
            <td>
              @if ($p->offer_letter_path)
                <a href="{{ route('admin.placements.proof', $p) }}" class="action-btn" title="View Proof"><i class="bi bi-file-earmark-check-fill"></i></a>
              @else
                —
              @endif
            </td>
            <td>
              <span class="badge-rt {{ $p->status === 'approved' ? 'bg-active' : ($p->status === 'pending' ? 'bg-pending' : 'bg-failed') }}">{{ ucfirst($p->status) }}</span>
              @if ($p->status === 'approved' && $p->is_featured)
                <div style="font-size:11px;color:var(--orange);margin-top:2px"><i class="bi bi-star-fill"></i> Featured</div>
              @endif
            </td>
            <td>
              <div class="d-flex align-items-center justify-content-end gap-1 flex-wrap" style="min-width:140px">
                @if ($p->status === 'pending')
                  <button class="btn btn-sm btn-outline-success" title="Approve &amp; Publish" data-bs-toggle="modal" data-bs-target="#approvePlacement{{ $p->id }}"><i class="bi bi-check-lg"></i></button>
                  <button class="btn btn-sm btn-outline-danger" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectPlacement{{ $p->id }}"><i class="bi bi-x-lg"></i></button>
                @else
                  <button class="action-btn" style="border:none;background:none" title="Edit" data-bs-toggle="modal" data-bs-target="#editPlacement{{ $p->id }}"><i class="bi bi-pencil-square"></i></button>
                  @if ($p->status === 'approved')
                    <form method="POST" action="{{ route('admin.placements.feature', $p) }}">
                      @csrf
                      <button type="submit" class="action-btn" style="border:none;background:none" title="{{ $p->is_featured ? 'Unfeature' : 'Feature on homepage' }}"><i class="bi {{ $p->is_featured ? 'bi-star-fill' : 'bi-star' }}"></i></button>
                    </form>
                  @endif
                @endif
                <form method="POST" action="{{ route('admin.placements.destroy', $p) }}" onsubmit="return confirm('Delete this placement record?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" style="color:var(--muted)">No placements found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <div class="mt-3">{{ $placements->links() }}</div>

  @foreach ($placements as $p)
    <div class="modal fade" id="approvePlacement{{ $p->id }}" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Approve &amp; Publish Placement</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('admin.placements.approve', $p) }}">
            @csrf
            <div class="modal-body">
              <p style="font-size:13px;color:var(--muted)">Publishing placement for <b style="color:var(--text)">{{ optional($p->user)->name ?: 'Student' }}</b>. Review the details before making it public.</p>
              @include('admin.placements._fields', ['p' => $p, 'courses' => $courses])
            </div>
            <div class="modal-footer">
              <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="bsave">Approve &amp; Publish</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="rejectPlacement{{ $p->id }}" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Reject Placement</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('admin.placements.reject', $p) }}">
            @csrf
            <div class="modal-body">
              <p style="font-size:13px;color:var(--muted)">Rejecting {{ optional($p->user)->name ?: 'Student' }}'s placement at {{ $p->company_name }}.</p>
              <label class="flbl">Reason (shown to student)</label>
              <textarea class="fctrl" name="admin_notes" rows="3" placeholder="e.g. Could not verify offer letter"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="bsave" style="background:var(--danger)">Reject Placement</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    @if ($p->status !== 'pending')
      <div class="modal fade" id="editPlacement{{ $p->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Placement &mdash; {{ optional($p->user)->name ?: 'Student' }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('admin.placements.update', $p) }}">
              @csrf
              <div class="modal-body">
                @include('admin.placements._fields', ['p' => $p, 'courses' => $courses])
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
@endsection
