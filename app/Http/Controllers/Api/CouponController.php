<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function validateCode(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $coupon = Coupon::whereRaw('UPPER(code) = ?', [strtoupper($request->code)])->first();

        if (!$coupon || !$coupon->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired coupon code.'], 200);
        }

        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'discount_amount' => (float) $coupon->discount_amount,
            'message' => 'Coupon applied! ₹' . number_format($coupon->discount_amount, 0) . ' discount.',
        ]);
    }
}
