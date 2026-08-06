<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

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
            'radius_meters' => ['nullable', 'integer', 'min:10', 'max:2000'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ]);

        $location->update($validated);

        return back()->with('status', 'Attendance point updated.');
    }

    public function export(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'manage-attendance');

        $bookingIds = $request->user()->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id');
        $locationIds = AttendanceLocation::whereIn('franchise_booking_id', $bookingIds)->pluck('id');

        $attendances = Attendance::with(['user', 'location'])
            ->whereIn('attendance_location_id', $locationIds)
            ->latest('marked_at')
            ->get();

        $rows = ["Student,Location,Date,Punch In,Punch In Method,Punch In Lat,Punch In Lng,Punch In Device,Punch In IP,Punch Out,Punch Out Method,Punch Out Lat,Punch Out Lng,Punch Out Device,Punch Out IP,Duration (min)\n"];
        foreach ($attendances as $a) {
            $rows[] = implode(',', [
                '"' . str_replace('"', '""', $a->user->name) . '"',
                '"' . str_replace('"', '""', $a->location->name) . '"',
                $a->date->format('Y-m-d'),
                $a->marked_at->format('H:i'),
                $a->method,
                $a->latitude ?? '',
                $a->longitude ?? '',
                '"' . str_replace('"', '""', $a->device ?? '') . '"',
                $a->ip_address ?? '',
                $a->isPunchedOut() ? $a->check_out_at->format('H:i') : '',
                $a->check_out_method ?? '',
                $a->check_out_latitude ?? '',
                $a->check_out_longitude ?? '',
                '"' . str_replace('"', '""', $a->check_out_device ?? '') . '"',
                $a->check_out_ip_address ?? '',
                $a->durationMinutes() ?? '',
            ]) . "\n";
        }

        return Response::make(implode('', $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
