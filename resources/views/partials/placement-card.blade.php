@php
  $grad = ['var(--grad)', 'linear-gradient(135deg,#9c27b0,#e91e63)', 'linear-gradient(135deg,#009688,#4caf50)', 'linear-gradient(135deg,#f59e0b,#ef4444)'][($index ?? 0) % 4];
@endphp
<div class="tc">
  @if ($p->is_featured)
    <div class="cc-badge bw" style="margin-bottom:12px"><i class="bi bi-star-fill me-1"></i>Featured</div>
  @endif
  @if ($p->testimonial)
    <p>"{{ $p->testimonial }}"</p>
  @else
    <p>Placed as <strong>{{ $p->job_title }}</strong> at <strong>{{ $p->company_name }}</strong>{{ $p->course ? ' after completing ' . $p->course->name : '' }}.</p>
  @endif
  <div class="rr mb-3">
    @if (optional($p->user)->photoUrl())
      <img src="{{ $p->user->photoUrl() }}" alt="{{ $p->user->name }}" class="ra" style="object-fit:cover">
    @else
      <div class="ra" style="background:{{ $grad }}">{{ strtoupper(substr(optional($p->user)->name ?: '?', 0, 1)) }}</div>
    @endif
    <div>
      <div class="rname">{{ optional($p->user)->name ?: 'Student' }}</div>
      <div class="rrole">{{ $p->job_title }} @ {{ $p->company_name }}</div>
    </div>
  </div>
  <div class="d-flex flex-wrap gap-2" style="font-size:11px;color:var(--muted)">
    @if ($p->job_type)<span><i class="bi bi-briefcase-fill me-1"></i>{{ $p->job_type }}</span>@endif
    @if ($p->work_mode)<span><i class="bi bi-laptop me-1"></i>{{ $p->work_mode }}</span>@endif
    @if ($p->location)<span><i class="bi bi-geo-alt-fill me-1"></i>{{ $p->location }}</span>@endif
    @if ($p->package)<span><i class="bi bi-cash-stack me-1"></i>{{ $p->package }}</span>@endif
  </div>
  @if ($p->linkedin_url)
    <a href="{{ $p->linkedin_url }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:6px;font-size:12px;color:var(--orange);text-decoration:none;margin-top:12px"><i class="bi bi-linkedin"></i>View LinkedIn Profile</a>
  @endif
</div>
