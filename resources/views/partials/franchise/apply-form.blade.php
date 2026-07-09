  <!-- APPLY / INQUIRY FORM -->
  <section class="sec sec-alt" id="apply">
    <div class="container">
      <div class="row g-5 align-items-center">
        <div class="col-lg-5 rv">
          <div class="sec-lbl">Apply Now</div>
          <h2 class="sec-h">Let's Discuss <em>Your City</em></h2>
          <p class="sec-sub mb-4">Share your details — our franchise team will call you within 24 hours.</p>
          <div class="fcp"><i class="bi bi-telephone-fill"></i><a href="tel:9117744925" style="color:rgba(var(--text-rgb),.8);text-decoration:none">+91 9117744925</a></div>
          <div class="fcp"><i class="bi bi-envelope-fill"></i><a href="mailto:franchise@rtechcomputer.in" style="color:rgba(var(--text-rgb),.8);text-decoration:none">franchise@rtechcomputer.in</a></div>
          <div class="fcp"><i class="bi bi-whatsapp"></i><a href="https://wa.me/919117744925?text=Hi%2C%20I%27m%20interested%20in%20an%20R-Tech%20Computer%20franchise!" style="color:rgba(var(--text-rgb),.8);text-decoration:none">Chat on WhatsApp</a></div>
        </div>
        <div class="col-lg-7 rv">
          <div class="cbox">
            <h5 style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:18px;margin-bottom:24px">Franchise Inquiry Form</h5>
            @if ($errors->any() && !$errors->has('login') && !$errors->has('password') && !$errors->has('email'))
              <div class="alert alert-danger" style="font-size:13px">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('franchise.inquiry') }}">
              @csrf
              <div class="row g-3">
                <div class="col-md-6"><label class="fl">Full Name</label><input class="inp" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required /></div>
                <div class="col-md-6"><label class="fl">Phone</label><input class="inp" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 XXXXX XXXXX" required /></div>
                <div class="col-md-6"><label class="fl">Email</label><input class="inp" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" /></div>
                <div class="col-md-6"><label class="fl">City / Location</label><input class="inp" type="text" name="city" value="{{ old('city') }}" placeholder="City you're interested in" required /></div>
                <div class="col-12">
                  <label class="fl">Investment Budget</label>
                  <select class="inp" name="budget_range" style="cursor:pointer">
                    <option value="">Select a range</option>
                    <option>₹1.5L – ₹2.5L (Study Center Partner)</option>
                    <option>₹3L – ₹4.5L (Growth Partner)</option>
                    <option>₹6L – ₹9L (City Partner)</option>
                    <option>Not sure yet</option>
                  </select>
                </div>
                <div class="col-12"><label class="fl">Message</label><textarea class="inp" name="message" rows="3" placeholder="Tell us about your space, city and goals…">{{ old('message') }}</textarea></div>
                <div class="col-12"><button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-send-fill"></i>Submit Inquiry</button></div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
