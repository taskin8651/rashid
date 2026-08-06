@extends('layouts.site')

@section('title', 'Enquire Now')

@section('content')
  <section class="sec">
    <div class="container" style="max-width:640px">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Enquire Now</div>
        <h2 class="sec-h">
          @if ($booking)
            Talk to R-Tech <em>{{ $booking->city }}</em>
          @else
            Talk to <em>R-Tech Computer</em>
          @endif
        </h2>
        <p class="sec-sub mx-auto">Share your details and our counselors will get back to you shortly.</p>
      </div>

      <div class="cbox rv">
        <form method="POST" action="{{ route('leads.apply.submit') }}">
          @csrf
          <input type="hidden" name="token" value="{{ $booking->lead_form_token ?? '' }}">
          <div style="position:absolute;left:-9999px" aria-hidden="true">
            <label>Leave this field empty</label>
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </div>

          <div class="row g-3">
            <div class="col-md-6"><label class="fl">Full Name</label><input class="inp" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required /></div>
            <div class="col-md-6"><label class="fl">Phone</label><input class="inp" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 XXXXX XXXXX" required /></div>
            <div class="col-12"><label class="fl">Email</label><input class="inp" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" /></div>
            <div class="col-12">
              <label class="fl">Interested Course</label>
              <select class="inp" name="course_id" style="cursor:pointer">
                <option value="">Select a course</option>
                @foreach ($courses as $c)
                  <option value="{{ $c->id }}" @selected(old('course_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12"><label class="fl">Message</label><textarea class="inp" name="message" rows="3" placeholder="Tell us your goal…">{{ old('message') }}</textarea></div>
            <div class="col-12"><button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-send-fill"></i>Submit Enquiry</button></div>
          </div>
        </form>
      </div>
    </div>
  </section>
@endsection
