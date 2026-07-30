<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    use AuthorizesFranchiseAccess;

    public function manage(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'manage-attendance');

        $locations = AttendanceLocation::with('franchiseBooking')
            ->withCount('attendances')
            ->whereIn('franchise_booking_id', $request->user()->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id'))
            ->get();

        return view('franchise.attendance.manage', compact('locations'));
    }

    public function records(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'manage-attendance');

        $bookingIds = $request->user()->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id');

        $locations = AttendanceLocation::whereIn('franchise_booking_id', $bookingIds)->get();
        $locationId = $request->query('location');
        $date = $request->query('date');

        $attendances = Attendance::with(['user', 'location'])
            ->whereIn('attendance_location_id', $locations->pluck('id'))
            ->when($locationId, fn ($q) => $q->where('attendance_location_id', $locationId))
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->latest('marked_at')
            ->paginate(30)
            ->withQueryString();

        return view('franchise.attendance.records', compact('attendances', 'locations', 'locationId', 'date'));
    }

    public function update(Request $request, AttendanceLocation $location)
    {
        $bookingIds = $request->user()->accessibleFranchiseBookingsQuery()->pluck('id');
        abort_unless($bookingIds->contains($location->franchise_booking_id), 403);
        $this->authorizeFranchisePermission($request, $location->franchise_booking_id, 'manage-attendance');

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'wifi_ssid' => ['nullable', 'string', 'max:255'],
        ]);

        $location->update($validated);

        return back()->with('status', 'Attendance point updated.');
    }
}
