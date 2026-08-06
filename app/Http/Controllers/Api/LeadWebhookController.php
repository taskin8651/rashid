<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Concerns\CapturesLeads;
use App\Http\Controllers\Controller;
use App\Models\FranchiseBooking;
use Illuminate\Http\Request;

class LeadWebhookController extends Controller
{
    use CapturesLeads;

    /**
     * Generic inbound-lead webhook — point any external tool (Zapier, Make,
     * a WhatsApp Business provider like Interakt/WATI, Meta Lead Ads via
     * Zapier, etc.) at this URL and it'll land in this franchise's leads.
     * Field names are read flexibly since different tools use different
     * keys for the same thing.
     */
    public function store(Request $request, string $token)
    {
        $booking = FranchiseBooking::where('lead_form_token', $token)->where('status', 'paid')->firstOrFail();

        $phone = $request->input('phone')
            ?? $request->input('phone_number')
            ?? $request->input('whatsapp_number')
            ?? $request->input('mobile');

        if (blank($phone)) {
            return response()->json(['message' => 'A phone number is required.'], 422);
        }

        $lead = $this->captureLead([
            'name' => $request->input('name') ?? $request->input('full_name') ?? 'WhatsApp Lead',
            'phone' => $phone,
            'email' => $request->input('email'),
            'course_id' => null,
            'message' => $request->input('message') ?? $request->input('note') ?? $request->input('comment'),
        ], $booking, 'social_media');

        return response()->json(['status' => 'ok', 'lead_id' => $lead->id], 201);
    }
}
