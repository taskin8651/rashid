  <nav class="navbar" id="topnav">
    <div class="container">
      <div class="nav-inner">
        <a class="brand" href="{{ route('home') }}">
          <div class="brand-logo-wrap"><img src="{{ asset('assets/img/logo.png') }}" alt="R-Tech Computer" class="brand-logo"></div>
        </a>
        <div class="nav-links" id="navLinks">
          <a class="nav-link-item {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
          <a class="nav-link-item {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
          <a class="nav-link-item {{ request()->routeIs('courses') ? 'active' : '' }}" href="{{ route('courses') }}">Courses</a>
          <a class="nav-link-item {{ request()->routeIs('free-demo') ? 'active' : '' }}" href="{{ route('free-demo') }}">Free Demo</a>
          <a class="nav-link-item {{ request()->routeIs('why-rtech') ? 'active' : '' }}" href="{{ route('why-rtech') }}">Why R-Tech</a>
          <a class="nav-link-item {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Gallery</a>
          <a class="nav-link-item {{ request()->routeIs('placements') ? 'active' : '' }}" href="{{ route('placements') }}">Placements</a>
          <a class="nav-link-item {{ request()->routeIs('careers*') ? 'active' : '' }}" href="{{ route('careers') }}">Careers</a>
          <a class="nav-link-item {{ request()->routeIs('franchise') ? 'active' : '' }}" href="{{ route('franchise') }}">Franchise</a>
          <a class="nav-link-item {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
         
        </div>

        <div class="nav-actions">
          @auth
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button class="nav-cta ghost" type="submit"><i class="bi bi-box-arrow-right"></i>Logout</button>
            </form>
          @else
            <button class="nav-cta ghost" type="button" data-bs-toggle="modal" data-bs-target="#lm"><i class="bi bi-person-fill"></i>Login</button>
          @endauth
          <a class="nav-cta" href="{{ route('courses') }}"><i class="bi bi-lightning-fill"></i>Enroll</a>
        </div>

        <!-- Mobile-only: social icons (primary nav lives in the bottom tab bar) -->
        <div class="mobile-header-actions">
          <a href="#" class="mha-social" title="Instagram"><i class="bi bi-instagram"></i></a>
          <a href="https://wa.me/919117744925" class="mha-social" title="WhatsApp" target="_blank"><i class="bi bi-whatsapp"></i></a>
        </div>

        @include('partials.theme-toggle-button')
      </div>
    </div>
  </nav>
