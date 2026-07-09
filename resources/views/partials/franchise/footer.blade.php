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
            <a href="https://wa.me/919117744925?text=Hi%2C%20I%27m%20interested%20in%20an%20R-Tech%20Computer%20franchise!" class="sb" title="WhatsApp" target="_blank"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
          <div class="ftt">Franchise</div>
          <ul class="flinks">
            <li><a href="#why">Why Partner</a></li>
            <li><a href="#tiers">Investment Plans</a></li>
            <li><a href="#cities">City Availability</a></li>
            <li><a href="#apply">Apply Now</a></li>
          </ul>
        </div>
        <div class="col-6 col-md-3 col-lg-2">
          <div class="ftt">Quick Links</div>
          <ul class="flinks">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('courses') }}">Courses</a></li>
            <li><a href="{{ route('contact') }}">Contact</a></li>
            @auth
              @if (auth()->user()->hasRole('franchisee'))
                <li><a href="{{ route('franchise.dashboard') }}">My Franchise Dashboard</a></li>
              @endif
            @else
              <li><a href="#" data-bs-toggle="modal" data-bs-target="#flm">Franchise Login</a></li>
            @endauth
          </ul>
        </div>
        <div class="col-lg-4">
          <div class="ftt">Contact</div>
          <div class="fcp"><i class="bi bi-telephone-fill"></i>+91 9117744925</div>
          <div class="fcp"><i class="bi bi-envelope-fill"></i>franchise@rtechcomputer.in</div>
          <div class="fcp"><i class="bi bi-whatsapp"></i>WhatsApp Support</div>
        </div>
      </div>
      <div class="fbot">
        <p>© {{ date('Y') }} R-Tech Computer · All rights reserved · <a href="#">Privacy Policy</a> · <a href="#">Terms</a></p>
        <a href="#topnav" class="back-top"><i class="bi bi-arrow-up"></i>Back to top</a>
      </div>
    </div>
  </footer>

  <a href="https://wa.me/919117744925?text=Hi%2C%20I%27m%20interested%20in%20an%20R-Tech%20Computer%20franchise!" class="wa" target="_blank"><i class="bi bi-whatsapp"></i></a>
