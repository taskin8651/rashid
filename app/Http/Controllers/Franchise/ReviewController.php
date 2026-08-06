<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use AuthorizesFranchiseAccess;

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'manage-courses');

        $user = $request->user();
        $bookingIds = $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'manage-courses'))
            ->values();

        $reviews = Review::with(['user', 'course'])
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->latest()
            ->paginate(20);

        return view('franchise.reviews.index', compact('reviews'));
    }
}
