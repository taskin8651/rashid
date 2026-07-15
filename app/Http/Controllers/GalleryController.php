<?php

namespace App\Http\Controllers;

use App\Models\FranchiseBooking;
use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $franchiseBookingId = $request->query('franchise');

        $images = GalleryImage::where('status', 'approved')
            ->with('franchiseBooking')
            ->when($franchiseBookingId, fn ($q) => $q->where('franchise_booking_id', $franchiseBookingId))
            ->latest()
            ->get();

        $franchises = FranchiseBooking::where('status', 'paid')
            ->whereHas('galleryImages', fn ($q) => $q->where('status', 'approved'))
            ->orderBy('city')
            ->get(['id', 'city']);

        return view('gallery', compact('images', 'franchises', 'franchiseBookingId'));
    }
}
