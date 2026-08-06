<?php

namespace App\Http\Controllers\Concerns;

use App\Models\FranchiseBooking;
use App\Models\StudentLead;

trait CapturesLeads
{
    /**
     * Turns an inbound enquiry (public form or webhook) into a StudentLead.
     * If the same phone number already has an open lead for this franchise,
     * that's appended to instead of duplicated — webhook retries and repeat
     * ad-clicks are common, and staff don't want the same person listed
     * twice.
     */
    protected function captureLead(array $data, ?FranchiseBooking $booking, string $source): StudentLead
    {
        $existing = StudentLead::where('phone', $data['phone'])
            ->where('franchise_booking_id', $booking?->id)
            ->whereNotIn('status', ['converted', 'lost'])
            ->latest()
            ->first();

        if ($existing) {
            $existing->touch();

            $existing->notes()->create([
                'note' => 'Submitted the enquiry form again' . (filled($data['message'] ?? null) ? ": {$data['message']}" : '.'),
                'created_by' => null,
            ]);

            return $existing;
        }

        $lead = StudentLead::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'course_id' => $data['course_id'] ?? null,
            'franchise_booking_id' => $booking?->id,
            'source' => $source,
            'status' => 'new',
            'created_by' => null,
        ]);

        if (filled($data['message'] ?? null)) {
            $lead->notes()->create([
                'note' => $data['message'],
                'created_by' => null,
            ]);
        }

        return $lead;
    }
}
