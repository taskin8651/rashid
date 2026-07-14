<?php

namespace App\Http\Controllers;

use App\Mail\FranchiseBookingConfirmed;
use App\Models\FranchiseBooking;
use App\Models\FranchiseLead;
use App\Models\Payment;
use App\Models\User;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class FranchiseController extends Controller
{
    public function index()
    {
        return view('franchise');
    }

    public function show(FranchiseBooking $franchiseBooking)
    {
        abort_unless($franchiseBooking->status === 'paid', 404);

        $franchiseBooking->loadCount([
            'courses' => fn ($q) => $q->where('status', 'active'),
            'enrollments',
        ]);
        $franchiseBooking->load([
            'courses' => fn ($q) => $q->where('status', 'active')->with(['category', 'modules']),
        ]);

        $minCoursePrice = $franchiseBooking->courses->min('price');

        return view('franchise-show', compact('franchiseBooking', 'minCoursePrice'));
    }

    public function inquiry(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'city' => ['required', 'string', 'max:255'],
            'budget_range' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
        ]);

        $lead = FranchiseLead::create($validated);

        return back()->with('status', "Thanks {$lead->name}! Our franchise team will call you within 24 hours.");
    }

    public function bookingCreate(Request $request, RazorpayService $razorpay)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
        ];

        if (!Auth::check()) {
            $rules['email'][] = Rule::unique('users', 'email');
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        $validated = $request->validate($rules);

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
            ]);
            Auth::login($user);
            $request->session()->regenerate();
        }

        if (!$user->hasRole('franchisee')) {
            $user->assignRole('franchisee');
        }

        $amount = (float) config('services.franchise.booking_fee', 25000);

        $booking = FranchiseBooking::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'],
            'amount' => $amount,
            'status' => 'pending',
        ]);

        $order = $razorpay->createOrder($amount, 'fb_' . $booking->id);

        Payment::create([
            'payable_type' => 'franchise_booking',
            'payable_id' => $booking->id,
            'razorpay_order_id' => $order['order_id'],
            'amount' => $amount,
            'status' => 'created',
        ]);

        return view('franchise.checkout', [
            'booking' => $booking,
            'order' => $order,
        ]);
    }

    public function bookingVerify(Request $request, RazorpayService $razorpay)
    {
        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $payment = Payment::where('razorpay_order_id', $validated['razorpay_order_id'])
            ->where('payable_type', 'franchise_booking')
            ->firstOrFail();

        $valid = $razorpay->verifySignature($validated['razorpay_order_id'], $validated['razorpay_payment_id'], $validated['razorpay_signature']);

        if (!$valid) {
            $payment->update(['status' => 'failed']);
            return redirect()->route('franchise')->withErrors(['payment' => 'Payment verification failed.']);
        }

        $payment->update([
            'razorpay_payment_id' => $validated['razorpay_payment_id'],
            'razorpay_signature' => $validated['razorpay_signature'],
            'status' => 'paid',
        ]);

        $booking = FranchiseBooking::findOrFail($payment->payable_id);
        $booking->update(['status' => 'paid']);

        if ($booking->email) {
            try {
                Mail::to($booking->email)->send(new FranchiseBookingConfirmed($booking));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('franchise.success', compact('booking'));
    }
}
