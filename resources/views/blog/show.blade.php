@extends('layouts.site')

@section('title', $post->title)

@section('content')
  <section class="sec">
    <div class="container" style="max-width:760px">
      <a href="{{ route('blog') }}" style="color:var(--muted);text-decoration:none;font-size:12.5px;display:inline-block;margin-bottom:18px"><i class="bi bi-arrow-left me-1"></i>Back to Blog</a>

      <div class="rv">
        <div class="sec-lbl">{{ $post->published_at->format('d M Y') }} &middot; {{ $post->author->name ?? 'R-Tech Computer' }}</div>
        <h1 class="sec-h" style="text-align:left;font-size:clamp(24px,4vw,34px)">{{ $post->title }}</h1>
      </div>

      @if ($post->coverUrl())
        <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" style="width:100%;border-radius:16px;margin:24px 0;object-fit:cover;max-height:420px">
      @endif

      <div class="legal-content rv" style="margin:0">
        {!! nl2br(e($post->body)) !!}
      </div>

      @if ($related->count())
        <div class="mt-5 pt-4" style="border-top:1px solid var(--border)">
          <h3 style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:18px;margin-bottom:16px">More Articles</h3>
          <div class="row g-3">
            @foreach ($related as $r)
              <div class="col-md-4">
                <a href="{{ route('blog.show', $r) }}" style="color:var(--text);text-decoration:none;font-size:13px;font-weight:600">{{ $r->title }}</a>
              </div>
            @endforeach
          </div>
        </div>
      @endif
    </div>
  </section>
@endsection
