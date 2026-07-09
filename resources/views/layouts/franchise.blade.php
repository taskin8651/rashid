<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>@yield('title', 'My Franchise') | R-Tech Computer</title>
<meta name="robots" content="noindex"/>
@include('partials.theme-init')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="{{ asset('student/assets/css/style.css') }}" rel="stylesheet"/>
</head>
<body>

<aside class="sb" id="sb">
  <div class="sb-brand">
    <div class="d-flex align-items-center gap-2">
      <div class="logo-box">RT</div>
      <div><div class="t1">R-Tech Computer</div><div class="t2">Franchise Partner</div></div>
    </div>
  </div>
  <nav class="sb-nav">
    <div class="nsec">Main</div>
    <a class="slink {{ request()->routeIs('franchise.dashboard') ? 'act' : '' }}" href="{{ route('franchise.dashboard') }}"><i class="bi bi-grid-fill"></i>Overview</a>
    <a class="slink {{ request()->routeIs('franchise.documents.*') ? 'act' : '' }}" href="{{ route('franchise.documents.index') }}"><i class="bi bi-file-earmark-text-fill"></i>Documents</a>
    <a class="slink {{ request()->routeIs('franchise.resources.*') ? 'act' : '' }}" href="{{ route('franchise.resources.index') }}"><i class="bi bi-folder2-open"></i>Training &amp; Marketing Kit</a>
    <div class="nsec">My Institute</div>
    <a class="slink {{ request()->routeIs('franchise.courses.*') ? 'act' : '' }}" href="{{ route('franchise.courses.index') }}"><i class="bi bi-collection-play-fill"></i>My Courses</a>
    <a class="slink {{ request()->routeIs('franchise.students.*') ? 'act' : '' }}" href="{{ route('franchise.students.index') }}"><i class="bi bi-people-fill"></i>My Students</a>
    <div class="nsec">Account</div>
    <a class="slink {{ request()->routeIs('franchise.profile.*') ? 'act' : '' }}" href="{{ route('franchise.profile.edit') }}"><i class="bi bi-person-fill"></i>Profile</a>
    <div class="nsec">More</div>
    <a class="slink" href="{{ route('franchise') }}"><i class="bi bi-house-fill"></i>Franchise Home</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="slink" style="background:none;border:none;width:100%;text-align:left"><i class="bi bi-box-arrow-right"></i>Logout</button>
    </form>
  </nav>
  <div class="sb-bot"><div class="uinfo"><div class="uav">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><div class="un">{{ auth()->user()->name }}</div><div class="ur">Franchise Partner</div></div></div></div>
</aside>

<div class="main">
  <div class="topbar">
    <button class="sbtn" onclick="document.getElementById('sb').classList.toggle('open')"><i class="bi bi-list"></i></button>
    <div class="tt">@yield('title', 'Overview')</div>
    <div class="tact">
      <a href="{{ route('home') }}" class="ibtn" title="Home"><i class="bi bi-house-fill"></i></a>
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
@include('partials.theme-toggle-script')
</body>
</html>
