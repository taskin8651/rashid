<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::latest()->get();

        return view('admin.coupons.index', compact('coupons'));
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

        Coupon::create($validated);

        return back()->with('status', 'Coupon created.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->update(['status' => 'inactive']);
        return back()->with('status', 'Coupon deactivated.');
    }
}
