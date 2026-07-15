<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Auth::attempt($credentials)) {
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['login' => 'Account is deactivated. Contact support.'])->withInput();
        }

        $request->session()->regenerate();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }

        if ($user->hasRole('franchisee')) {
            return redirect()->route('franchise.dashboard');
        }

        return redirect()->route('home');
    }

    public function signup(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
        ]);
        $user->assignRole('student');

        $refCode = $request->session()->pull('ref_code');
        if ($refCode) {
            $referrer = User::where('referral_code', $refCode)->first();
            if ($referrer) {
                Referral::create([
                    'referrer_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'status' => 'pending',
                ]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')->with('status', 'Account created successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        // Always report success, whether or not the email exists, to avoid
        // leaking which addresses are registered.
        try {
            PasswordBroker::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with(['status' => 'If that email is registered, a password reset link is on its way.', 'show_forgot_modal' => true]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $status = PasswordBroker::reset($validated, function (User $user, string $password) {
            $user->forceFill(['password' => $password])->save();
        });

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            return back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
        }

        return redirect()->route('home')->with('status', 'Password reset! You can now log in.');
    }
}
