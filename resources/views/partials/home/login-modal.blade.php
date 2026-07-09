  <!-- LOGIN / SIGNUP MODAL -->
  <div class="modal fade" id="lm" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Student Login</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#loginTab">Login</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#signupTab">Sign Up</button></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="loginTab">
              <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                @error('login')<div class="alert alert-danger py-2" style="font-size:13px">{{ $message }}</div>@enderror
                <div class="mb-3"><label style="font-size:12px;font-weight:600;color:rgba(var(--text-rgb),.7);display:block;margin-bottom:5px">Email</label><input class="mi" type="email" name="email" placeholder="Enter email" required /></div>
                <div class="mb-3"><label style="font-size:12px;font-weight:600;color:rgba(var(--text-rgb),.7);display:block;margin-bottom:5px">Password</label><input class="mi" type="password" name="password" placeholder="Enter password" required /></div>
                <button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-box-arrow-in-right"></i>Login</button>
                <p style="text-align:center;font-size:11px;color:var(--muted);margin-top:14px">Admin? <a href="{{ route('admin.dashboard') }}" style="color:var(--orange);text-decoration:none">Admin Panel →</a></p>
              </form>
            </div>
            <div class="tab-pane fade" id="signupTab">
              <form method="POST" action="{{ route('signup.attempt') }}">
                @csrf
                <div class="mb-3"><label class="fl">Full Name</label><input class="mi" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required />@error('name')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="fl">Email</label><input class="mi" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required />@error('email')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror</div>
                <div class="mb-3"><label class="fl">Phone</label><input class="mi" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+91 XXXXX XXXXX" /></div>
                <div class="mb-3"><label class="fl">Password</label><input class="mi" type="password" name="password" placeholder="Min 8 characters" required />@error('password')<div class="text-danger" style="font-size:12px">{{ $message }}</div>@enderror</div>
                <div class="mb-4"><label class="fl">Confirm Password</label><input class="mi" type="password" name="password_confirmation" placeholder="Repeat password" required /></div>
                <button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-person-plus-fill"></i>Create Account</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- DEMO MODAL (illustrative only, no data) -->
  <div class="modal fade" id="demoM" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Free Demo Video</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body p-0" style="border-radius:0 0 18px 18px;overflow:hidden">
          <div style="aspect-ratio:16/9;background:linear-gradient(135deg,#050d1f,#0f2147);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px">
            <div style="width:80px;height:80px;background:var(--grad);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:30px;color:#fff">▶</div>
            <p style="color:rgba(255,255,255,.5);font-size:13px;text-align:center">Demo video player</p>
          </div>
        </div>
      </div>
    </div>
  </div>
