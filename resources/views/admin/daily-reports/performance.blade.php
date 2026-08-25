@extends('layouts.admin')

@section('title', 'Performance — ' . $member->name)

@section('content')
  <div class="shead mb-4">
    <h4>{{ $member->name }} — Performance</h4>
    <p style="text-transform:capitalize">{{ $member->getRoleNames()->first() }} &middot; <a href="{{ route('admin.daily-reports.index', ['user' => $member->id]) }}" style="color:var(--muted)">View all their reports</a></p>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-2"><div class="card-rt"><div class="card-title">Reports</div><h3>{{ $stats['total'] }}</h3></div></div>
    <div class="col-6 col-md-2"><div class="card-rt"><div class="card-title">Approved</div><h3>{{ $stats['approved'] }}</h3></div></div>
    <div class="col-6 col-md-2"><div class="card-rt"><div class="card-title">Pending</div><h3>{{ $stats['pending'] }}</h3></div></div>
    <div class="col-6 col-md-2"><div class="card-rt"><div class="card-title">Rejected</div><h3>{{ $stats['rejected'] }}</h3></div></div>
    <div class="col-6 col-md-2"><div class="card-rt"><div class="card-title">Total Hours</div><h3>{{ $stats['total_hours'] }}</h3></div></div>
    <div class="col-6 col-md-2"><div class="card-rt"><div class="card-title">Days Missed</div><h3>{{ $stats['days_without_report'] }}</h3></div></div>
  </div>

  <div class="card-rt">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <a class="bghost" href="{{ route('admin.daily-reports.performance', ['member' => $member, 'month' => $monthStart->copy()->subMonth()->format('Y-m')]) }}"><i class="bi bi-chevron-left"></i></a>
      <div class="card-title" style="margin:0">{{ $monthStart->format('F Y') }}</div>
      <a class="bghost" href="{{ route('admin.daily-reports.performance', ['member' => $member, 'month' => $monthStart->copy()->addMonth()->format('Y-m')]) }}"><i class="bi bi-chevron-right"></i></a>
    </div>

    <div class="cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px">
      @foreach (['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dow)
        <div style="font-size:11px;color:var(--muted);text-align:center;font-weight:700">{{ $dow }}</div>
      @endforeach

      @for ($i = 0; $i < $monthStart->dayOfWeek; $i++)
        <div></div>
      @endfor

      @foreach ($days as $d)
        @php
          $report = $d['report'];
          $isFuture = $d['date']->isFuture();
          $cellStyle = 'border-radius:8px;padding:6px 4px;min-height:52px;font-size:11px;text-align:center';
          if ($report) {
            $cellStyle .= $report->review_status === 'approved'
              ? ';background:rgba(40,180,90,.14);border:1px solid rgba(125,220,160,.3)'
              : ($report->review_status === 'rejected'
                ? ';background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.3)'
                : ';background:rgba(255,165,0,.14);border:1px solid rgba(255,184,77,.3)');
          } elseif (!$isFuture) {
            $cellStyle .= ';background:rgba(var(--text-rgb),.05);border:1px solid var(--border)';
          } else {
            $cellStyle .= ';border:1px solid transparent';
          }
        @endphp
        <div style="{{ $cellStyle }}" title="{{ $report ? ($report->task_subject ?? $report->description) : ($isFuture ? '' : 'No report submitted') }}">
          <div style="font-weight:700">{{ $d['date']->day }}</div>
          @if ($report)
            <div>{{ $report->hours_worked ?? '' }}{{ $report->hours_worked ? 'h' : '' }}</div>
          @elseif (!$isFuture)
            <div style="color:var(--muted)">—</div>
          @endif
        </div>
      @endforeach
    </div>

    <div class="d-flex gap-3 mt-3" style="font-size:11px;color:var(--muted)">
      <span><span class="badge-rt bg-active">&nbsp;</span> Approved</span>
      <span><span class="badge-rt bg-pending">&nbsp;</span> Pending</span>
      <span><span class="badge-rt" style="background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.3)">&nbsp;</span> Rejected</span>
      <span><span class="badge-rt bg-inactive">&nbsp;</span> No report</span>
    </div>
  </div>
@endsection
