@php
  $action = $note
    ? route('franchise.courses.notes.update', [$course, $note])
    : route('franchise.courses.notes.store', $course);
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-file-earmark-pdf-fill me-2"></i>{{ $note ? 'Edit Note' : 'Upload Note' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-8"><label class="flbl">Title</label><input class="fctrl" type="text" name="title" value="{{ $note->title ?? '' }}" required></div>
            <div class="col-4"><label class="flbl">Pages</label><input class="fctrl" type="number" name="page_count" min="0" value="{{ $note->page_count ?? '' }}"></div>
            <div class="col-12"><label class="flbl">PDF File{{ $note ? ' (leave empty to keep current)' : '' }}</label><input class="fctrl" type="file" name="file" accept="application/pdf" {{ $note ? '' : 'required' }}></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="bsave"><i class="bi bi-check2-circle me-1"></i>Save Note</button>
        </div>
      </form>
    </div>
  </div>
</div>
