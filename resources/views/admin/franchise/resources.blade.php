@extends('layouts.admin')

@section('title', 'Franchise Resources')

@section('content')
  <div class="shead mb-4"><h4>Training Materials &amp; Marketing Kit</h4><p>Files here are visible to every franchise partner in their dashboard</p></div>

  <div class="card-rt mb-4">
    <form method="POST" action="{{ route('admin.franchise.resources.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-4"><label class="flbl">Title</label><input class="fctrl" name="title" placeholder="e.g. Brand Guidelines" required/></div>
        <div class="col-md-4"><label class="flbl">Description</label><input class="fctrl" name="description" placeholder="Short description"/></div>
        <div class="col-md-4"><label class="flbl">File</label><input class="fctrl" type="file" name="file" required/></div>
        <div class="col-12"><button class="bsave" type="submit"><i class="bi bi-upload me-1"></i>Add Resource</button></div>
      </div>
    </form>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>Title</th><th>Description</th><th>File</th><th>Added</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($resources as $r)
          <tr>
            <td>{{ $r->title }}</td>
            <td>{{ $r->description ?? '—' }}</td>
            <td>{{ $r->original_name }}</td>
            <td>{{ $r->created_at->format('d M Y') }}</td>
            <td>
              <form method="POST" action="{{ route('admin.franchise.resources.destroy', $r) }}" onsubmit="return confirm('Remove this resource?')">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" style="color:var(--muted)">No resources added yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
