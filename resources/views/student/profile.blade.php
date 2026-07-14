@extends('layouts.student')

@section('title', 'Profile')

@section('content')
  <div class="shead"><h4>My Profile</h4><p>Manage your personal information</p></div>
  <div class="row g-4">
    <div class="col-lg-4">
      <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px;text-align:center">
        @if ($user->photoUrl())
          <img src="{{ $user->photoUrl() }}" alt="{{ $profile['name'] }}" class="mx-auto mb-3" style="width:96px;height:96px;border-radius:50%;object-fit:cover;display:block">
        @else
          <div class="pav mx-auto mb-3">{{ strtoupper(substr($profile['name'], 0, 1)) }}</div>
        @endif
        <h5 style="font-size:18px;font-weight:700;margin-bottom:2px">{{ $profile['name'] }}</h5>
        <p style="font-size:13px;color:var(--muted);margin-bottom:6px">Student</p>
        <p style="font-size:11px;color:var(--muted);margin-bottom:16px">ID: {{ $user->student_code ?? 'Not yet issued' }}</p>
        <div class="d-flex gap-2">
          <a href="{{ route('student.id-card.view') }}" target="_blank" class="bghost flex-grow-1 text-center" style="text-decoration:none"><i class="bi bi-eye-fill me-1"></i>View</a>
          <a href="{{ route('student.id-card.download') }}" class="bsave flex-grow-1 text-center" style="text-decoration:none"><i class="bi bi-download me-1"></i>Download</a>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px">
        <h6 style="font-size:15px;font-weight:700;margin-bottom:20px">Personal Information</h6>
        <form method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-md-6"><label class="flbl">Full Name</label><input type="text" class="fctrl" name="name" value="{{ $profile['name'] }}"/></div>
            <div class="col-md-6"><label class="flbl">Email Address</label><input type="email" class="fctrl" name="email" value="{{ $profile['email'] }}"/></div>
            <div class="col-md-6"><label class="flbl">Phone Number</label><input type="tel" class="fctrl" name="phone" value="{{ $profile['phone'] }}"/></div>
            <div class="col-md-6"><label class="flbl">Date of Birth</label><input type="date" class="fctrl" name="date_of_birth" value="{{ $profile['date_of_birth'] }}"/></div>
            <div class="col-12"><label class="flbl">Address</label><textarea class="fctrl" name="address" rows="2">{{ $profile['address'] }}</textarea></div>
            <div class="col-md-4"><label class="flbl">Guardian / Father's Name</label><input type="text" class="fctrl" name="guardian_name" value="{{ $profile['guardian_name'] }}"/></div>
            <div class="col-md-4"><label class="flbl">Blood Group</label>
              <select class="fctrl" name="blood_group">
                <option value="">Select</option>
                @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                  <option value="{{ $bg }}" @selected($profile['blood_group'] === $bg)>{{ $bg }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4"><label class="flbl">Emergency Contact</label><input type="tel" class="fctrl" name="emergency_contact" value="{{ $profile['emergency_contact'] }}"/></div>
            <div class="col-12"><label class="flbl">Profile Photo</label><input type="file" class="fctrl" name="photo" accept="image/*"/><span style="font-size:11px;color:var(--muted)">Used on your ID card &middot; square photo recommended</span></div>
          </div>
          <button class="bsave mt-4" type="submit"><i class="bi bi-check2-circle me-1"></i>Save Changes</button>
        </form>

        <div class="mt-4 pt-3" style="border-top:1px solid var(--border)">
          <h6 style="font-size:15px;font-weight:700;margin-bottom:16px">Change Password</h6>
          <form method="POST" action="{{ route('student.password.update') }}">
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
