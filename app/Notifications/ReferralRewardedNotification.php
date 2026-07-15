<?php

namespace App\Notifications;

use App\Models\Coupon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReferralRewardedNotification extends Notification
{
    use Queueable;

    public function __construct(protected Coupon $coupon)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'bi-gift-fill',
            'title' => 'Referral Reward Unlocked',
            'body' => 'Your friend just enrolled! Use code ' . $this->coupon->code . ' for ₹' . number_format((float) $this->coupon->discount_amount, 0) . ' off your next course.',
            'url' => route('student.referrals.index'),
        ];
    }
}
