<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Services\EnrollmentActivationService;
use App\Services\RazorpayService;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'upiId' => config('services.upi.id'),
            'upiLink' => $this->buildUpiLink($course, $enrollment, $finalAmount),
            'upiQr' => $this->buildUpiQr($course, $enrollment, $finalAmount),
        ]);
    }

    /**
     * A direct UPI transfer has no gateway callback, so it can only ever be
     * "submitted" here — an admin confirms the money actually landed (see
     * Admin\PaymentController::verifyUpi) before the enrollment flips to paid.
     */
    public function submitUpiPayment(Request $request, Course $course)
    {
        $validated = $request->validate([
            'utr' => ['required', 'string', 'max:50'],
        ]);

        $user = $request->user();

        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->latest()
            ->firstOrFail();

        Payment::create([
            'payable_type' => 'course_enrollment',
            'payable_id' => $enrollment->id,
            'user_id' => $user->id,
            'amount' => $enrollment->final_amount,
            'method' => 'upi',
            'note' => 'UPI reference: ' . $validated['utr'],
            'status' => 'created',
        ]);

        return redirect()->route('student.dashboard')
            ->with('status', 'UPI payment submitted! We will verify your transfer and activate the course shortly.');
    }

    private function buildUpiLink(Course $course, Enrollment $enrollment, float $amount): ?string
    {
        $upiId = config('services.upi.id');

        if (! $upiId) {
            return null;
        }

        return 'upi://pay?' . http_build_query([
            'pa' => $upiId,
            'pn' => config('services.upi.name'),
            'am' => number_format($amount, 2, '.', ''),
            'cu' => 'INR',
            'tn' => 'Enrollment #' . $enrollment->id . ' - ' . $course->name,
        ]);
    }

    private function buildUpiQr(Course $course, Enrollment $enrollment, float $amount): ?string
    {
        $link = $this->buildUpiLink($course, $enrollment, $amount);

        if (! $link) {
            return null;
        }

        return Builder::create()
            ->writer(new PngWriter())
            ->data($link)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->size(260)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build()
            ->getDataUri();
    }

    public function verify(Request $request, Course $course, RazorpayService $razorpay, EnrollmentActivationService $activation)
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

        $payment->update([
            'razorpay_payment_id' => $validated['razorpay_payment_id'],
            'razorpay_signature' => $validated['razorpay_signature'],
        ]);

        $activation->activate($payment);

        return redirect()->route('student.dashboard')->with('status', 'Payment verified. Enrollment confirmed!');
    }
}
