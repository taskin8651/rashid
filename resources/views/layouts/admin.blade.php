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
    @canany(['manage-leads', 'follow-up-leads', 'manage-students', 'manage-certificate-applications', 'manage-placements', 'manage-attendance', 'manage-attendance-locations', 'manage-courses', 'manage-categories', 'manage-admins'])
      <div class="nsec">Management</div>
      @canany(['manage-leads', 'follow-up-leads'])
        <a class="slink {{ request()->routeIs('admin.leads.*') ? 'act' : '' }}" href="{{ route('admin.leads.index') }}"><i class="bi bi-person-lines-fill"></i>Leads</a>
      @endcanany
      @can('manage-students')
        <a class="slink {{ request()->routeIs('admin.students.*') ? 'act' : '' }}" href="{{ route('admin.students.index') }}"><i class="bi bi-people-fill"></i>Students</a>
      @endcan
      @canany(['manage-certificate-applications', 'manage-admins'])
        <a class="slink {{ request()->routeIs('admin.certificates.*') ? 'act' : '' }}" href="{{ route('admin.certificates.index') }}"><i class="bi bi-award-fill"></i>Certificates</a>
        <a class="slink {{ request()->routeIs('admin.certificate-applications.*') ? 'act' : '' }}" href="{{ route('admin.certificate-applications.index') }}"><i class="bi bi-patch-question-fill"></i>Certificate Applications</a>
      @endcanany
      @can('manage-placements')
        <a class="slink {{ request()->routeIs('admin.placements.*') ? 'act' : '' }}" href="{{ route('admin.placements.index') }}"><i class="bi bi-briefcase-fill"></i>Placements</a>
      @endcan
      @can('manage-attendance')
        <a class="slink {{ request()->routeIs('admin.attendance.*') ? 'act' : '' }}" href="{{ route('admin.attendance.index') }}"><i class="bi bi-qr-code-scan"></i>Attendance</a>
      @endcan
      @can('manage-attendance-locations')
        <a class="slink {{ request()->routeIs('admin.attendance-locations.*') ? 'act' : '' }}" href="{{ route('admin.attendance-locations.index') }}"><i class="bi bi-geo-alt-fill"></i>Attendance Locations</a>
      @endcan
      @can('manage-courses')
        <a class="slink {{ request()->routeIs('admin.courses.*') ? 'act' : '' }}" href="{{ route('admin.courses.index') }}"><i class="bi bi-collection-play-fill"></i>Courses</a>
      @endcan
      @can('manage-categories')
        <a class="slink {{ request()->routeIs('admin.categories.*') ? 'act' : '' }}" href="{{ route('admin.categories.index') }}"><i class="bi bi-tags-fill"></i>Categories</a>
      @endcan
    @endcanany
    @canany(['manage-coupons', 'manage-payments', 'manage-franchise-leads', 'manage-franchise-resources'])
      <div class="nsec">Commerce</div>
      @can('manage-coupons')
        <a class="slink {{ request()->routeIs('admin.coupons.*') ? 'act' : '' }}" href="{{ route('admin.coupons.index') }}"><i class="bi bi-ticket-perforated-fill"></i>Coupons</a>
      @endcan
      @can('manage-payments')
        <a class="slink {{ request()->routeIs('admin.payments.*') ? 'act' : '' }}" href="{{ route('admin.payments.index') }}"><i class="bi bi-credit-card-fill"></i>Payments</a>
        <a class="slink {{ request()->routeIs('admin.expenses.*') ? 'act' : '' }}" href="{{ route('admin.expenses.index') }}"><i class="bi bi-receipt-cutoff"></i>Expenses</a>
      @endcan
      @can('manage-franchise-leads')
        <a class="slink {{ request()->routeIs('admin.franchise.index') ? 'act' : '' }}" href="{{ route('admin.franchise.index') }}"><i class="bi bi-flag-fill"></i>Franchise Leads</a>
      @endcan
      @can('manage-franchise-resources')
        <a class="slink {{ request()->routeIs('admin.franchise.resources.*') ? 'act' : '' }}" href="{{ route('admin.franchise.resources.index') }}"><i class="bi bi-folder2-open"></i>Franchise Resources</a>
      @endcan
    @endcanany
    @canany(['manage-gallery', 'manage-reviews', 'manage-faqs', 'manage-blog', 'manage-team-members'])
      <div class="nsec">Content</div>
      @can('manage-gallery')
        <a class="slink {{ request()->routeIs('admin.gallery.*') ? 'act' : '' }}" href="{{ route('admin.gallery.index') }}"><i class="bi bi-images"></i>Gallery</a>
      @endcan
      @can('manage-team-members')
        <a class="slink {{ request()->routeIs('admin.team-members.*') ? 'act' : '' }}" href="{{ route('admin.team-members.index') }}"><i class="bi bi-person-vcard-fill"></i>Our Team</a>
      @endcan
      @can('manage-reviews')
        <a class="slink {{ request()->routeIs('admin.reviews.*') ? 'act' : '' }}" href="{{ route('admin.reviews.index') }}"><i class="bi bi-star-fill"></i>Reviews</a>
      @endcan
      @can('manage-faqs')
        <a class="slink {{ request()->routeIs('admin.faqs.*') ? 'act' : '' }}" href="{{ route('admin.faqs.index') }}"><i class="bi bi-question-circle-fill"></i>FAQs</a>
      @endcan
      @can('manage-blog')
        <a class="slink {{ request()->routeIs('admin.posts.*') ? 'act' : '' }}" href="{{ route('admin.posts.index') }}"><i class="bi bi-file-earmark-richtext-fill"></i>Blog</a>
      @endcan
    @endcanany
    @can('manage-team')
      <div class="nsec">Organization</div>
      <a class="slink {{ request()->routeIs('admin.team.*') ? 'act' : '' }}" href="{{ route('admin.team.index') }}"><i class="bi bi-people-fill"></i>Team &amp; Roles</a>
    @endcan
    <div class="nsec">Account</div>
    <a class="slink {{ request()->routeIs('admin.profile.*') ? 'act' : '' }}" href="{{ route('admin.profile.edit') }}"><i class="bi bi-person-fill"></i>Profile</a>
    <div class="nsec">More</div>
    <a class="slink" href="{{ route('home') }}"><i class="bi bi-house-fill"></i>Back to Website</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="slink" style="background:none;border:none;width:100%;text-align:left"><i class="bi bi-box-arrow-right"></i>Logout</button>
    </form>
  </nav>
  <div class="sb-bot"><div class="uinfo"><div class="uav">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div><div><div class="un">{{ auth()->user()->name }}</div><div class="ur">{{ auth()->user()->getRoleNames()->map(fn ($r) => ucfirst($r))->join(', ') }}</div></div></div></div>
</aside>

<div class="main">
  <div class="topbar">
    <button class="sbtn" onclick="document.getElementById('sb').classList.toggle('open')"><i class="bi bi-list"></i></button>
    <div class="tt">@yield('title', 'Dashboard')</div>
    <div class="tact">
      @include('partials.notification-bell')
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
@yield('scripts')
</body>
</html>
