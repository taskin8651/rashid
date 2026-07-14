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
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#loginTab" id="loginTabTrigger">Login</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#signupTab">Sign Up</button></li>
          </ul>
          <button type="button" data-bs-toggle="tab" data-bs-target="#forgotTab" id="forgotTabTrigger" class="d-none"></button>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="loginTab">
              <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
                @error('login')<div class="alert alert-danger py-2" style="font-size:13px">{{ $message }}</div>@enderror
                <div class="mb-3"><label style="font-size:12px;font-weight:600;color:rgba(var(--text-rgb),.7);display:block;margin-bottom:5px">Email</label><input class="mi" type="email" name="email" placeholder="Enter email" required /></div>
                <div class="mb-3">
                  <div class="d-flex justify-content-between align-items-center" style="margin-bottom:5px">
                    <label style="font-size:12px;font-weight:600;color:rgba(var(--text-rgb),.7)">Password</label>
                    <a href="#" onclick="document.getElementById('forgotTabTrigger').click();return false" style="font-size:11px;color:var(--orange);text-decoration:none">Forgot password?</a>
                  </div>
                  <input class="mi" type="password" name="password" placeholder="Enter password" required />
                </div>
                <button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-box-arrow-in-right"></i>Login</button>
                <p style="text-align:center;font-size:11px;color:var(--muted);margin-top:14px">Admin? <a href="{{ route('admin.dashboard') }}" style="color:var(--orange);text-decoration:none">Admin Panel →</a></p>
              </form>
            </div>
            <div class="tab-pane fade" id="forgotTab">
              <form method="POST" action="{{ route('password.email') }}">
                @csrf
                @if (session('status'))
                  <div class="alert alert-success py-2" style="font-size:13px">{{ session('status') }}</div>
                @endif
                <p style="font-size:12px;color:var(--muted);margin-bottom:14px">Enter your account email and we'll send you a password reset link.</p>
                <div class="mb-4"><label class="fl">Email</label><input class="mi" type="email" name="email" placeholder="you@example.com" required /></div>
                <button class="btn-p w-100 justify-content-center" type="submit"><i class="bi bi-send-fill"></i>Send Reset Link</button>
                <p style="text-align:center;font-size:11px;color:var(--muted);margin-top:14px"><a href="#" onclick="document.getElementById('loginTabTrigger').click();return false" style="color:var(--orange)">&larr; Back to Login</a></p>
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

  <!-- DEMO MODAL (video src set dynamically via JS from the clicked card's data-src) -->
  <div class="modal fade" id="demoM" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="demoMTitle">Free Demo Video</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body p-0" style="border-radius:0 0 18px 18px;overflow:hidden;background:#000">
          <video id="demoMPlayer" controls controlsList="nodownload noremoteplayback" disablePictureInPicture disableRemotePlayback oncontextmenu="return false" style="width:100%;aspect-ratio:16/9;display:block"></video>
        </div>
      </div>
    </div>
  </div>
