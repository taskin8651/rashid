@extends('layouts.admin')

@section('title', 'Student Management')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0"><h4>Student Management</h4><p>{{ $students->total() }} registered students</p></div>
    <form method="GET" action="{{ route('admin.students.index') }}" class="d-flex gap-2">
      <input class="fctrl" type="text" name="search" value="{{ $search }}" placeholder="Search students…" style="width:220px"/>
      <button class="bsave" type="submit">Search</button>
    </form>
  </div>
  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Student</th><th>Email</th><th>Course</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($students as $s)
          <tr>
            <td><a href="{{ route('admin.students.show', $s) }}" style="color:var(--text);text-decoration:none;font-weight:600">{{ $s->name }}</a></td>
            <td>{{ $s->email }}</td>
            <td>{{ optional($s->enrollments->first())->course->name ?? '—' }}</td>
            <td>{{ $s->created_at->format('d M Y') }}</td>
            <td><span class="badge-rt {{ $s->is_active ? 'bg-active' : 'bg-inactive' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td>
              <a href="{{ route('admin.students.show', $s) }}" class="action-btn" title="View"><i class="bi bi-eye-fill"></i></a>
              <form method="POST" action="{{ route('admin.students.destroy', $s) }}" onsubmit="return confirm('Delete this student? This cannot be undone.')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" style="color:var(--muted)">No students found.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
  <div class="mt-3">{{ $students->links() }}</div>
@endsection
