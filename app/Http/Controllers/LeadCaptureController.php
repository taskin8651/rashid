<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\CapturesLeads;
use App\Models\Course;
use App\Models\FranchiseBooking;
use Illuminate\Http\Request;

class LeadCaptureController extends Controller
{
    use CapturesLeads;

    public function show(Request $request, ?string $token = null)
    {
        $booking = $token
            ? FranchiseBooking::where('lead_form_token', $token)->where('status', 'paid')->firstOrFail()
            : null;

        $courses = Course::where('status', 'active')
            ->when($booking, fn ($q) => $q->where('franchise_booking_id', $booking->id))
            ->orderBy('name')
            ->get();

        return view('lead-capture', compact('booking', 'courses'));
    }

    public function store(Request $request)
    {
        abort_if($request->filled('website'), 422);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'message' => ['nullable', 'string', 'max:1000'],
            'token' => ['nullable', 'string'],
        ]);

        $booking = $request->filled('token')
            ? FranchiseBooking::where('lead_form_token', $validated['token'])->where('status', 'paid')->first()
            : null;

        $this->captureLead($validated, $booking, 'website');

        return back()->with('status', "Thanks {$validated['name']}! Our team will reach out to you shortly.");
    }
}
