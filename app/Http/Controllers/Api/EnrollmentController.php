<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function createOrder(Request $request, RazorpayService $razorpay)
    {
        $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'batch_id' => ['nullable', 'exists:batches,id'],
            'coupon_code' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $course = Course::findOrFail($request->course_id);

        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->where('status', 'paid')->exists()) {
            return response()->json(['message' => 'You are already enrolled in this course.'], 422);
        }

        $discount = 0;
        $coupon = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($request->coupon_code)])->first();
            if ($coupon && $coupon->isValid()) {
                $discount = (float) $coupon->discount_amount;
            } else {
                $coupon = null;
            }
        }

        $finalAmount = max(0, (float) $course->price - $discount);

        $enrollment = DB::transaction(function () use ($user, $course, $request, $coupon, $discount, $finalAmount) {
            return Enrollment::updateOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                [
                    'batch_id' => $request->batch_id,
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

        return response()->json([
            'enrollment_id' => $enrollment->id,
            'order_id' => $order['order_id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key_id' => $order['key_id'],
            'course_name' => $course->name,
        ]);
    }

    public function verifyPayment(Request $request, RazorpayService $razorpay)
    {
        $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)
            ->where('payable_type', 'course_enrollment')
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $valid = $razorpay->verifySignature($request->razorpay_order_id, $request->razorpay_payment_id, $request->razorpay_signature);

        if (!$valid) {
            $payment->update(['status' => 'failed']);
            return response()->json(['message' => 'Payment verification failed.'], 422);
        }

        DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
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
        });

        return response()->json(['message' => 'Payment verified. Enrollment confirmed.']);
    }
}
