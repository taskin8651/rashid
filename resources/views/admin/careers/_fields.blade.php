@php $job = $job ?? null; @endphp
<div class="row g-2 mb-1">
  <div class="col-md-6"><label class="flbl">Job Title</label><input class="fctrl" type="text" name="title" value="{{ $job->title ?? '' }}" required placeholder="e.g. Junior Web Developer" /></div>
  <div class="col-md-6"><label class="flbl">Company Name</label><input class="fctrl" type="text" name="company_name" value="{{ $job->company_name ?? 'R-Tech Computer' }}" required /></div>
  <div class="col-md-6">
    <label class="flbl">Related Course</label>
    <select class="fctrl" name="course_id">
      <option value="">None</option>
      @foreach ($courses as $course)
        <option value="{{ $course->id }}" @selected(($job->course_id ?? null) === $course->id)>{{ $course->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="flbl">Job Type</label>
    <select class="fctrl" name="job_type">
      <option value="">Select type</option>
      @foreach (['Full-time', 'Part-time', 'Internship', 'Freelance'] as $type)
        <option value="{{ $type }}" @selected(($job->job_type ?? null) === $type)>{{ $type }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="flbl">Work Mode</label>
    <select class="fctrl" name="work_mode">
      <option value="">Select mode</option>
      @foreach (['Onsite', 'Remote', 'Hybrid'] as $mode)
        <option value="{{ $mode }}" @selected(($job->work_mode ?? null) === $mode)>{{ $mode }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4"><label class="flbl">Location</label><input class="fctrl" type="text" name="location" value="{{ $job->location ?? '' }}" placeholder="e.g. Patna" /></div>
  <div class="col-md-4"><label class="flbl">Package / Salary</label><input class="fctrl" type="text" name="package" value="{{ $job->package ?? '' }}" placeholder="e.g. 2.5 - 4 LPA" /></div>
  <div class="col-md-2"><label class="flbl">Vacancies</label><input class="fctrl" type="number" min="1" name="vacancies" value="{{ $job->vacancies ?? '' }}" /></div>
  <div class="col-md-2"><label class="flbl">Apply By</label><input class="fctrl" type="date" name="apply_by" value="{{ optional($job->apply_by ?? null)->format('Y-m-d') }}" /></div>
  <div class="col-md-12"><label class="flbl">Job Description</label><textarea class="fctrl" name="description" rows="4" required>{{ $job->description ?? '' }}</textarea></div>
  <div class="col-md-12"><label class="flbl">Requirements / Skills (optional)</label><textarea class="fctrl" name="requirements" rows="3" placeholder="One per line, e.g.&#10;Basic HTML/CSS knowledge&#10;Good communication skills">{{ $job->requirements ?? '' }}</textarea></div>
</div>
