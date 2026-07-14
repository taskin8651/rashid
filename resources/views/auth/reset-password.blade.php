@extends('layouts.site')

@section('title', 'Reset Password')

@section('content')
  <section class="sec" style="padding-top:120px;min-height:70vh">
    <div class="container" style="max-width:460px">
      <div class="cbox">
        <h5 style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:20px;margin-bottom:6px">Reset Your Password</h5>
        <p style="font-size:13px;color:var(--muted);margin-bottom:24px">Choose a new password for your account.</p>

        @if ($errors->any())
          <div class="alert alert-danger" style="font-size:13px">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
          @csrf
          <input type="hidden" name="token" value="{{ $token }}">
          <div class="mb-3"><label class="fl">Email</label><input class="mi" type="email" name="email" value="{{ old('email', $email) }}" required /></div>
          <div class="mb-3"><label class="fl">New Password</label><input class="mi" type="password" name="password" placeholder="Min 8 characters" required /></div>
          <div class="mb-4"><label class="fl">Confirm New Password</label><input class="mi" type="password" name="password_confirmation" placeholder="Repeat password" required /></div>
          <button class="btn-enr w-100 justify-content-center" style="font-size:14px;padding:12px" type="submit"><i class="bi bi-shield-lock-fill me-2"></i>Reset Password</button>
        </form>
      </div>
    </div>
  </section>
@endsection
