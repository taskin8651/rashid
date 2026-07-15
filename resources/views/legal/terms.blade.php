@extends('layouts.site')

@section('title', 'Terms of Service')

@section('content')
  <section class="sec">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Legal</div>
        <h2 class="sec-h">Terms of <em>Service</em></h2>
      </div>

      <div class="legal-content rv">
        <p class="legal-updated">Last updated: {{ now()->format('d M Y') }}</p>

        <h2>1. Acceptance of Terms</h2>
        <p>By creating an account, enrolling in a course, or registering a franchise on R-Tech Computer, you agree to be bound by these Terms of Service.</p>

        <h2>2. Course Access</h2>
        <p>Course access is granted upon successful payment and is valid for the duration stated on the course page. Videos, notes, and assignments are for personal, non-commercial use only. Sharing your login credentials or redistributing course content is prohibited and may result in account suspension.</p>

        <h2>3. Certificates</h2>
        <p>Certificates are issued upon 100% completion of a course's video content. A unique certificate code is generated for each certificate, which can be independently verified.</p>

        <h2>4. Franchise Partnership</h2>
        <p>Franchise registration requires a one-time refundable registration fee (see our <a href="{{ route('refund-policy') }}" style="color:var(--orange)">Refund Policy</a>). Franchise partners are responsible for the courses, content, and student support they offer under their registered city, subject to R-Tech Computer's brand and quality guidelines.</p>

        <h2>5. User Conduct</h2>
        <ul>
          <li>You must provide accurate information when registering</li>
          <li>You may not upload unlawful, offensive, or infringing content (including in the gallery or assignment submissions)</li>
          <li>Coupon codes and referral rewards may not be resold or abused</li>
        </ul>

        <h2>6. Payments</h2>
        <p>All payments are processed via Razorpay in Indian Rupees (INR). Prices are as displayed at checkout, inclusive of any applicable discounts or coupons.</p>

        <h2>7. Limitation of Liability</h2>
        <p>R-Tech Computer provides courses and franchise support on an "as-is" basis. We are not liable for indirect or consequential losses arising from use of the platform.</p>

        <h2>8. Changes to Terms</h2>
        <p>We may update these terms from time to time. Continued use of the platform after changes constitutes acceptance of the revised terms.</p>

        <h2>9. Contact</h2>
        <p>Questions about these terms can be sent via our <a href="{{ route('contact') }}" style="color:var(--orange)">Contact page</a>.</p>
      </div>
    </div>
  </section>
@endsection
