@extends('layouts.site')

@section('title', 'Placements')

@section('content')
  <section class="sec" style="padding-bottom:0">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Career Outcomes</div>
        <h2 class="sec-h">Where Our Students <em>Work</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">Real students, real jobs — a look at the companies hiring R-Tech Computer graduates.</p>
        <a href="{{ route('careers') }}" class="btn-g mt-2"><i class="bi bi-send-fill"></i>Browse Open Job Postings</a>
      </div>

      <div class="row g-3 justify-content-center mb-4">
        <div class="col-6 col-md-3 text-center rv">
          <div style="font-size:28px;font-weight:800;color:var(--text)">{{ $stats['placed'] }}+</div>
          <div style="font-size:12px;color:var(--muted)">Students Placed</div>
        </div>
        <div class="col-6 col-md-3 text-center rv">
          <div style="font-size:28px;font-weight:800;color:var(--text)">{{ $stats['companies'] }}+</div>
          <div style="font-size:12px;color:var(--muted)">Hiring Partners</div>
        </div>
      </div>

      @if ($courses->count())
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
          <a href="{{ route('placements') }}" class="gal-filter {{ !$courseId ? 'active' : '' }}">All</a>
          @foreach ($courses as $c)
            <a href="{{ route('placements', ['course' => $c->id]) }}" class="gal-filter {{ (string) $courseId === (string) $c->id ? 'active' : '' }}">{{ $c->name }}</a>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  <section class="sec" style="padding-top:0">
    <div class="container">
      @if ($placements->isEmpty())
        <div class="text-center" style="padding:60px 24px">
          <i class="bi bi-briefcase" style="font-size:32px;color:var(--muted);margin-bottom:12px;display:inline-block"></i>
          <p style="font-size:14px;color:var(--muted)">No placements published yet. Check back soon!</p>
        </div>
      @else
        <div class="row g-4">
          @foreach ($placements as $p)
            <div class="col-md-6 col-lg-4 rv">
              @include('partials.placement-card', ['p' => $p, 'index' => $loop->index])
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </section>
@endsection
