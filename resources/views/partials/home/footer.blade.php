  <!-- FOOTER -->
  <footer id="siteFooter">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="d-flex align-items-center gap-2 mb-2">
            <div class="brand-logo-wrap"><img src="{{ asset('assets/img/logo.png') }}" alt="R-Tech Computer" class="brand-logo"></div>
            <div><div class="fn1">R-Tech Computer</div><div class="fn2">Skill Se Placement Tak</div></div>
          </div>
          <p class="fp">Empowering students with high-demand digital skills. Practical training, live projects and placement support.</p>
          <div class="srow">
            <a href="#" class="sb" title="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="sb" title="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="sb" title="YouTube"><i class="bi bi-youtube"></i></a>
            <a href="https://wa.me/919117744925" class="sb" title="WhatsApp" target="_blank"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
          <div class="ftt">Quick Links</div>
          <ul class="flinks">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('about') }}">About Us</a></li>
            <li><a href="{{ route('courses') }}">Courses</a></li>
            <li><a href="{{ route('gallery') }}">Gallery</a></li>
            <li><a href="{{ route('placements') }}">Placements</a></li>
            <li><a href="{{ route('careers') }}">Careers</a></li>
            <li><a href="{{ route('blog') }}">Blog</a></li>
            <li><a href="{{ route('certificates.verify') }}">Verify Certificate</a></li>
            <li><a href="{{ route('certificate-applications.create') }}">Apply for Certificate</a></li>
            <li><a href="{{ route('franchise') }}">Franchise</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
          </ul>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
          <div class="ftt">Account</div>
          <ul class="flinks">
            <li><a href="{{ route('student.dashboard') }}">Student Login</a></li>
            <li><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <div class="ftt">Contact</div>
          <div class="fcp"><i class="bi bi-telephone-fill"></i>+91 9117744925</div>
          <div class="fcp"><i class="bi bi-globe2"></i>rtechcomputer.in</div>
        </div>
      </div>
      <div class="fbot">
        <p>© {{ date('Y') }} R-Tech Computer · All rights reserved · <a href="{{ route('privacy-policy') }}">Privacy Policy</a> · <a href="{{ route('terms') }}">Terms</a> · <a href="{{ route('refund-policy') }}">Refund Policy</a></p>
        <a href="#topnav" class="back-top"><i class="bi bi-arrow-up"></i>Back to top</a>
      </div>
    </div>
  </footer>

  <a href="https://wa.me/919117744925" class="wa" target="_blank"><i class="bi bi-whatsapp"></i></a>
