@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div class="shead mb-0"><h4>Category Management</h4><p>Organize courses into categories</p></div>
  </div>

  <details class="card-rt mb-4">
    <summary class="bsave" style="display:inline-block;cursor:pointer;list-style:none"><i class="bi bi-plus-circle-fill me-1"></i>Add Category</summary>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4">
      @csrf
      <div class="row g-3">
        <div class="col-md-4"><label class="flbl">Name</label><input class="fctrl" name="name" placeholder="e.g. Web Development" required/></div>
        <div class="col-md-3"><label class="flbl">Icon (emoji)</label><input class="fctrl" name="icon" placeholder="💻"/></div>
        <div class="col-md-3"><label class="flbl">Status</label><select class="fctrl" name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
        <div class="col-md-2 d-flex align-items-end"><button class="bsave w-100" type="submit">Save</button></div>
      </div>
    </form>
  </details>

  <div class="row g-3">
    @foreach ($categories as $c)
      <div class="col-md-6 col-lg-3">
        <div class="card-rt">
          <div style="font-size:28px;margin-bottom:8px">{{ $c->icon ?: '📁' }}</div>
          <h6 style="font-size:14px;font-weight:700;margin-bottom:4px">{{ $c->name }}</h6>
          <p style="font-size:12px;color:var(--muted);margin-bottom:10px">{{ $c->courses_count }} course(s)</p>
          <div class="d-flex gap-2 align-items-center">
            <span class="badge-rt {{ $c->status === 'active' ? 'bg-active' : 'bg-inactive' }} ms-auto">{{ $c->status }}</span>
            <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Delete this category?')">
              @csrf @method('DELETE')
              <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
