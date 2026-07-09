  <!-- FRANCHISE BOOKING MODAL -->
  <div class="modal fade" id="fbm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <div><h5 class="modal-title">Reserve Your City</h5><div style="font-size:12px;color:var(--muted);margin-top:2px">Refundable registration · Secure payment powered by Razorpay</div></div>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST" action="{{ route('franchise.booking.create') }}">
          @csrf
          <div class="modal-body">
            <div class="row g-4">
              <div class="col-md-6">
                <h6 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px">Your Details</h6>
                <div class="mb-3"><label class="fl">Full Name</label><input class="mi" type="text" name="name" value="{{ auth()->user()->name ?? old('name') }}" placeholder="Enter your full name" required /></div>
                <div class="mb-3"><label class="fl">Email</label><input class="mi" type="email" name="email" value="{{ auth()->user()->email ?? old('email') }}" placeholder="you@example.com" required /></div>
                <div class="mb-3"><label class="fl">Phone</label><input class="mi" type="tel" name="phone" value="{{ auth()->user()->phone ?? old('phone') }}" placeholder="+91 XXXXX XXXXX" required /></div>
                <div class="mb-3"><label class="fl">City to Reserve</label><input class="mi" type="text" name="city" placeholder="e.g. Lucknow" required /></div>
                @guest
                  <p style="font-size:11px;color:var(--muted);margin-bottom:8px"><i class="bi bi-info-circle me-1"></i>We'll create your franchise partner account so you can track your application afterwards.</p>
                  <div class="mb-3"><label class="fl">Set a Password</label><input class="mi" type="password" name="password" placeholder="Min 8 characters" required /></div>
                  <div class="mb-0"><label class="fl">Confirm Password</label><input class="mi" type="password" name="password_confirmation" placeholder="Repeat password" required /></div>
                @endguest
              </div>
              <div class="col-md-6">
                <h6 style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:16px">Payment</h6>
                <div class="sbox">
                  <div class="sr"><span>Franchise Registration Fee</span><span>₹25,000</span></div>
                  <div class="sr"><span>Refundable</span><span style="color:var(--ok)">Yes</span></div>
                  <div class="st"><span>Total</span><span style="color:var(--orange)">₹25,000</span></div>
                </div>
                <div class="mt-3" style="font-size:12px;color:var(--muted)">
                  <div class="mb-1"><i class="bi bi-check-circle-fill me-1" style="color:var(--ok)"></i>Instant franchise dashboard access</div>
                  <div class="mb-1"><i class="bi bi-check-circle-fill me-1" style="color:var(--ok)"></i>Document upload &amp; agreement tracking</div>
                  <div><i class="bi bi-check-circle-fill me-1" style="color:var(--ok)"></i>Training &amp; marketing kit downloads</div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn-g" type="button" data-bs-dismiss="modal">Cancel</button>
            <button class="btn-enr" style="font-size:14px;padding:11px 24px" type="submit"><i class="bi bi-shield-lock-fill me-2"></i>Continue to Payment</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  @guest
    <!-- FRANCHISE LOGIN MODAL -->
    <div class="modal fade" id="flm" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <div><h5 class="modal-title">Franchise Partner Login</h5><div style="font-size:12px;color:var(--muted);margin-top:3px">Access your franchise dashboard</div></div>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            @error('login')<div class="alert alert-danger py-2" style="font-size:13px">{{ $message }}</div>@enderror
            <form method="POST" action="{{ route('login.attempt') }}">
              @csrf
              <div class="mb-3"><label class="fl">Email</label><input class="mi" type="email" name="email" placeholder="Enter email" required /></div>
              <div class="mb-3"><label class="fl">Password</label><input class="mi" type="password" name="password" placeholder="Enter password" required /></div>
              <button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-box-arrow-in-right"></i>Login</button>
            </form>
            <p style="text-align:center;font-size:12px;color:var(--muted);margin-top:16px">Haven't reserved a city yet? <span class="ls" data-bs-toggle="modal" data-bs-target="#fbm" data-bs-dismiss="modal">Reserve Your City</span></p>
          </div>
        </div>
      </div>
    </div>
  @endguest
