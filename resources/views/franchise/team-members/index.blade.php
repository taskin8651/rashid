@extends('layouts.franchise')

@section('title', 'Our Team')

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Our Team</h4><p>Showcase your institute's team on the public About page — submissions need R-Tech's approval</p></div>
    @if ($bookings->isNotEmpty())
      <button class="bsave" data-bs-toggle="modal" data-bs-target="#addMember"><i class="bi bi-person-plus-fill me-1"></i>Add Team Member</button>
    @endif
  </div>

  @if ($bookings->isEmpty())
    <div class="card-rt mt-4" style="padding:24px">
      <p style="font-size:13px;color:var(--muted);margin:0">You need a paid franchise booking before you can add team members.</p>
    </div>
  @else
    <div class="row g-3 mt-1">
      @forelse ($members as $m)
        <div class="col-md-4 col-lg-3">
          <div class="card-rt p-0" style="overflow:hidden">
            @if ($m->photoUrl())
              <img src="{{ $m->photoUrl() }}" style="width:100%;height:150px;object-fit:cover;display:block">
            @else
              <div style="width:100%;height:150px;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800">{{ strtoupper(substr($m->name, 0, 1)) }}</div>
            @endif
            <div class="p-3">
              <p style="font-size:13px;font-weight:700;margin-bottom:2px">{{ $m->name }}</p>
              <p style="font-size:11px;color:var(--orange);margin-bottom:8px">{{ $m->designation }}</p>
              <span class="badge-rt {{ $m->status === 'approved' ? 'bg-active' : ($m->status === 'pending' ? 'bg-pending' : 'bg-failed') }} mb-2 d-inline-block">{{ ucfirst($m->status) }}</span>
              <div class="d-flex gap-2 flex-wrap">
                <button class="action-btn" style="border:none;background:none" title="Edit" data-bs-toggle="modal" data-bs-target="#editMember{{ $m->id }}"><i class="bi bi-pencil-fill"></i></button>
                <form method="POST" action="{{ route('franchise.team-members.destroy', $m) }}" onsubmit="return confirm('Remove {{ $m->name }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="editMember{{ $m->id }}" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header"><h5 class="modal-title">Edit — {{ $m->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
              <form method="POST" action="{{ route('franchise.team-members.update', $m) }}" enctype="multipart/form-data">
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
        <div class="col-12"><p style="font-size:13px;color:var(--muted)">No team members added yet.</p></div>
      @endforelse
    </div>

    <!-- Add Member Modal -->
    <div class="modal fade" id="addMember" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Add Team Member</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('franchise.team-members.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              @if ($bookings->count() > 1)
                <div class="mb-3">
                  <label class="flbl">Institute</label>
                  <select class="fctrl" name="franchise_booking_id" required>
                    @foreach ($bookings as $b)
                      <option value="{{ $b->id }}">{{ $b->city }}</option>
                    @endforeach
                  </select>
                </div>
              @else
                <input type="hidden" name="franchise_booking_id" value="{{ $bookings->first()->id ?? '' }}"/>
              @endif
              <div class="mb-3"><label class="flbl">Name</label><input class="fctrl" name="name" required/></div>
              <div class="mb-3"><label class="flbl">Designation</label><input class="fctrl" name="designation" placeholder="e.g. Center Head" required/></div>
              <div class="mb-3"><label class="flbl">Bio (optional)</label><textarea class="fctrl" name="bio" rows="2" placeholder="A line or two about them"></textarea></div>
              <div class="row g-3 mb-3">
                <div class="col-6"><label class="flbl">Email (optional)</label><input class="fctrl" type="email" name="email"/></div>
                <div class="col-6"><label class="flbl">Phone (optional)</label><input class="fctrl" name="phone"/></div>
              </div>
              <div class="mb-3"><label class="flbl">LinkedIn URL (optional)</label><input class="fctrl" type="url" name="linkedin_url" placeholder="https://linkedin.com/in/..."/></div>
              <div class="mb-1"><label class="flbl">Photo (optional)</label><input class="fctrl" type="file" name="photo" accept="image/*"/></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="bsave">Submit for Approval</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endsection
