@extends('layouts.site')

@section('title', 'About Us')
@section('meta_description', 'Learn about R-Tech Computer — our story, mission, team and the certified, job-oriented training that has helped hundreds of students build real careers.')

@section('content')
  

  <!-- ABOUT INTRO -->
  <section class="sec">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6 rv">
          <div class="sec-lbl">About R-Tech Computer</div>
          <h2 class="sec-h">Skill Se <em>Placement Tak</em></h2>
          <p class="sec-sub mb-3" style="max-width:100%">R-Tech Computer was founded with one simple belief — real careers are built on real, practical skills, not just certificates. What started as a single training center has grown into a trusted, multi-city institute network, helping students move from learning to earning.</p>
          <p style="font-size:14px;color:var(--muted);line-height:1.8">We focus on job-oriented computer courses — Web Development, Graphic Design, Digital Marketing and more — taught hands-on, in small batches, with live projects and dedicated placement support. Every course ends with an industry-recognized certificate that students can verify online for life.</p>
          <div class="hero-stats mt-3">
            <div><div class="stat-n">500+</div><div class="stat-l">Students Trained</div></div>
            <div><div class="stat-n">{{ $stats['franchises'] }}+</div><div class="stat-l">Franchise Centers</div></div>
            <div><div class="stat-n">95%</div><div class="stat-l">Placement Rate</div></div>
            <div><div class="stat-n">{{ $stats['courses'] }}+</div><div class="stat-l">Courses Offered</div></div>
          </div>
        </div>
        <div class="col-lg-6 rv">
          <div class="hero-card" style="padding:0;overflow:hidden">
            <img src="{{ asset('assets/img/hero/home-hero-2.jpg') }}" alt="Students training at R-Tech Computer" style="width:100%;height:420px;object-fit:cover;display:block">
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MISSION & VISION -->
  <section class="sec sec-alt">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-6 rv">
          <div class="wc" style="padding:32px;height:100%">
            <div class="wi"><i class="bi bi-bullseye"></i></div>
            <h5 style="font-size:17px;margin-bottom:10px">Our Mission</h5>
            <p style="font-size:13.5px">To make quality, job-oriented digital skills training accessible and affordable for every student — regardless of which city or town they come from — through practical, mentor-led courses and genuine placement support.</p>
          </div>
        </div>
        <div class="col-md-6 rv">
          <div class="wc" style="padding:32px;height:100%">
            <div class="wi"><i class="bi bi-eye-fill"></i></div>
            <h5 style="font-size:17px;margin-bottom:10px">Our Vision</h5>
            <p style="font-size:13.5px">To become India's most trusted computer training brand — recognized for turning learners into hired professionals — by building a nationwide network of franchise centers that carry the same standard of teaching everywhere.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- WHAT MAKES US DIFFERENT -->
  <section class="sec">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Our Strengths</div>
        <h2 class="sec-h">What Makes Us <em>Different</em></h2>
      </div>
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-laptop-fill"></i></div><h5>Practical Training</h5><p>Hands-on learning with real-world projects from day one</p></div></div>
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-people-fill"></i></div><h5>Small Batches</h5><p>Personal attention — max 15 students per batch</p></div></div>
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-patch-check-fill"></i></div><h5>Verified Certificate</h5><p>Industry-recognized certificate, verifiable online</p></div></div>
        <div class="col-6 col-md-3 rv"><div class="wc"><div class="wi"><i class="bi bi-briefcase-fill"></i></div><h5>Placement Support</h5><p>Dedicated help to land your first job in the field</p></div></div>
      </div>
      <div class="text-center rv"><a href="{{ route('why-rtech') }}" class="btn-g">See All Reasons to Choose Us<i class="bi bi-arrow-right"></i></a></div>
    </div>
  </section>

  <!-- TEAM -->
  <section class="sec sec-alt">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Our Team</div>
        <h2 class="sec-h">Meet the People Behind <em>R-Tech</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">A small, hands-on team focused on one goal — your career outcome.</p>
      </div>
      <div class="row g-4">
        @php
          $team = [
            ['name' => 'Rashid Khan', 'role' => 'Founder & Director', 'grad' => 'var(--grad)'],
            ['name' => 'Ayesha Ansari', 'role' => 'Head of Training', 'grad' => 'linear-gradient(135deg,#9c27b0,#e91e63)'],
            ['name' => 'Vikram Rao', 'role' => 'Franchise Relations Head', 'grad' => 'linear-gradient(135deg,#009688,#4caf50)'],
            ['name' => 'Sneha Kapoor', 'role' => 'Placement Coordinator', 'grad' => 'linear-gradient(135deg,#f59e0b,#ef4444)'],
          ];
        @endphp
        @foreach ($team as $member)
          <div class="col-6 col-md-3 rv">
            <div class="tc text-center">
              <div class="ra mx-auto mb-3" style="width:72px;height:72px;font-size:24px;background:{{ $member['grad'] }}">{{ strtoupper(substr($member['name'], 0, 1)) }}</div>
              <div class="rname" style="font-size:15px">{{ $member['name'] }}</div>
              <div class="rrole">{{ $member['role'] }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- CERTIFICATES -->
  <section class="sec">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Certification</div>
        <h2 class="sec-h">Industry-Recognized <em>Certificates</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">Every student who completes a course receives a certificate with a unique ID — verifiable online, anytime.</p>
      </div>
      <div class="row g-4 align-items-stretch justify-content-center">
        <div class="col-lg-5 rv">
          <div style="background:var(--card);border:2px solid var(--orange);border-radius:16px;padding:30px;position:relative;box-shadow:var(--shadow-card);height:100%">
            <div style="position:absolute;top:16px;right:16px;width:52px;height:52px;border-radius:50%;background:var(--grad);display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px"><i class="bi bi-patch-check-fill"></i></div>
            <div style="font-family:'Space Grotesk',sans-serif;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:var(--orange);font-weight:700;margin-bottom:6px">Certificate of Completion</div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:22px">This is to certify that</div>
            <div style="font-family:'Space Grotesk',sans-serif;font-size:24px;font-weight:700;margin-bottom:6px;border-bottom:1px dashed var(--border);padding-bottom:14px">Student Name</div>
            <div style="font-size:12px;color:var(--muted);margin:16px 0 4px">has successfully completed the course</div>
            <div style="font-size:16px;font-weight:700;color:var(--orange);margin-bottom:22px">Web Development</div>
            <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);border-top:1px solid var(--border);padding-top:14px">
              <span>Cert ID: RTC-25-XXXXXX</span>
              <span>R-Tech Computer</span>
            </div>
          </div>
        </div>
        <div class="col-lg-5 rv">
          <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;box-shadow:var(--shadow-card);height:100%">
            <div style="background:var(--grad);padding:20px 30px;color:#fff">
              <div style="font-family:'Space Grotesk',sans-serif;font-size:12px;letter-spacing:2px;text-transform:uppercase;font-weight:700">Certificate of Completion</div>
            </div>
            <div style="padding:26px 30px">
              <div style="font-size:11px;color:var(--muted);margin-bottom:22px">This is to certify that</div>
              <div style="font-family:'Space Grotesk',sans-serif;font-size:24px;font-weight:700;margin-bottom:6px;border-bottom:1px dashed var(--border);padding-bottom:14px">Student Name</div>
              <div style="font-size:12px;color:var(--muted);margin:16px 0 4px">has successfully completed the course</div>
              <div style="font-size:16px;font-weight:700;color:var(--orange);margin-bottom:22px">Digital Marketing</div>
              <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--muted);border-top:1px solid var(--border);padding-top:14px">
                <span>Cert ID: RTC-25-XXXXXX</span>
                <span>R-Tech Computer</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="text-center mt-4 rv d-flex flex-wrap justify-content-center gap-3">
        <a href="{{ route('certificates.verify') }}" class="btn-g"><i class="bi bi-search"></i>Verify a Certificate</a>
        <a href="{{ route('certificate-applications.create') }}" class="btn-p"><i class="bi bi-patch-check-fill"></i>Apply for Certificate</a>
      </div>
    </div>
  </section>

  <!-- PARTNERS -->
  <section class="sec sec-alt" style="padding:64px 0">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Our Network</div>
        <h2 class="sec-h" style="font-size:clamp(22px,3vw,30px)">Trusted <em>Partners</em> &amp; Affiliations</h2>
      </div>
      <div class="d-flex flex-wrap justify-content-center gap-3 rv">
        <div class="f-pill"><i class="bi bi-award-fill"></i>NSDC Aligned Curriculum</div>
        <div class="f-pill"><i class="bi bi-patch-check-fill"></i>MSME Registered</div>
        <div class="f-pill"><i class="bi bi-shield-check"></i>ISO 9001:2015</div>
        <div class="f-pill"><i class="bi bi-globe-asia-australia"></i>Skill India Network</div>
        <div class="f-pill"><i class="bi bi-rocket-takeoff-fill"></i>Startup India Recognized</div>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <section class="sec">
    <div class="container">
      <div class="text-center mb-5 rv">
        <div class="sec-lbl">Student Reviews</div>
        <h2 class="sec-h">What Our <em>Students Say</em></h2>
      </div>
      <div class="row g-4">
        @forelse ($testimonials as $t)
          @php $grad = ['var(--grad)', 'linear-gradient(135deg,#9c27b0,#e91e63)', 'linear-gradient(135deg,#009688,#4caf50)', 'linear-gradient(135deg,#f59e0b,#ef4444)'][$loop->index % 4]; @endphp
          <div class="col-md-6 col-lg-4 rv">
            <div class="tc">
              <div class="stars">{{ str_repeat('★', $t->rating) }}{{ str_repeat('☆', 5 - $t->rating) }}</div>
              <p>"{{ $t->comment }}"</p>
              <div class="rr">
                <div class="ra" style="background:{{ $grad }}">{{ strtoupper(substr($t->user->name, 0, 1)) }}</div>
                <div><div class="rname">{{ $t->user->name }}</div><div class="rrole">{{ $t->course->name }}</div></div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-md-6 col-lg-4 rv"><div class="tc"><div class="stars">★★★★★</div><p>"R-Tech completely changed my career direction. The Web Development course was 100% practical — I built real projects and got placed within 2 months!"</p><div class="rr"><div class="ra" style="background:var(--grad)">A</div><div><div class="rname">Amit Sharma</div><div class="rrole">Web Developer @ TechCorp</div></div></div></div></div>
          <div class="col-md-6 col-lg-4 rv"><div class="tc"><div class="stars">★★★★★</div><p>"The Graphic Design course covered everything in depth. Now I run my own freelance design business!"</p><div class="rr"><div class="ra" style="background:linear-gradient(135deg,#9c27b0,#e91e63)">P</div><div><div class="rname">Priya Singh</div><div class="rrole">Freelance Graphic Designer</div></div></div></div></div>
          <div class="col-md-6 col-lg-4 rv"><div class="tc"><div class="stars">★★★★★</div><p>"Best digital marketing training I've found. I now manage ₹5L/month ad budgets professionally!"</p><div class="rr"><div class="ra" style="background:linear-gradient(135deg,#009688,#4caf50)">R</div><div><div class="rname">Rohit Verma</div><div class="rrole">Digital Marketing Manager</div></div></div></div></div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section style="padding:64px 0;background:var(--grad);position:relative;overflow:hidden">
    <div class="container text-center rv">
      <h3 style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:clamp(22px,3.5vw,32px);color:#fff;margin-bottom:10px">Ready to Start Your Career Journey?</h3>
      <p style="color:rgba(255,255,255,.85);margin-bottom:26px;font-size:14px">Join hundreds of students who transformed their careers with R-Tech Computer.</p>
      <div class="d-flex flex-wrap justify-content-center gap-3">
        <a href="{{ route('courses') }}" class="btn-p" style="background:#fff;color:var(--orange)"><i class="bi bi-lightning-fill"></i>Explore Courses</a>
        <a href="{{ route('contact') }}" class="btn-g" style="background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.35);color:#fff"><i class="bi bi-chat-dots-fill"></i>Contact Us</a>
      </div>
    </div>
  </section>
@endsection
