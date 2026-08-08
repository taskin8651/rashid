@extends('layouts.franchise')

@section('title', 'Team & Roles')

@php
  $permissionLabels = [
    'manage-leads' => 'Leads (full — create, edit, convert, delete)',
    'follow-up-leads' => 'Leads (follow-up only — notes & status, no edit/convert/delete)',
    'manage-courses' => 'Manage Courses (videos, quiz, assignments, notes)',
    'view-students' => 'View Students',
    'manage-students' => 'Register Students & Manage Fees',
    'manage-gallery' => 'Manage Gallery',
    'manage-placements' => 'Manage Placements',
    'manage-attendance' => 'Manage Attendance',
    'manage-documents' => 'Manage Documents',
    'manage-expenses' => 'Manage Expenses (view income & record expenses)',
    'manage-team-members' => 'Our Team (public profiles on the website)',
  ];
@endphp

@section('content')
  <div class="shead d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Team &amp; Roles</h4><p>Create roles for your staff and control what they can access</p></div>
    @if ($bookings->isNotEmpty())
      <div class="d-flex gap-2 flex-wrap">
        <button class="bghost" data-bs-toggle="modal" data-bs-target="#newRole"><i class="bi bi-shield-plus me-1"></i>New Role</button>
        <button class="bsave" data-bs-toggle="modal" data-bs-target="#newMember"><i class="bi bi-person-plus-fill"></i>Add Team Member</button>
      </div>
    @endif
  </div>

  @if ($bookings->isEmpty())
    <div class="card-rt mt-4" style="padding:24px">
      <p style="font-size:13px;color:var(--muted);margin:0">You need a paid franchise booking before you can build a team.</p>
    </div>
  @else
    <div class="card-rt mb-4 mt-3">
      <div class="card-title">Roles</div>
      @forelse ($roles as $role)
        <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid var(--border)">
          <div>
            <div style="font-size:13px;font-weight:700">{{ $role->name }}</div>
            <div style="font-size:11px;color:var(--muted)">{{ $role->franchiseBooking->city }} &middot; {{ count($role->permissions) }} {{ \Illuminate\Support\Str::plural('permission', count($role->permissions)) }} &middot; {{ $role->team_members_count }} {{ \Illuminate\Support\Str::plural('member', $role->team_members_count) }}</div>
          </div>
          <div class="d-flex gap-2">
            <button class="action-btn" data-bs-toggle="modal" data-bs-target="#editRole{{ $role->id }}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
            <form method="POST" action="{{ route('franchise.team.roles.destroy', $role) }}" onsubmit="return confirm('Delete role {{ $role->name }}?')">
              @csrf @method('DELETE')
              <button type="submit" class="action-btn danger" style="border:none;background:none" title="Delete"><i class="bi bi-trash-fill"></i></button>
            </form>
          </div>
        </div>

        <div class="modal fade" id="editRole{{ $role->id }}" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header"><h5 class="modal-title">Edit Role — {{ $role->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
              <form method="POST" action="{{ route('franchise.team.roles.update', $role) }}">
                @csrf
                <div class="modal-body">
                  @foreach ($availablePermissions as $perm)
                    <div class="form-check mb-2">
                      <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="perm{{ $role->id }}_{{ $loop->index }}" @checked(in_array($perm, $role->permissions))>
                      <label class="form-check-label" for="perm{{ $role->id }}_{{ $loop->index }}" style="font-size:13px">{{ $permissionLabels[$perm] ?? $perm }}</label>
                    </div>
                  @endforeach
                </div>
                <div class="modal-footer">
                  <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="bsave">Save Permissions</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @empty
        <p style="font-size:13px;color:var(--muted);margin:0">No roles created yet.</p>
      @endforelse
    </div>

    <div class="card-rt">
      <div class="card-title">Team Members</div>
      <div class="table-wrap"><table class="table-rt">
        <thead><tr><th>Name</th><th>Email</th><th>Institute</th><th>Role</th><th>Actions</th></tr></thead>
        <tbody>
          @forelse ($members as $member)
            <tr>
              <td>{{ $member->user->name }}</td>
              <td>{{ $member->user->email }}</td>
              <td>{{ $member->franchiseBooking->city }}</td>
              <td>{{ $member->role->name }}</td>
              <td>
                <button class="action-btn" data-bs-toggle="modal" data-bs-target="#editMember{{ $member->id }}" title="Change Role"><i class="bi bi-pencil-fill"></i></button>
                <form method="POST" action="{{ route('franchise.team.members.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->user->name }} from your team?')" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="action-btn danger" style="border:none;background:none" title="Remove"><i class="bi bi-person-dash-fill"></i></button>
                </form>
              </td>
            </tr>

            <div class="modal fade" id="editMember{{ $member->id }}" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header"><h5 class="modal-title">{{ $member->user->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                  <form method="POST" action="{{ route('franchise.team.members.update', $member) }}">
                    @csrf
                    <div class="modal-body">
                      <label class="flbl">Role</label>
                      <select class="fctrl" name="franchise_role_id">
                        @foreach ($roles->where('franchise_booking_id', $member->franchise_booking_id) as $role)
                          <option value="{{ $role->id }}" @selected($member->franchise_role_id === $role->id)>{{ $role->name }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="bsave">Save</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          @empty
            <tr><td colspan="5" style="color:var(--muted)">No team members yet.</td></tr>
          @endforelse
        </tbody>
      </table></div>
    </div>

    <!-- New Role Modal -->
    <div class="modal fade" id="newRole" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">New Role</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('franchise.team.roles.store') }}">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label class="flbl">Institute</label>
                <select class="fctrl" name="franchise_booking_id" required>
                  @foreach ($bookings as $b)
                    <option value="{{ $b->id }}">{{ $b->city }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3"><label class="flbl">Role Name</label><input class="fctrl" name="name" placeholder="e.g. Front Desk" required/></div>
              @foreach ($availablePermissions as $perm)
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="newperm{{ $loop->index }}">
                  <label class="form-check-label" for="newperm{{ $loop->index }}" style="font-size:13px">{{ $permissionLabels[$perm] ?? $perm }}</label>
                </div>
              @endforeach
            </div>
            <div class="modal-footer">
              <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="bsave">Create Role</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- New Member Modal -->
    <div class="modal fade" id="newMember" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title">Add Team Member</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
          <form method="POST" action="{{ route('franchise.team.members.store') }}">
            @csrf
            <div class="modal-body">
              <div class="mb-3">
                <label class="flbl">Institute</label>
                <select class="fctrl" name="franchise_booking_id" id="memberBooking" required>
                  @foreach ($bookings as $b)
                    <option value="{{ $b->id }}">{{ $b->city }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label class="flbl">Role</label>
                <select class="fctrl" name="franchise_role_id" required>
                  @foreach ($roles as $role)
                    <option value="{{ $role->id }}" data-booking="{{ $role->franchise_booking_id }}">{{ $role->name }} ({{ $role->franchiseBooking->city }})</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3"><label class="flbl">Full Name</label><input class="fctrl" name="name" required/></div>
              <div class="mb-3"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" required/></div>
              <div class="row g-3">
                <div class="col-6"><label class="flbl">Password</label><input class="fctrl" type="password" name="password" required/></div>
                <div class="col-6"><label class="flbl">Confirm Password</label><input class="fctrl" type="password" name="password_confirmation" required/></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="bsave">Add Member</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endsection
