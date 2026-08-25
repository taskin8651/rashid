@extends('layouts.staff')

@section('title', 'Edit Daily Report')

@section('content')
  <div class="card-rt">
    <div class="card-title">Edit Report — {{ $dailyReport->report_date->format('d M Y') }}</div>

    <form method="POST" action="{{ route('staff.reports.update', $dailyReport) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="row g-3">
        <div class="col-md-4"><label class="flbl">Date</label><input type="date" class="fctrl" name="report_date" value="{{ old('report_date', $dailyReport->report_date->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required/></div>
        <div class="col-md-4">
          <label class="flbl">Course (optional)</label>
          <select class="fctrl" name="course_id">
            <option value="">— None —</option>
            @foreach ($courses as $course)
              <option value="{{ $course->id }}" @selected((string) old('course_id', $dailyReport->course_id) === (string) $course->id)>{{ $course->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4"><label class="flbl">Task / Subject</label><input type="text" class="fctrl" name="task_subject" value="{{ old('task_subject', $dailyReport->task_subject) }}"/></div>
        <div class="col-md-4"><label class="flbl">Hours Worked</label><input type="number" step="0.5" min="0" max="24" class="fctrl" name="hours_worked" value="{{ old('hours_worked', $dailyReport->hours_worked) }}"/></div>
        <div class="col-md-4">
          <label class="flbl">Status</label>
          <select class="fctrl" name="status" required>
            <option value="present" @selected(old('status', $dailyReport->status) === 'present')>Present</option>
            <option value="absent" @selected(old('status', $dailyReport->status) === 'absent')>Absent</option>
            <option value="leave" @selected(old('status', $dailyReport->status) === 'leave')>Leave</option>
          </select>
        </div>
        <div class="col-md-8">
          <label class="flbl">Attachment (optional)</label><input type="file" class="fctrl" name="attachment"/>
          @if ($dailyReport->attachment_original_name)
            <span style="font-size:11px;color:var(--muted)">Current: {{ $dailyReport->attachment_original_name }} — upload a new file to replace it</span>
          @endif
        </div>

        <div class="col-12"><hr class="my-1"/><div class="flbl mb-1" style="font-weight:700">Today's Work Numbers (optional)</div></div>
        <div class="col-md-4"><label class="flbl">Leads Followed Up</label><input type="number" min="0" class="fctrl" name="leads_followed_up" value="{{ old('leads_followed_up', $dailyReport->leads_followed_up) }}"/></div>
        <div class="col-md-4"><label class="flbl">Students Enrolled</label><input type="number" min="0" class="fctrl" name="students_enrolled" value="{{ old('students_enrolled', $dailyReport->students_enrolled) }}"/></div>
        <div class="col-md-4"><label class="flbl">Calls Made</label><input type="number" min="0" class="fctrl" name="calls_made" value="{{ old('calls_made', $dailyReport->calls_made) }}"/></div>

        <div class="col-12"><label class="flbl">Description</label><textarea class="fctrl" name="description" rows="4" required>{{ old('description', $dailyReport->description) }}</textarea></div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" class="bsave"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
        <a class="bghost" href="{{ route('staff.reports.index') }}">Cancel</a>
      </div>
    </form>
  </div>
@endsection
