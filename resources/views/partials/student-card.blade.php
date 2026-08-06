@php
  $enrollment = $student->featuredEnrollment();
  $grad = ['var(--grad)', 'linear-gradient(135deg,#9c27b0,#e91e63)', 'linear-gradient(135deg,#009688,#4caf50)', 'linear-gradient(135deg,#f59e0b,#ef4444)'][($index ?? 0) % 4];
@endphp
<div class="tc p-0" style="overflow:hidden">
  <div style="width:100%;aspect-ratio:1/1;position:relative">
    @if ($student->photoUrl())
      <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}" style="width:100%;height:100%;object-fit:cover;display:block">
    @else
      <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:42px;font-weight:800;color:#fff;background:{{ $grad }}">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
    @endif
  </div>
  <div style="padding:16px">
    <div class="rname" style="font-size:15px;margin-bottom:6px">{{ $student->name }}</div>
    @if ($enrollment?->course)
      <div class="cc-badge bw" style="margin-bottom:0">{{ $enrollment->course->name }}</div>
    @endif
    @if ($enrollment?->status === 'completed')
      <div style="font-size:11px;color:var(--ok);margin-top:8px;font-weight:700"><i class="bi bi-patch-check-fill me-1"></i>Course Completed</div>
    @endif
  </div>
</div>
