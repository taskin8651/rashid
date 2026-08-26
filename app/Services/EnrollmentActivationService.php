<?php

namespace App\Services;

use App\Mail\EnrollmentConfirmed;
use App\Models\Payment;
use App\Models\Referral;
use App\Notifications\EnrollmentConfirmedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EnrollmentActivationService
{
    /**
     * Marks the payment paid and grants course access. Guarded by the
     * enrollment's own status (not just the payment's) because a manually
     * verified UPI payment can arrive after the same enrollment was already
     * completed via Razorpay — without this check that race would double-fire
     * the coupon/seat increments and the confirmation email.
     */
    public function activate(Payment $payment, ?int $recordedBy = null): void
    {
        $enrollment = DB::transaction(function () use ($payment, $recordedBy) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => $payment->paid_at ?? now(),
                'recorded_by' => $payment->recorded_by ?? $recordedBy,
            ]);

            $enrollment = $payment->payable();

            if ($enrollment->status !== 'paid') {
                $enrollment->update(['status' => 'paid', 'enrolled_at' => now()]);

                if ($enrollment->coupon_id) {
                    $enrollment->coupon()->increment('used_count');
                }

                if ($enrollment->batch_id) {
                    $enrollment->batch()->increment('seats_filled');
                }
            }

            return $enrollment;
        });

        $enrollment->load(['user', 'course']);

        try {
            Mail::to($enrollment->user->email)->send(new EnrollmentConfirmed($enrollment));
        } catch (\Throwable $e) {
            report($e);
        }

        $enrollment->user->notify(new EnrollmentConfirmedNotification($enrollment));

        Referral::rewardForFirstPurchase($enrollment->user);
    }
}
