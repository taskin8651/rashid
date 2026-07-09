@php
  $loginModalTarget = request()->routeIs('franchise') ? '#flm' : '#lm';
@endphp
<nav class="mobile-bottom-nav">
  <a class="mbn-item {{ request()->routeIs('home') ? 'act' : '' }}" href="{{ route('home') }}">
    <i class="bi bi-house-fill"></i>Home
  </a>
  <a class="mbn-item {{ request()->routeIs('courses') ? 'act' : '' }}" href="{{ route('courses') }}">
    <i class="bi bi-collection-play-fill"></i>Courses
  </a>
  <a class="mbn-item {{ request()->routeIs('free-demo') ? 'act' : '' }}" href="{{ route('free-demo') }}">
    <i class="bi bi-play-circle-fill"></i>Demo
  </a>
  <a class="mbn-item {{ request()->routeIs('franchise') ? 'act' : '' }}" href="{{ route('franchise') }}">
    <i class="bi bi-flag-fill"></i>Franchise
  </a>
  @auth
    @php
      $dashRoute = auth()->user()->hasRole('admin')
        ? route('admin.dashboard')
        : (auth()->user()->hasRole('franchisee') ? route('franchise.dashboard') : route('student.dashboard'));
    @endphp
    <a class="mbn-item" href="{{ $dashRoute }}">
      <i class="bi bi-person-circle"></i>Account
    </a>
  @else
    <button class="mbn-item" type="button" data-bs-toggle="modal" data-bs-target="{{ $loginModalTarget }}">
      <i class="bi bi-person-circle"></i>Account
    </button>
  @endauth
</nav>
