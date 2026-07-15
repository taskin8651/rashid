<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $user->ensureReferralCode();

        $referrals = $user->referralsMade()->with(['referredUser', 'rewardCoupon'])->latest()->get();

        $stats = [
            'total' => $referrals->count(),
            'rewarded' => $referrals->where('status', 'rewarded')->count(),
            'pending' => $referrals->where('status', 'pending')->count(),
        ];

        return view('student.referrals', compact('user', 'referrals', 'stats'));
    }
}
