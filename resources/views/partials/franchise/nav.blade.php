  <nav class="navbar" id="topnav">
    <div class="container">
      <div class="nav-inner">
        <a class="brand" href="{{ route('home') }}">
          <div class="brand-logo-wrap"><img src="{{ asset('assets/img/logo.png') }}" alt="R-Tech Computer" class="brand-logo"></div>
        </a>
        <div class="nav-links" id="navLinks">
          <a class="nav-link-item" href="{{ route('home') }}">Home</a>
          <a class="nav-link-item" href="{{ route('courses') }}">Courses</a>
          <a class="nav-link-item" href="#why">Why Partner</a>
          <a class="nav-link-item" href="#tiers">Investment</a>
          <a class="nav-link-item" href="#faq">FAQs</a>
          <a class="nav-link-item active" href="{{ route('franchise') }}#apply">Apply</a>
          @auth
            @if (auth()->user()->hasRole('franchisee'))
              <a class="nav-link-item" href="{{ route('franchise.dashboard') }}">My Franchise</a>
            @endif
          @endauth
        </div>

        <div class="nav-actions">
          @auth
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button class="nav-cta ghost" type="submit"><i class="bi bi-box-arrow-right"></i>Logout</button>
            </form>
          @else
            <button class="nav-cta ghost" type="button" data-bs-toggle="modal" data-bs-target="#flm"><i class="bi bi-person-fill"></i>Login</button>
          @endauth
          <button class="nav-cta" type="button" data-bs-toggle="modal" data-bs-target="#fbm"><i class="bi bi-flag-fill"></i>Reserve Your City</button>
        </div>

        <!-- Mobile-only: social icons (primary nav lives in the bottom tab bar) -->
        <div class="mobile-header-actions ">
          <a href="#" class="mha-social" title="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="https://wa.me/919117744925" class="mha-social" title="WhatsApp" target="_blank"><i class="bi bi-whatsapp"></i></a>
        </div>

        @include('partials.theme-toggle-button')
      </div>
    </div>
  </nav>
