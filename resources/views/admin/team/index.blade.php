@extends('layouts.admin')

@section('title', 'Team & Roles')

@php
  $permissionLabels = [
    'view-admin-dashboard' => 'Dashboard',
    'manage-leads' => 'Leads (full — create, edit, convert, delete)',
    'follow-up-leads' => 'Leads (follow-up only — notes & status, no edit/convert/delete)',
    'manage-students' => 'Students',
    'manage-courses' => 'Courses (videos, quiz, assignments, notes)',
    'manage-categories' => 'Categories',
    'manage-coupons' => 'Coupons',
    'manage-payments' => 'Payments & Refunds',
    'manage-franchise-leads' => 'Franchise Leads & Bookings',
    'manage-franchise-resources' => 'Franchise Resources',
    'manage-gallery' => 'Gallery',
    'manage-reviews' => 'Reviews',
    'manage-certificate-applications' => 'Certificate Applications',
    'manage-attendance' => 'Attendance Records',
    'manage-attendance-locations' => 'Attendance Locations',
    'manage-faqs' => 'FAQs',
    'manage-blog' => 'Blog',
    'manage-team' => 'Team & Roles',
  ];
@endphp

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Team &amp; Roles</h4><p>Create custom admin roles and invite your team with scoped access</p></div>
    <div class="d-flex gap-2 flex-wrap">
      <button class="bghost" data-bs-toggle="modal" data-bs-target="#newRole"><i class="bi bi-shield-plus me-1"></i>New Role</button>
      <button class="bsave" data-bs-toggle="modal" data-bs-target="#newMember"><i class="bi bi-person-plus-fill me-1"></i>Add Team Member</button>
    </div>
  </div>

  <div class="card-rt mb-4">
    <div class="card-title">Roles</div>
    <div class="row g-3">
      @foreach ($roles as $role)
        <div class="col-md-6 col-lg-4">
          <div style="border:1px solid var(--border);border-radius:12px;padding:16px">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 style="font-size:14px;font-weight:700;margin:0;text-transform:capitalize">{{ $role->name }}</h6>
              @if ($role->name === 'admin')
                <span class="badge-rt bg-active">Full Access</span>
              @else
                <span class="badge-rt bg-pending">{{ $role->users_count }} {{ \Illuminate\Support\Str::plural('member', $role->users_count) }}</span>
              @endif
            </div>
            <p style="font-size:11.5px;color:var(--muted);margin-bottom:12px">
              {{ $role->name === 'admin' ? 'Every permission, always. Cannot be edited or removed.' : $role->permissions->count() . ' ' . \Illuminate\Support\Str::plural('permission', $role->permissions->count()) . ' granted' }}
            </p>
            @if ($role->name !== 'admin')
              <div class="d-flex gap-2">
                <button class="bghost flex-grow-1" style="font-size:12px;padding:7px" data-bs-toggle="modal" data-bs-target="#editRole{{ $role->id }}"><i class="bi bi-pencil-fill me-1"></i>Edit</button>
                <form method="POST" action="{{ route('admin.team.roles.destroy', $role) }}" onsubmit="return confirm('Delete role {{ $role->name }}?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="bghost" style="font-size:12px;padding:7px;color:var(--danger);border-color:rgba(239,68,68,.3)"><i class="bi bi-trash-fill"></i></button>
                </form>
              </div>
            @endif
          </div>
        </div>

        @if ($role->name !== 'admin')
          <div class="modal fade" id="editRole{{ $role->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit Role — {{ $role->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.team.roles.update', $role) }}">
                  @csrf
                  <div class="modal-body">
                    <label class="flbl mb-2">Permissions</label>
                    <div class="row g-2">
                      @foreach ($availablePermissions as $perm)
                        <div class="col-6 form-check">
                          <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="perm{{ $role->id }}_{{ $loop->index }}" @checked($role->permissions->pluck('name')->contains($perm))>
                          <label class="form-check-label" for="perm{{ $role->id }}_{{ $loop->index }}" style="font-size:12.5px">{{ $permissionLabels[$perm] ?? $perm }}</label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="bsave">Save Permissions</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endif
      @endforeach
    </div>
  </div>

  <div class="card-rt">
    <div class="card-title">Team Members</div>
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($members as $member)
          <tr>
            <td>{{ $member->name }}</td>
            <td>{{ $member->email }}</td>
            <td style="text-transform:capitalize">{{ $member->roles->pluck('name')->join(', ') }}</td>
            <td><span class="badge-rt {{ $member->is_active ? 'bg-active' : 'bg-inactive' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <button class="action-btn" data-bs-toggle="modal" data-bs-target="#editMember{{ $member->id }}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
              <form method="POST" action="{{ route('admin.team.members.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->name }} from the admin panel?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Remove"><i class="bi bi-person-dash-fill"></i></button>
              </form>
            </td>
          </tr>

          <div class="modal fade" id="editMember{{ $member->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit {{ $member->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.team.members.update', $member) }}">
                  @csrf
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="flbl">Role</label>
                      <select class="fctrl" name="role">
                        @foreach ($roles as $role)
                          <option value="{{ $role->name }}" @selected($member->hasRole($role->name))>{{ ucfirst($role->name) }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_active" value="1" id="active{{ $member->id }}" @checked($member->is_active)>
                      <label class="form-check-label" for="active{{ $member->id }}" style="font-size:13px">Active (can log in)</label>
                    </div>
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
        <form method="POST" action="{{ route('admin.team.roles.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3"><label class="flbl">Role Name</label><input class="fctrl" name="name" placeholder="e.g. Content Manager" required/></div>
            <label class="flbl mb-2">Permissions</label>
            <div class="row g-2">
              @foreach ($availablePermissions as $perm)
                <div class="col-6 form-check">
                  <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="newperm{{ $loop->index }}">
                  <label class="form-check-label" for="newperm{{ $loop->index }}" style="font-size:12.5px">{{ $permissionLabels[$perm] ?? $perm }}</label>
                </div>
              @endforeach
            </div>
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
        <form method="POST" action="{{ route('admin.team.members.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3"><label class="flbl">Full Name</label><input class="fctrl" name="name" required/></div>
            <div class="mb-3"><label class="flbl">Email</label><input class="fctrl" type="email" name="email" required/></div>
            <div class="row g-3 mb-3">
              <div class="col-6"><label class="flbl">Password</label><input class="fctrl" type="password" name="password" required/></div>
              <div class="col-6"><label class="flbl">Confirm Password</label><input class="fctrl" type="password" name="password_confirmation" required/></div>
            </div>
            <div>
              <label class="flbl">Role</label>
              <select class="fctrl" name="role" required>
                @foreach ($roles as $role)
                  <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
              </select>
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
@endsection
