@extends('layouts.site')

@section('title', 'Gallery')

@section('content')
  <section class="sec" style="padding-bottom:0">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Moments</div>
        <h2 class="sec-h">Our <em>Gallery</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">A look inside our classrooms, events and franchise centres across India.</p>
      </div>

      @if ($franchises->count())
        <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
          <a href="{{ route('gallery') }}" class="gal-filter {{ !$franchiseBookingId ? 'active' : '' }}">All</a>
          @foreach ($franchises as $f)
            <a href="{{ route('gallery', ['franchise' => $f->id]) }}" class="gal-filter {{ (string) $franchiseBookingId === (string) $f->id ? 'active' : '' }}">{{ $f->city }}</a>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  <section class="sec" style="padding-top:0">
    <div class="container">
      @if ($images->isEmpty())
        <div class="text-center" style="padding:60px 24px">
          <i class="bi bi-images" style="font-size:32px;color:var(--muted);margin-bottom:12px;display:inline-block"></i>
          <p style="font-size:14px;color:var(--muted)">No gallery photos yet. Check back soon!</p>
        </div>
      @else
        <div class="gal-grid">
          @foreach ($images as $img)
            <div class="gal-item rv" data-bs-toggle="modal" data-bs-target="#galLightbox" data-src="{{ $img->imageUrl() }}" data-caption="{{ $img->caption }}">
              <img src="{{ $img->imageUrl() }}" alt="{{ $img->caption ?? 'Gallery photo' }}" loading="lazy">
              @if ($img->franchiseBooking)
                <span class="gal-tag"><i class="bi bi-geo-alt-fill"></i>{{ $img->franchiseBooking->city }}</span>
              @endif
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </section>

  <!-- LIGHTBOX -->
  <div class="modal fade" id="galLightbox" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content" style="background:transparent;border:none">
        <button class="btn-close btn-close-white ms-auto mb-2" data-bs-dismiss="modal" style="opacity:.9"></button>
        <img id="galLightboxImg" src="" alt="" style="width:100%;border-radius:14px">
        <p id="galLightboxCaption" class="text-center text-white mt-3" style="font-size:13px"></p>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    const galLightbox = document.getElementById('galLightbox');
    if (galLightbox) {
      galLightbox.addEventListener('show.bs.modal', e => {
        const item = e.relatedTarget;
        document.getElementById('galLightboxImg').src = item?.dataset.src || '';
        document.getElementById('galLightboxCaption').textContent = item?.dataset.caption || '';
      });
    }
  </script>
@endsection
