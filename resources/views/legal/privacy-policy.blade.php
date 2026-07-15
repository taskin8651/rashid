@extends('layouts.site')

@section('title', 'Privacy Policy')

@section('content')
  <section class="sec">
    <div class="container">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Legal</div>
        <h2 class="sec-h">Privacy <em>Policy</em></h2>
      </div>

      <div class="legal-content rv">
        <p class="legal-updated">Last updated: {{ now()->format('d M Y') }}</p>

        <h2>1. Information We Collect</h2>
        <p>When you create an account, enroll in a course, or register a franchise with R-Tech Computer, we collect information such as your name, email address, phone number, date of birth, address, and payment details. We also collect course progress data (videos watched, quiz scores, assignment submissions) to power your learning dashboard and certificates.</p>

        <h2>2. How We Use Your Information</h2>
        <ul>
          <li>To create and manage your student, franchise, or admin account</li>
          <li>To process course enrollments, franchise registrations, and payments</li>
          <li>To issue certificates, ID cards, and track your learning progress</li>
          <li>To send transactional emails (enrollment confirmations, certificates, password resets)</li>
          <li>To respond to support and contact form requests</li>
        </ul>

        <h2>3. Payment Information</h2>
        <p>All payments are processed securely through Razorpay. R-Tech Computer does not store your card, UPI, or net-banking credentials on its own servers — these are handled entirely by Razorpay's PCI-DSS compliant infrastructure.</p>

        <h2>4. Data Sharing</h2>
        <p>We do not sell your personal information. Data may be shared with the specific franchise partner instructor teaching your enrolled course, solely to support your learning experience. We may disclose information if required by law.</p>

        <h2>5. Data Security</h2>
        <p>We use industry-standard measures — encrypted connections, hashed passwords, and access-controlled storage — to protect your information. Assignment submissions and personal documents are stored on access-restricted storage, not publicly reachable.</p>

        <h2>6. Your Rights</h2>
        <p>You may update your profile information at any time from your dashboard, or contact us to request account deletion or a copy of your data.</p>

        <h2>7. Contact Us</h2>
        <p>For any privacy-related questions, reach out via our <a href="{{ route('contact') }}" style="color:var(--orange)">Contact page</a> or call +91 9117744925.</p>
      </div>
    </div>
  </section>
@endsection
