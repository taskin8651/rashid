<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FranchiseBooking;
use App\Models\FranchiseLead;
use App\Models\Payment;
use App\Services\RazorpayService;
use Illuminate\Http\Request;

class FranchiseController extends Controller
{
    public function inquiry(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'city' => ['required', 'string', 'max:255'],
            'budget_range' => ['nullable', 'string'],
            'message' => ['nullable', 'string'],
        ]);

        $lead = FranchiseLead::create($request->only(['name', 'phone', 'email', 'city', 'budget_range', 'message']));

        return response()->json([
            'message' => 'Thanks! Our franchise team will call you within 24 hours.',
            'lead_id' => $lead->id,
        ], 201);
    }

    public function bookingCreateOrder(Request $request, RazorpayService $razorpay)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'city' => ['required', 'string', 'max:255'],
            'franchise_lead_id' => ['nullable', 'exists:franchise_leads,id'],
        ]);

        $amount = (float) config('services.franchise.booking_fee', 25000);

        $booking = FranchiseBooking::create([
            'franchise_lead_id' => $request->franchise_lead_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
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

        return response()->json([
            'booking_id' => $booking->id,
            'order_id' => $order['order_id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key_id' => $order['key_id'],
        ]);
    }

    public function bookingVerifyPayment(Request $request, RazorpayService $razorpay)
    {
        $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $payment = Payment::where('razorpay_order_id', $request->razorpay_order_id)
            ->where('payable_type', 'franchise_booking')
            ->firstOrFail();

        $valid = $razorpay->verifySignature($request->razorpay_order_id, $request->razorpay_payment_id, $request->razorpay_signature);

        if (!$valid) {
            $payment->update(['status' => 'failed']);
            return response()->json(['message' => 'Payment verification failed.'], 422);
        }

        $payment->update([
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
            'status' => 'paid',
        ]);

        FranchiseBooking::where('id', $payment->payable_id)->update(['status' => 'paid']);

        return response()->json(['message' => 'Booking confirmed! Our team will reach out to finalize your city.']);
    }
}
