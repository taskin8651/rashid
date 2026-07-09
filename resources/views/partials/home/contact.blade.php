  <!-- CONTACT -->
  <section class="sec sec-alt" id="contact">
    <div class="container">
      <div class="row g-5 align-items-center">
        <div class="col-lg-5 rv">
          <div class="sec-lbl">Get In Touch</div>
          <h2 class="sec-h">We'll Help You <em>Choose Right</em></h2>
          <p class="sec-sub mb-4">Have questions? Our counselors will guide you to the perfect learning path.</p>
          <div class="fcp"><i class="bi bi-telephone-fill"></i><a href="tel:9117744925" style="color:rgba(var(--text-rgb),.8);text-decoration:none">+91 9117744925</a></div>
          <div class="fcp"><i class="bi bi-whatsapp"></i><a href="https://wa.me/919117744925" style="color:rgba(var(--text-rgb),.8);text-decoration:none">Chat on WhatsApp</a></div>
        </div>
        <div class="col-lg-7 rv">
          <div class="cbox">
            <h5 style="font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:18px;margin-bottom:24px">Send Us a Message</h5>
            <form method="POST" action="{{ route('contact.submit') }}">
              @csrf
              <div class="row g-3">
                <div class="col-md-6"><label class="fl">Full Name</label><input class="inp" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required /></div>
                <div class="col-md-6"><label class="fl">Phone</label><input class="inp" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 XXXXX XXXXX" /></div>
                <div class="col-12"><label class="fl">Email</label><input class="inp" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required /></div>
                <div class="col-12">
                  <label class="fl">Interested Course</label>
                  <select class="inp" name="interested_course" style="cursor:pointer">
                    <option value="">Select a course</option>
                    @foreach ($courses as $c)
                      <option value="{{ $c->name }}">{{ $c->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12"><label class="fl">Message</label><textarea class="inp" name="message" rows="3" placeholder="Tell us your goal…">{{ old('message') }}</textarea></div>
                <div class="col-12"><button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-send-fill"></i>Send Message</button></div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
