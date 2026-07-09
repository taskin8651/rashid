@extends('layouts.franchise')

@section('title', 'Profile')

@section('content')
  <div class="shead"><h4>My Profile</h4><p>Manage your personal information</p></div>
  <div class="row g-4">
    <div class="col-lg-8">
      <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px">
        <h6 style="font-size:15px;font-weight:700;margin-bottom:20px">Personal Information</h6>
        <form method="POST" action="{{ route('franchise.profile.update') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6"><label class="flbl">Full Name</label><input type="text" class="fctrl" name="name" value="{{ $profile['name'] }}"/></div>
            <div class="col-md-6"><label class="flbl">Email Address</label><input type="email" class="fctrl" name="email" value="{{ $profile['email'] }}"/></div>
            <div class="col-md-6"><label class="flbl">Phone Number</label><input type="tel" class="fctrl" name="phone" value="{{ $profile['phone'] }}"/></div>
          </div>
          <button class="bsave mt-4" type="submit"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
        </form>

        <div class="mt-4 pt-3" style="border-top:1px solid var(--border)">
          <h6 style="font-size:15px;font-weight:700;margin-bottom:16px">Change Password</h6>
          <form method="POST" action="{{ route('franchise.password.update') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-4"><label class="flbl">Current Password</label><input type="password" class="fctrl" name="current_password" placeholder="••••••••"/></div>
              <div class="col-md-4"><label class="flbl">New Password</label><input type="password" class="fctrl" name="password" placeholder="••••••••"/></div>
              <div class="col-md-4"><label class="flbl">Confirm New</label><input type="password" class="fctrl" name="password_confirmation" placeholder="••••••••"/></div>
            </div>
            <button class="bsave mt-3" type="submit"><i class="bi bi-shield-lock-fill me-1"></i>Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
