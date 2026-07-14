<?php

namespace App\Http\Controllers;

use App\Mail\EnrollmentConfirmed;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EnrollmentController extends Controller
{
    public function create(Request $request, Course $course)
    {
        if (Enrollment::where('user_id', $request->user()->id)->where('course_id', $course->id)->where('status', 'paid')->exists()) {
            return redirect()->route('student.dashboard')->with('status', 'You are already enrolled in this course.');
        }

        $batches = $course->batches()->where('status', '!=', 'closed')->get();

        return view('enroll.create', compact('course', 'batches'));
    }

    public function store(Request $request, Course $course, RazorpayService $razorpay)
    {
        $validated = $request->validate([
            'batch_id' => ['nullable', 'exists:batches,id'],
            'coupon_code' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->where('status', 'paid')->exists()) {
            return redirect()->route('student.dashboard')->with('status', 'You are already enrolled in this course.');
        }

        $discount = 0;
        $coupon = null;
        if (!empty($validated['coupon_code'])) {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($validated['coupon_code'])])->first();
            if ($coupon && $coupon->isValid()) {
                $discount = (float) $coupon->discount_amount;
            } else {
                $coupon = null;
                return back()->withErrors(['coupon_code' => 'Invalid or expired coupon code.'])->withInput();
            }
        }

        $finalAmount = max(0, (float) $course->price - $discount);

        $enrollment = DB::transaction(function () use ($user, $course, $validated, $coupon, $discount, $finalAmount) {
            return Enrollment::updateOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'batch_id' => $validated['batch_id'] ?? null,
                    'coupon_id' => $coupon?->id,
                    'base_price' => $course->price,
                    'discount_amount' => $discount,
                    'final_amount' => $finalAmount,
                    'status' => 'pending',
                ]
            );
        });

        $order = $razorpay->createOrder($finalAmount, 'enr_' . $enrollment->id);

        Payment::create([
            'payable_type' => 'course_enrollment',
            'payable_id' => $enrollment->id,
            'user_id' => $user->id,
            'razorpay_order_id' => $order['order_id'],
            'amount' => $finalAmount,
            'status' => 'created',
        ]);

        return view('enroll.checkout', [
            'course' => $course,
            'order' => $order,
            'discount' => $discount,
            'finalAmount' => $finalAmount,
        ]);
    }

    public function verify(Request $request, Course $course, RazorpayService $razorpay)
    {
        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $payment = Payment::where('razorpay_order_id', $validated['razorpay_order_id'])
            ->where('payable_type', 'course_enrollment')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $valid = $razorpay->verifySignature($validated['razorpay_order_id'], $validated['razorpay_payment_id'], $validated['razorpay_signature']);

        if (!$valid) {
            $payment->update(['status' => 'failed']);
            return redirect()->route('enroll.create', $course)->withErrors(['payment' => 'Payment verification failed.']);
        }

        $enrollment = DB::transaction(function () use ($payment, $validated) {
            $payment->update([
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
                'status' => 'paid',
            ]);

            $enrollment = Enrollment::findOrFail($payment->payable_id);
            $enrollment->update(['status' => 'paid', 'enrolled_at' => now()]);

            if ($enrollment->coupon_id) {
                $enrollment->coupon()->increment('used_count');
            }

            if ($enrollment->batch_id) {
                $enrollment->batch()->increment('seats_filled');
            }

            return $enrollment;
        });

        try {
            Mail::to($enrollment->user->email)->send(new EnrollmentConfirmed($enrollment->load(['user', 'course'])));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('student.dashboard')->with('status', 'Payment verified. Enrollment confirmed!');
    }
}
