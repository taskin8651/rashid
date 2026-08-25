@extends('layouts.teacher')

@section('title', 'Add Daily Report')

@section('content')
  <div class="card-rt">
    <div class="card-title">Add Today's Report</div>

    <form method="POST" action="{{ route('teacher.reports.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-3">
        <div class="col-md-4"><label class="flbl">Date</label><input type="date" class="fctrl" name="report_date" value="{{ old('report_date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required/></div>
        <div class="col-md-4">
          <label class="flbl">Course (optional)</label>
          <select class="fctrl" name="course_id">
            <option value="">— None —</option>
            @foreach ($courses as $course)
              <option value="{{ $course->id }}" @selected((string) old('course_id') === (string) $course->id)>{{ $course->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4"><label class="flbl">Task / Subject</label><input type="text" class="fctrl" name="task_subject" value="{{ old('task_subject') }}" placeholder="e.g. Class 10 Maths"/></div>
        <div class="col-md-4"><label class="flbl">Hours Worked</label><input type="number" step="0.5" min="0" max="24" class="fctrl" name="hours_worked" value="{{ old('hours_worked') }}"/></div>
        <div class="col-md-4">
          <label class="flbl">Status</label>
          <select class="fctrl" name="status" required>
            <option value="present" @selected(old('status', 'present') === 'present')>Present</option>
            <option value="absent" @selected(old('status') === 'absent')>Absent</option>
            <option value="leave" @selected(old('status') === 'leave')>Leave</option>
          </select>
        </div>
        <div class="col-md-4"><label class="flbl">Attachment (optional)</label><input type="file" class="fctrl" name="attachment"/></div>

        <div class="col-12"><hr class="my-1"/><div class="flbl mb-1" style="font-weight:700">What Was Taught (optional)</div></div>
        <div class="col-md-8"><label class="flbl">Topics Covered</label><textarea class="fctrl" name="topics_covered" rows="2">{{ old('topics_covered') }}</textarea></div>
        <div class="col-md-2"><label class="flbl">Students Present</label><input type="number" min="0" class="fctrl" name="students_present" value="{{ old('students_present') }}"/></div>
        <div class="col-md-2 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="homework_assigned" value="1" id="hw" @checked(old('homework_assigned'))>
            <label class="form-check-label" for="hw" style="font-size:13px">Homework Assigned</label>
          </div>
        </div>

        <div class="col-12"><label class="flbl">Description</label><textarea class="fctrl" name="description" rows="4" required>{{ old('description') }}</textarea></div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="bsave"><i class="bi bi-check-lg me-1"></i>Submit Report</button>
        <a class="bghost" href="{{ route('teacher.reports.index') }}">Cancel</a>
      </div>
    </form>
  </div>
@endsection
