<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $reviews = Review::with(['user', 'course'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount = Review::where('status', 'pending')->count();

        return view('admin.reviews.index', compact('reviews', 'status', 'pendingCount'));
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);

        return back()->with('status', 'Review approved.');
    }

    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected', 'is_featured' => false]);

        return back()->with('status', 'Review rejected.');
    }

    public function toggleFeature(Review $review)
    {
        $review->update(['is_featured' => !$review->is_featured]);

        return back()->with('status', $review->is_featured ? 'Marked as featured testimonial.' : 'Removed from testimonials.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
