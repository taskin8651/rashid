@extends('layouts.site')

@section('title', 'Blog')

@section('content')
  <section class="sec" style="padding-bottom:0">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Insights</div>
        <h2 class="sec-h">From Our <em>Blog</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">Career tips, industry trends and updates from R-Tech Computer.</p>
      </div>
    </div>
  </section>

  <section class="sec" style="padding-top:0">
    <div class="container">
      @if ($posts->isEmpty())
        <div class="text-center" style="padding:60px 24px">
          <i class="bi bi-file-earmark-richtext" style="font-size:32px;color:var(--muted);margin-bottom:12px;display:inline-block"></i>
          <p style="font-size:14px;color:var(--muted)">No articles published yet. Check back soon!</p>
        </div>
      @else
        <div class="row g-4">
          @foreach ($posts as $post)
            <div class="col-md-6 col-lg-4 rv">
              <a href="{{ route('blog.show', $post) }}" class="blog-card">
                @if ($post->coverUrl())
                  <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}">
                @else
                  <div class="blog-card-fallback"><i class="bi bi-file-earmark-richtext"></i></div>
                @endif
                <div class="blog-card-body">
                  <span class="blog-card-date">{{ $post->published_at->format('d M Y') }}</span>
                  <h3>{{ $post->title }}</h3>
                  <p>{{ $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->body), 110) }}</p>
                </div>
              </a>
            </div>
          @endforeach
        </div>
        <div class="mt-4">{{ $posts->links() }}</div>
      @endif
    </div>
  </section>
@endsection
