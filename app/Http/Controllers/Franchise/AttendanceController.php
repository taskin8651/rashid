<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $bookingIds = $request->user()->franchiseBookings()->where('status', 'paid')->pluck('id');

        $locations = AttendanceLocation::with('franchiseBooking')
            ->whereIn('franchise_booking_id', $bookingIds)
            ->get();

        $attendances = Attendance::with(['user', 'location'])
            ->whereIn('attendance_location_id', $locations->pluck('id'))
            ->latest('marked_at')
            ->take(50)
            ->get();

        return view('franchise.attendance', compact('locations', 'attendances'));
    }

    public function update(Request $request, AttendanceLocation $location)
    {
        $bookingIds = $request->user()->franchiseBookings()->pluck('id');
        abort_unless($bookingIds->contains($location->franchise_booking_id), 403);

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'wifi_ssid' => ['nullable', 'string', 'max:255'],
        ]);

        $location->update($validated);

        return back()->with('status', 'Attendance point updated.');
    }
}
