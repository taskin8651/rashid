@extends('layouts.admin')

@section('title', 'Our Team')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Our Team</h4><p>Public profiles shown on the About page. Franchise submissions need your approval.</p></div>
    <button class="bsave" style="font-size:12px;padding:8px 16px" data-bs-toggle="modal" data-bs-target="#addMember"><i class="bi bi-person-plus-fill me-1"></i>Add Team Member</button>
  </div>

  <div class="d-flex gap-2 mb-3 flex-wrap">
    <a href="{{ route('admin.team-members.index') }}" class="badge-rt {{ !$status ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">All</a>
    <a href="{{ route('admin.team-members.index', ['status' => 'pending']) }}" class="badge-rt {{ $status === 'pending' ? 'bg-pending' : 'bg-inactive' }}" style="text-decoration:none">Pending ({{ $pendingCount }})</a>
    <a href="{{ route('admin.team-members.index', ['status' => 'approved']) }}" class="badge-rt {{ $status === 'approved' ? 'bg-active' : 'bg-inactive' }}" style="text-decoration:none">Approved</a>
    <a href="{{ route('admin.team-members.index', ['status' => 'rejected']) }}" class="badge-rt {{ $status === 'rejected' ? 'bg-failed' : 'bg-inactive' }}" style="text-decoration:none">Rejected</a>
  </div>

  <div class="row g-3">
    @forelse ($members as $m)
      <div class="col-md-4 col-lg-3">
        <div class="card-rt p-0" style="overflow:hidden">
          @if ($m->photoUrl())
            <img src="{{ $m->photoUrl() }}" style="width:100%;height:160px;object-fit:cover;display:block">
          @else
            <div style="width:100%;height:160px;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-size:36px;font-weight:800">{{ strtoupper(substr($m->name, 0, 1)) }}</div>
          @endif
          <div class="p-3">
            <p style="font-size:13px;font-weight:700;margin-bottom:2px">{{ $m->name }}</p>
            <p style="font-size:11px;color:var(--orange);margin-bottom:4px">{{ $m->designation }}</p>
            <p style="font-size:11px;color:var(--muted);margin-bottom:8px">{{ $m->franchiseBooking->city ?? 'R-Tech HQ' }} &middot; {{ $m->creator->name ?? '—' }}</p>
            <span class="badge-rt {{ $m->status === 'approved' ? 'bg-active' : ($m->status === 'pending' ? 'bg-pending' : 'bg-failed') }} mb-2 d-inline-block">{{ ucfirst($m->status) }}</span>
            <div class="d-flex gap-2 flex-wrap">
              @if ($m->status !== 'approved')
                <form method="POST" action="{{ route('admin.team-members.approve', $m) }}">
                  @csrf
                  <button type="submit" class="action-btn" title="Approve"><i class="bi bi-check-lg"></i></button>
                </form>
              @endif
              @if ($m->status !== 'rejected')
                <form method="POST" action="{{ route('admin.team-members.reject', $m) }}">
                  @csrf
                  <button type="submit" class="action-btn" title="Reject"><i class="bi bi-x-lg"></i></button>
                </form>
              @endif
              <button class="action-btn" style="border:none;background:none" title="Edit" data-bs-toggle="modal" data-bs-target="#editMember{{ $m->id }}"><i class="bi bi-pencil-fill"></i></button>
              <form method="POST" action="{{ route('admin.team-members.destroy', $m) }}" onsubmit="return confirm('Remove {{ $m->name }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Edit Modal -->
      <div class="modal fade" id="editMember{{ $m->id }}" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit — {{ $m->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="{{ route('admin.team-members.update', $m) }}" enctype="multipart/form-data">
              @csrf
              <div class="modal-body">
                <div class="mb-3"><label class="flbl">Name</label><input class="fctrl" name="name" value="{{ $m->name }}" required/></div>
                <div class="mb-3"><label class="flbl">Designation</label><input class="fctrl" name="designation" value="{{ $m->designation }}" required/></div>
                <div class="mb-3"><label class="flbl">Bio (optional)</label><textarea class="fctrl" name="bio" rows="2">{{ $m->bio }}</textarea></div>
                <div class="row g-3 mb-3">
                  <div class="col-6"><label class="flbl">Email (optional)</label><input class="fctrl" type="email" name="email" value="{{ $m->email }}"/></div>
                  <div class="col-6"><label class="flbl">Phone (optional)</label><input class="fctrl" name="phone" value="{{ $m->phone }}"/></div>
                </div>
                <div class="mb-3"><label class="flbl">LinkedIn URL (optional)</label><input class="fctrl" type="url" name="linkedin_url" value="{{ $m->linkedin_url }}"/></div>
                <div class="mb-3"><label class="flbl">Sort Order</label><input class="fctrl" type="number" min="0" name="sort_order" value="{{ $m->sort_order }}"/></div>
                <div class="mb-1"><label class="flbl">Replace Photo (optional)</label><input class="fctrl" type="file" name="photo" accept="image/*"/></div>
              </div>
              <div class="modal-footer">
                <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="bsave">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12"><p style="color:var(--muted)">No team members yet.</p></div>
    @endforelse
  </div>

  <div class="mt-3">{{ $members->links() }}</div>

  <!-- Add Member Modal -->
  <div class="modal fade" id="addMember" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Team Member</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.team-members.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="modal-body">
            <div class="mb-3"><label class="flbl">Name</label><input class="fctrl" name="name" required/></div>
            <div class="mb-3"><label class="flbl">Designation</label><input class="fctrl" name="designation" placeholder="e.g. Founder & Director" required/></div>
            <div class="mb-3"><label class="flbl">Bio (optional)</label><textarea class="fctrl" name="bio" rows="2" placeholder="A line or two about them"></textarea></div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Email (optional)</label><input class="fctrl" type="email" name="email"/></div>
              <div class="col-6"><label class="flbl">Phone (optional)</label><input class="fctrl" name="phone"/></div>
            </div>
            <div class="mb-3"><label class="flbl">LinkedIn URL (optional)</label><input class="fctrl" type="url" name="linkedin_url" placeholder="https://linkedin.com/in/..."/></div>
            <div class="mb-3"><label class="flbl">Sort Order</label><input class="fctrl" type="number" min="0" name="sort_order" value="0"/></div>
            <div class="mb-1"><label class="flbl">Photo (optional)</label><input class="fctrl" type="file" name="photo" accept="image/*"/></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Add Team Member</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
