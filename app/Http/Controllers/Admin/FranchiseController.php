<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\FranchiseStatusUpdated;
use App\Models\FranchiseBooking;
use App\Models\FranchiseDocument;
use App\Models\FranchiseLead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class FranchiseController extends Controller
{
    public function index()
    {
        $leads = FranchiseLead::latest()->get();
        $bookings = FranchiseBooking::with(['user', 'documents'])->latest()->get();
        $stages = FranchiseBooking::STAGES;

        return view('admin.franchise.index', compact('leads', 'bookings', 'stages'));
    }

    public function updateLead(Request $request, FranchiseLead $lead)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['new', 'contacted', 'converted', 'rejected'])],
        ]);

        $lead->update($validated);

        return back()->with('status', 'Lead updated.');
    }

    public function updateBooking(Request $request, FranchiseBooking $booking)
    {
        $validated = $request->validate([
            'stage' => ['required', Rule::in(array_keys(FranchiseBooking::STAGES))],
        ]);

        $booking->update($validated);

        if ($booking->email) {
            try {
                Mail::to($booking->email)->send(new FranchiseStatusUpdated($booking));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('status', 'Booking stage updated.');
    }

    public function uploadAgreement(Request $request, FranchiseBooking $booking)
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:10240'],
        ]);

        $path = $request->file('file')->store('franchise-documents');

        FranchiseDocument::create([
            'franchise_booking_id' => $booking->id,
            'uploaded_by' => $request->user()->id,
            'type' => 'agreement',
            'label' => $validated['label'],
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);

        return back()->with('status', 'Document uploaded for ' . $booking->name . '.');
    }

    public function downloadDocument(Request $request, FranchiseDocument $document): Response
    {
        return response()->download(storage_path('app/' . $document->file_path), $document->original_name);
    }
}
