@php
  $action = $assignment
    ? route('franchise.courses.assignments.update', [$course, $assignment])
    : route('franchise.courses.assignments.store', $course);
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-journal-text me-2"></i>{{ $assignment ? 'Edit Assignment' : 'Add Assignment' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ $action }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12"><label class="flbl">Title</label><input class="fctrl" type="text" name="title" value="{{ $assignment->title ?? '' }}" required></div>
            <div class="col-12"><label class="flbl">Description</label><textarea class="fctrl" name="description" rows="3">{{ $assignment->description ?? '' }}</textarea></div>
            <div class="col-6"><label class="flbl">Due Date</label><input class="fctrl" type="date" name="due_date" value="{{ optional($assignment?->due_date)->format('Y-m-d') }}"></div>
            <div class="col-6"><label class="flbl">Max Score</label><input class="fctrl" type="number" name="max_score" min="1" value="{{ $assignment->max_score ?? 100 }}" required></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="bsave"><i class="bi bi-check2-circle me-1"></i>Save Assignment</button>
        </div>
      </form>
    </div>
  </div>
</div>
