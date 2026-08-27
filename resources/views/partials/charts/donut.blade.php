@php
  $total = collect($segments)->sum('value') ?: 1;
  $cumulative = 0;
  $stops = [];
  foreach ($segments as $seg) {
    if ($seg['value'] <= 0) { continue; }
    $pct = $seg['value'] / $total * 100;
    $stops[] = "rgb({$seg['color']}) {$cumulative}% " . ($cumulative + $pct) . '%';
    $cumulative += $pct;
  }
  $gradient = $stops ? implode(', ', $stops) : 'rgba(var(--text-rgb),.08) 0% 100%';
  $size = $size ?? 140;
@endphp
<div class="d-flex align-items-center gap-4 flex-wrap">
  <div class="donut" style="background:conic-gradient({{ $gradient }});width:{{ $size }}px;height:{{ $size }}px">
    <div class="donut-hole">
      @if (!empty($centerValue))
        <div class="donut-center-val">{{ $centerValue }}</div>
        @if (!empty($centerLabel))<div class="donut-center-lbl">{{ $centerLabel }}</div>@endif
      @endif
    </div>
  </div>
  @unless (!empty($hideLegend))
    <div class="donut-legend">
      @foreach ($segments as $seg)
        <div class="donut-legend-item">
          <span class="donut-dot" style="background:rgb({{ $seg['color'] }})"></span>
          <span>{{ $seg['label'] }}</span>
          <b>{{ $seg['display'] ?? $seg['value'] }}</b>
        </div>
      @endforeach
    </div>
  @endunless
</div>
