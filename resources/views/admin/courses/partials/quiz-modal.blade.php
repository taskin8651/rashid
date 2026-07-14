@php
  $options = $question?->options ?? collect();
  $correctIndex = $options->search(fn ($o) => $o->is_correct);
  $action = $question
    ? route('admin.courses.quiz.update', [$course, $question])
    : route('admin.courses.quiz.store', $course);
@endphp
<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-patch-question-fill me-2"></i>{{ $question ? 'Edit Question' : 'Add Question' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ $action }}">
        @csrf
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12"><label class="flbl">Question</label><textarea class="fctrl" name="question_text" rows="2" required>{{ $question->question_text ?? '' }}</textarea></div>
            <div class="col-md-4"><label class="flbl">Status</label>
              <select class="fctrl" name="status">
                <option value="active" @selected(($question->status ?? 'active') === 'active')>Active</option>
                <option value="inactive" @selected(($question->status ?? '') === 'inactive')>Inactive</option>
              </select>
            </div>
            <div class="col-12">
              <label class="flbl">Options &mdash; select the correct one</label>
              @for ($i = 0; $i < 4; $i++)
                <div class="d-flex align-items-center gap-2 mb-2">
                  <input type="radio" name="correct_index" value="{{ $i }}" required @checked($correctIndex === $i) style="width:18px;height:18px;flex-shrink:0">
                  <input class="fctrl" type="text" name="options[]" value="{{ $options->get($i)?->option_text }}" placeholder="Option {{ $i + 1 }}" required>
                </div>
              @endfor
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="bghost" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="bsave"><i class="bi bi-check2-circle me-1"></i>Save Question</button>
        </div>
      </form>
    </div>
  </div>
</div>
