<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $images = GalleryImage::with(['franchiseBooking', 'uploader'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(24)
            ->withQueryString();

        $pendingCount = GalleryImage::where('status', 'pending')->count();

        return view('admin.gallery.index', compact('images', 'status', 'pendingCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $path = $request->file('image')->store('gallery', 'public');

        GalleryImage::create([
            'uploaded_by' => $request->user()->id,
            'caption' => $validated['caption'] ?? null,
            'image_path' => $path,
            'status' => 'approved',
        ]);

        return back()->with('status', 'Image uploaded.');
    }

    public function approve(GalleryImage $gallery)
    {
        $gallery->update(['status' => 'approved']);

        return back()->with('status', 'Image approved.');
    }

    public function reject(GalleryImage $gallery)
    {
        $gallery->update(['status' => 'rejected']);

        return back()->with('status', 'Image rejected.');
    }

    public function destroy(GalleryImage $gallery)
    {
        $gallery->delete();

        return back()->with('status', 'Image removed.');
    }
}
