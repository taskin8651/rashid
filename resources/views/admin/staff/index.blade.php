@extends('layouts.admin')

@section('title', 'Staff & Teachers')

@section('content')
  <div class="shead mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div><h4>Staff & Teachers</h4><p>Create logins for staff and teachers so they can submit daily work reports</p></div>
    <button class="bsave" data-bs-toggle="modal" data-bs-target="#newMember"><i class="bi bi-person-plus-fill me-1"></i>Add Account</button>
  </div>

  @if (session('status'))
    <div class="alert alert-success mb-3" style="font-size:13px">{{ session('status') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger mb-3" style="font-size:13px">{{ $errors->first() }}</div>
  @endif

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Reports</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($members as $member)
          <tr>
            <td>{{ $member->name }}</td>
            <td>{{ $member->email }}</td>
            <td style="text-transform:capitalize">{{ $member->roles->pluck('name')->join(', ') }}</td>
            <td>{{ $member->daily_reports_count }}</td>
            <td><span class="badge-rt {{ $member->is_active ? 'bg-active' : 'bg-inactive' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <a class="action-btn" href="{{ route('admin.daily-reports.performance', $member) }}" title="Performance"><i class="bi bi-graph-up"></i></a>
              <button class="action-btn" data-bs-toggle="modal" data-bs-target="#editMember{{ $member->id }}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
              <form method="POST" action="{{ route('admin.staff.destroy', $member) }}" onsubmit="return confirm('Remove {{ $member->name }}\'s access?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none" title="Remove"><i class="bi bi-person-dash-fill"></i></button>
              </form>
            </td>
          </tr>

          <div class="modal fade" id="editMember{{ $member->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit {{ $member->name }}</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.staff.update', $member) }}">
                  @csrf @method('PUT')
                  <div class="modal-body">
                    <div class="mb-3">
                      <label class="flbl">Role</label>
                      <select class="fctrl" name="role">
                        <option value="staff" @selected($member->hasRole('staff'))>Staff</option>
                        <option value="teacher" @selected($member->hasRole('teacher'))>Teacher</option>
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
          <tr><td colspan="6" style="color:var(--muted)">No staff or teacher accounts yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>

  <!-- New Member Modal -->
  <div class="modal fade" id="newMember" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Add Staff / Teacher Account</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.staff.store') }}">
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
                <option value="staff">Staff</option>
                <option value="teacher">Teacher</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="bsave">Add Account</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
