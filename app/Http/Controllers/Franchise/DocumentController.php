<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\FranchiseBooking;
use App\Models\FranchiseDocument;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()->franchiseBookings()->with('documents')->latest()->get();
        $documentTypes = collect(FranchiseDocument::TYPES)->except('agreement');

        return view('franchise.documents', compact('bookings', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'franchise_booking_id' => ['required', 'exists:franchise_bookings,id'],
            'type' => ['required', Rule::in(['id_proof', 'space_photo', 'other'])],
            'label' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $booking = FranchiseBooking::where('id', $validated['franchise_booking_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $path = $request->file('file')->store('franchise-documents');

        FranchiseDocument::create([
            'franchise_booking_id' => $booking->id,
            'uploaded_by' => $request->user()->id,
            'type' => $validated['type'],
            'label' => $validated['label'],
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return back()->with('status', 'Document uploaded.');
    }

    public function download(Request $request, FranchiseDocument $document): Response
    {
        abort_unless($document->booking->user_id === $request->user()->id, 403);

        return response()->download(storage_path('app/' . $document->file_path), $document->original_name);
    }
}
