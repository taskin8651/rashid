<?php

namespace App\Models;

use App\Notifications\ReferralRewardedNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;

    public const REWARD_AMOUNT = 500;

    protected $fillable = ['referrer_id', 'referred_user_id', 'reward_coupon_id', 'status'];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function rewardCoupon()
    {
        return $this->belongsTo(Coupon::class, 'reward_coupon_id');
    }

    /**
     * Rewards the referrer with a one-time coupon the moment their referred
     * user's first paid enrollment lands — only fires once per referral.
     */
    public static function rewardForFirstPurchase(User $user): void
    {
        $referral = self::where('referred_user_id', $user->id)->where('status', 'pending')->first();

        if (!$referral) {
            return;
        }

        $isFirstPurchase = Enrollment::where('user_id', $user->id)->where('status', 'paid')->count() === 1;

        if (!$isFirstPurchase) {
            return;
        }

        $coupon = Coupon::create([
            'code' => 'REF' . strtoupper(Str::random(6)),
            'discount_amount' => self::REWARD_AMOUNT,
            'valid_until' => now()->addDays(90),
            'usage_limit' => 1,
            'status' => 'active',
        ]);

        $referral->update(['reward_coupon_id' => $coupon->id, 'status' => 'rewarded']);

        $referral->referrer->notify(new ReferralRewardedNotification($coupon));
    }
}
