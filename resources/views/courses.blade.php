@extends('layouts.site')

@section('title', 'Courses')

@section('content')
  <section class="sec" style="padding-bottom:0">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Browse</div>
        <h2 class="sec-h">Find Your <em>Course</em></h2>
      </div>
      <form method="GET" action="{{ route('courses') }}" class="d-flex flex-wrap justify-content-center gap-2 mb-3">
        <input class="mi" type="text" name="search" value="{{ $search }}" placeholder="Search courses…" style="max-width:280px">
        <select class="mi" name="category" style="max-width:200px">
          <option value="">All Categories</option>
          @foreach ($categories as $cat)
            <option value="{{ $cat->id }}" @selected((string) $categoryId === (string) $cat->id)>{{ $cat->name }}</option>
          @endforeach
        </select>
        <button class="btn-p" type="submit"><i class="bi bi-search"></i>Search</button>
        @if ($search || $categoryId)
          <a href="{{ route('courses') }}" class="btn-g"><i class="bi bi-x-lg"></i>Clear</a>
        @endif
      </form>
      @if ($search || $categoryId)
        <p class="text-center" style="font-size:13px;color:var(--muted)">{{ $courses->count() }} {{ \Illuminate\Support\Str::plural('result', $courses->count()) }} found</p>
      @endif
    </div>
  </section>

  @include('partials.home.courses')
@endsection
