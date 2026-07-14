<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>@yield('title', 'Admin Panel') | R-Tech Computer</title>
<meta name="robots" content="noindex"/>
@include('partials.theme-init')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="{{ asset('admin-assets/assets/css/style.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="app-wrap">
<aside class="sb" id="sb">
  <div class="sb-brand"><div class="d-flex align-items-center gap-2"><div class="logo-box">RT</div><div><div class="t1">R-Tech Computer</div><div class="t2">Admin Panel</div></div></div></div>
  <nav class="sb-nav">
    <div class="nsec">Overview</div>
    <a class="slink {{ request()->routeIs('admin.dashboard') ? 'act' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a>
    <div class="nsec">Management</div>
    <a class="slink {{ request()->routeIs('admin.students.*') ? 'act' : '' }}" href="{{ route('admin.students.index') }}"><i class="bi bi-people-fill"></i>Students</a>
    <a class="slink {{ request()->routeIs('admin.courses.*') ? 'act' : '' }}" href="{{ route('admin.courses.index') }}"><i class="bi bi-collection-play-fill"></i>Courses</a>
    <a class="slink {{ request()->routeIs('admin.categories.*') ? 'act' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags-fill"></i>Categories</a>
    <div class="nsec">Commerce</div>
    <a class="slink {{ request()->routeIs('admin.coupons.*') ? 'act' : '' }}" href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated-fill"></i>Coupons</a>
    <a class="slink {{ request()->routeIs('admin.payments.*') ? 'act' : '' }}" href="{{ route('admin.payments.index') }}"><i class="bi bi-credit-card-fill"></i>Payments</a>
    <a class="slink {{ request()->routeIs('admin.franchise.index') ? 'act' : '' }}" href="{{ route('admin.franchise.index') }}"><i class="bi bi-flag-fill"></i>Franchise Leads</a>
    <a class="slink {{ request()->routeIs('admin.franchise.resources.*') ? 'act' : '' }}" href="{{ route('admin.franchise.resources.index') }}"><i class="bi bi-folder2-open"></i>Franchise Resources</a>
    <div class="nsec">More</div>
    <a class="slink" href="{{ route('home') }}"><i class="bi bi-house-fill"></i>Back to Website</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="slink" style="background:none;border:none;width:100%;text-align:left"><i class="bi bi-box-arrow-right"></i>Logout</button>
    </form>
  </nav>
  <div class="sb-bot"><div class="uinfo"><div class="uav">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><div class="un">{{ auth()->user()->name }}</div><div class="ur">Super Admin</div></div></div></div>
</aside>

<div class="main">
  <div class="topbar">
    <button class="sbtn" onclick="document.getElementById('sb').classList.toggle('open')"><i class="bi bi-list"></i></button>
    <div class="tt">@yield('title', 'Dashboard')</div>
    <div class="tact">
      <a href="{{ route('home') }}" class="ibtn" title="View Site"><i class="bi bi-box-arrow-up-right"></i></a>
      @include('partials.theme-toggle-button')
    </div>
  </div>

  <div class="pbody">
    @if (session('status'))
      <div class="alert alert-success mb-4">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger mb-4">{{ $errors->first() }}</div>
    @endif

    @yield('content')
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@include('partials.theme-toggle-script')
</body>
</html>
