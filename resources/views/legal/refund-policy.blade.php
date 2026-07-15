@extends('layouts.site')

@section('title', 'Refund Policy')

@section('content')
  <section class="sec">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Legal</div>
        <h2 class="sec-h">Refund <em>Policy</em></h2>
      </div>

      <div class="legal-content rv">
        <p class="legal-updated">Last updated: {{ now()->format('d M Y') }}</p>

        <h2>1. Course Enrollments</h2>
        <p>Course fees are refundable within 7 days of enrollment, provided less than 20% of the course video content has been watched. To request a refund, contact our support team via the <a href="{{ route('contact') }}" style="color:var(--orange)">Contact page</a> with your enrollment details. Approved refunds are processed back to the original payment method through Razorpay within 5–7 business days.</p>

        <h2>2. Franchise Registration Fee</h2>
        <p>The one-time franchise registration fee is refundable if requested before the "Agreement Signed" stage of your franchise onboarding. Once the franchise agreement is signed and training has commenced, the registration fee becomes non-refundable.</p>

        <h2>3. Non-Refundable Cases</h2>
        <ul>
          <li>Certificates already issued for a completed course</li>
          <li>Courses where more than 20% of the content has been accessed</li>
          <li>Coupon or referral-discounted amounts already applied</li>
        </ul>

        <h2>4. How Refunds Are Processed</h2>
        <p>All refunds are issued through Razorpay directly to the original payment source (card, UPI, or net banking). R-Tech Computer does not process cash refunds. Once initiated, refunds typically reflect in your account within 5–7 business days, depending on your bank.</p>

        <h2>5. Contact for Refund Requests</h2>
        <p>Email or call our support team at +91 9117744925, or use the <a href="{{ route('contact') }}" style="color:var(--orange)">Contact page</a>, quoting your payment/transaction ID.</p>
      </div>
    </div>
  </section>
@endsection
