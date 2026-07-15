@extends('layouts.admin')

@section('title', 'FAQs')

@section('content')
  <div class="shead mb-4"><h4>Frequently Asked Questions</h4><p>Shown on the homepage FAQ section, in the order below</p></div>

  <div class="card-rt mb-4">
    <div class="card-title">Add FAQ</div>
    <form method="POST" action="{{ route('admin.faqs.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6"><label class="flbl">Question</label><input class="fctrl" name="question" required/></div>
        <div class="col-md-3"><label class="flbl">Sort Order</label><input class="fctrl" type="number" name="sort_order" value="0"/></div>
        <div class="col-md-3">
          <label class="flbl">Status</label>
          <select class="fctrl" name="status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="col-12"><label class="flbl">Answer</label><textarea class="fctrl" name="answer" rows="2" required></textarea></div>
        <div class="col-12"><button class="bsave" type="submit"><i class="bi bi-plus-lg me-1"></i>Add FAQ</button></div>
      </div>
    </form>
  </div>

  <div class="card-rt">
    <div class="table-wrap"><table class="table-rt">
      <thead><tr><th>#</th><th>Question</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse ($faqs as $faq)
          <tr>
            <td>{{ $faq->sort_order }}</td>
            <td style="max-width:420px">{{ $faq->question }}</td>
            <td><span class="badge-rt {{ $faq->status === 'active' ? 'bg-active' : 'bg-inactive' }}">{{ ucfirst($faq->status) }}</span></td>
            <td>
              <button class="action-btn" data-bs-toggle="modal" data-bs-target="#editFaq{{ $faq->id }}" title="Edit"><i class="bi bi-pencil-fill"></i></button>
              <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Delete this FAQ?')" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="action-btn danger" style="border:none;background:none"><i class="bi bi-trash-fill"></i></button>
              </form>
            </td>
          </tr>

          <div class="modal fade" id="editFaq{{ $faq->id }}" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Edit FAQ</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
                  @csrf
                  <div class="modal-body">
                    <div class="mb-3"><label class="flbl">Question</label><input class="fctrl" name="question" value="{{ $faq->question }}" required/></div>
                    <div class="mb-3"><label class="flbl">Answer</label><textarea class="fctrl" name="answer" rows="3" required>{{ $faq->answer }}</textarea></div>
                    <div class="row g-3">
                      <div class="col-6"><label class="flbl">Sort Order</label><input class="fctrl" type="number" name="sort_order" value="{{ $faq->sort_order }}"/></div>
                      <div class="col-6">
                        <label class="flbl">Status</label>
                        <select class="fctrl" name="status">
                          <option value="active" @selected($faq->status === 'active')>Active</option>
                          <option value="inactive" @selected($faq->status === 'inactive')>Inactive</option>
                        </select>
                      </div>
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
          <tr><td colspan="4" style="color:var(--muted)">No FAQs added yet.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div>
@endsection
