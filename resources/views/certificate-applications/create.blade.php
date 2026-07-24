@extends('layouts.site')

@section('title', 'Apply for Certificate')
@section('meta_title', 'Apply for Certificate | R-Tech Computer')
@section('meta_description', 'Offline students of R-Tech Computer can register and apply here to receive their official course completion certificate.')

@section('content')
  <section class="sec">
    <div class="container" style="max-width:760px">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Offline Students</div>
        <h2 class="sec-h">Apply for Your <em>Certificate</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:520px;margin:10px auto 0">Trained at an R-Tech Computer institute offline? Register here and apply for your official completion certificate — our team will review and issue it.</p>
      </div>

      <div class="cbox rv">
        @if ($errors->any())
          <div class="alert alert-danger" style="font-size:13px">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('certificate-applications.store') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-12"><h6 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px">Your Details</h6></div>
            <div class="col-md-6"><label class="fl">Full Name</label><input class="mi" type="text" name="name" value="{{ auth()->user()->name ?? old('name') }}" placeholder="Enter your full name" required /></div>
            <div class="col-md-6"><label class="fl">Email</label><input class="mi" type="email" name="email" value="{{ auth()->user()->email ?? old('email') }}" placeholder="you@example.com" required @if(auth()->check()) readonly @endif /></div>
            <div class="col-md-6"><label class="fl">Phone</label><input class="mi" type="tel" name="phone" value="{{ auth()->user()->phone ?? old('phone') }}" placeholder="+91 XXXXX XXXXX" required /></div>

            @guest
              <div class="col-md-6"><label class="fl">Set a Password</label><input class="mi" type="password" name="password" placeholder="Min 8 characters" required /></div>
              <div class="col-md-6"><label class="fl">Confirm Password</label><input class="mi" type="password" name="password_confirmation" placeholder="Repeat password" required /></div>
              <div class="col-12"><p style="font-size:11px;color:var(--muted);margin:0"><i class="bi bi-info-circle me-1"></i>We'll create your student account so you can track your application and download your certificate.</p></div>
            @endguest

            <div class="col-12 mt-2"><h6 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px">Training Details</h6></div>
            <div class="col-md-6">
              <label class="fl">Course You Completed</label>
              <select class="mi" name="course_id" required>
                <option value="">Select course</option>
                @foreach ($courses as $course)
                  <option value="{{ $course->id }}" @selected(old('course_id') == $course->id)>{{ $course->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="fl">Trained At</label>
              <select class="mi" name="franchise_booking_id">
                <option value="">R-Tech Computer (Direct)</option>
                @foreach ($franchises as $f)
                  <option value="{{ $f->id }}" @selected(old('franchise_booking_id') == $f->id)>{{ $f->city }} Institute</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6"><label class="fl">Completion Date</label><input class="mi" type="date" name="completion_date" max="{{ now()->format('Y-m-d') }}" value="{{ old('completion_date') }}" required /></div>
            <div class="col-md-6"><label class="fl">Proof of Training (optional)</label><input class="mi" type="file" name="proof" accept="image/*,.pdf" /></div>
            <div class="col-12"><label class="fl">Notes (optional)</label><textarea class="mi" name="notes" rows="2" placeholder="Anything that helps us verify your training">{{ old('notes') }}</textarea></div>

            <div class="col-12 form-check mt-2">
              <input class="form-check-input" type="checkbox" name="terms" id="applyTerms" value="1" required />
              <label class="form-check-label" for="applyTerms" style="font-size:12px;color:var(--muted)">
                I agree to the <a href="{{ route('terms') }}" target="_blank" style="color:var(--orange)">Terms of Service</a> and <a href="{{ route('privacy-policy') }}" target="_blank" style="color:var(--orange)">Privacy Policy</a>, and confirm the details above are accurate.
              </label>
            </div>
          </div>

          <button class="btn-enr w-100 justify-content-center mt-4" style="font-size:14px;padding:12px" type="submit"><i class="bi bi-send-check-fill me-2"></i>Submit Application</button>
        </form>
      </div>
    </div>
  </section>
@endsection
