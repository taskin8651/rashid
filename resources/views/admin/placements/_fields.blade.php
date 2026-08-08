<div class="row g-2 mb-1">
  <div class="col-md-6">
    <label class="flbl">Related Course</label>
    <select class="fctrl" name="course_id">
      <option value="">None</option>
      @foreach ($courses as $course)
        <option value="{{ $course->id }}" @selected($p->course_id === $course->id)>{{ $course->name }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6"><label class="flbl">Company Name</label><input class="fctrl" type="text" name="company_name" value="{{ $p->company_name }}" required /></div>
  <div class="col-md-6"><label class="flbl">Job Title</label><input class="fctrl" type="text" name="job_title" value="{{ $p->job_title }}" required /></div>
  <div class="col-md-3">
    <label class="flbl">Job Type</label>
    <select class="fctrl" name="job_type">
      <option value="">Select type</option>
      @foreach (['Full-time', 'Part-time', 'Internship', 'Freelance'] as $type)
        <option value="{{ $type }}" @selected($p->job_type === $type)>{{ $type }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="flbl">Work Mode</label>
    <select class="fctrl" name="work_mode">
      <option value="">Select mode</option>
      @foreach (['Onsite', 'Remote', 'Hybrid'] as $mode)
        <option value="{{ $mode }}" @selected($p->work_mode === $mode)>{{ $mode }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-4"><label class="flbl">Location</label><input class="fctrl" type="text" name="location" value="{{ $p->location }}" /></div>
  <div class="col-md-4"><label class="flbl">Package / Salary</label><input class="fctrl" type="text" name="package" value="{{ $p->package }}" /></div>
  <div class="col-md-4"><label class="flbl">Joining Date</label><input class="fctrl" type="date" name="joining_date" value="{{ optional($p->joining_date)->format('Y-m-d') }}" /></div>
  <div class="col-md-12"><label class="flbl">LinkedIn Profile</label><input class="fctrl" type="url" name="linkedin_url" value="{{ $p->linkedin_url }}" /></div>
  <div class="col-md-12"><label class="flbl">Success Story / Testimonial</label><textarea class="fctrl" name="testimonial" rows="3" maxlength="1000">{{ $p->testimonial }}</textarea></div>
</div>
