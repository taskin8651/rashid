<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()->franchiseBookings()->where('status', 'paid')->get(['id', 'city']);

        $images = GalleryImage::whereIn('franchise_booking_id', $bookings->pluck('id'))
            ->latest()
            ->get();

        return view('franchise.gallery', compact('images', 'bookings'));
    }

    public function store(Request $request)
    {
        $bookingIds = $request->user()->franchiseBookings()->where('status', 'paid')->pluck('id');

        $validated = $request->validate([
            'franchise_booking_id' => ['required', 'integer', Rule::in($bookingIds)],
            'caption' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        GalleryImage::create([
            'franchise_booking_id' => $validated['franchise_booking_id'],
            'uploaded_by' => $request->user()->id,
            'caption' => $validated['caption'] ?? null,
            'image_path' => $path,
            'status' => 'pending',
        ]);

        return back()->with('status', 'Image submitted for approval.');
    }

    public function destroy(Request $request, GalleryImage $gallery)
    {
        $bookingIds = $request->user()->franchiseBookings()->pluck('id');

        abort_unless($bookingIds->contains($gallery->franchise_booking_id), 403);

        $gallery->delete();

        return back()->with('status', 'Image removed.');
    }
}
