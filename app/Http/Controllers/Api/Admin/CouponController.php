<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::get()->map(fn (Coupon $c) => [
            'id' => $c->id,
            'code' => $c->code,
            'discount_amount' => (float) $c->discount_amount,
            'used_count' => $c->used_count,
            'usage_limit' => $c->usage_limit,
            'valid_until' => optional($c->valid_until)->format('d M Y'),
            'status' => $c->status,
        ]);

        return response()->json(['coupons' => $coupons]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('coupons', 'code')],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'valid_until' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['status'] = 'active';

        $coupon = Coupon::create($validated);

        return response()->json(['message' => 'Coupon created.', 'coupon_id' => $coupon->id], 201);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'valid_until' => ['nullable', 'date'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ]);

        $coupon->update($validated);

        return response()->json(['message' => 'Coupon updated.']);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->update(['status' => 'inactive']);
        return response()->json(['message' => 'Coupon deactivated.']);
    }
}
