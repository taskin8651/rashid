<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function scan(Request $request, string $qrToken)
    {
        $location = AttendanceLocation::where('qr_token', $qrToken)->where('status', 'active')->firstOrFail();

        $today = Attendance::where('user_id', $request->user()->id)
            ->where('attendance_location_id', $location->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        return view('attendance.scan', compact('location', 'today'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'qr_token' => ['required', 'string'],
            'method' => ['required', Rule::in(['gps', 'wifi'])],
            'latitude' => ['required_if:method,gps', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['required_if:method,gps', 'nullable', 'numeric', 'between:-180,180'],
            'wifi_ssid' => ['required_if:method,wifi', 'nullable', 'string', 'max:255'],
        ]);

        $location = AttendanceLocation::where('qr_token', $validated['qr_token'])->where('status', 'active')->firstOrFail();
        $user = $request->user();

        [$verified, $error] = $this->verifyPresence($location, $validated);
        if ($error) {
            return back()->withErrors(['location' => $error]);
        }

        $today = Attendance::where('user_id', $user->id)
            ->where('attendance_location_id', $location->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        // No record yet today: this scan is a punch-in.
        if (!$today) {
            Attendance::create(array_merge([
                'user_id' => $user->id,
                'attendance_location_id' => $location->id,
                'date' => now()->toDateString(),
                'marked_at' => now(),
                'method' => $validated['method'],
                'device' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ], $verified));

            return back()->with('status', 'Punched in! Have a great session.');
        }

        // Already punched in and out: nothing left to do today.
        if ($today->isPunchedOut()) {
            return back()->with('status', 'You already punched in and out today.');
        }

        // Punched in but not out yet: this scan is the punch-out.
        $today->update(array_merge([
            'check_out_at' => now(),
            'check_out_method' => $validated['method'],
            'check_out_device' => $request->userAgent(),
            'check_out_ip_address' => $request->ip(),
        ], $this->prefixCheckOut($verified)));

        return back()->with('status', 'Punched out! Total time: ' . $today->fresh()->durationLabel() . '.');
    }

    /**
     * Confirms the scan happened at (or near) the given location, either via
     * GPS geofence or a matching WiFi network. Returns the fields to persist
     * on success, or an error message to show the student.
     */
    protected function verifyPresence(AttendanceLocation $location, array $validated): array
    {
        if ($validated['method'] === 'gps') {
            $distance = $location->distanceTo((float) $validated['latitude'], (float) $validated['longitude']);

            if (!$location->isWithinRadius((float) $validated['latitude'], (float) $validated['longitude'])) {
                return [null, "You're {$distance}m away from {$location->name}. You must be within {$location->radius_meters}m to mark attendance, or use the WiFi option instead."];
            }

            return [[
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'distance_meters' => $distance,
            ], null];
        }

        if (!$location->matchesWifi($validated['wifi_ssid'])) {
            return [null, "That WiFi network doesn't match {$location->name}'s registered network. Please check you're connected to the institute WiFi, or use GPS instead."];
        }

        return [['wifi_ssid' => $validated['wifi_ssid']], null];
    }

    protected function prefixCheckOut(array $fields): array
    {
        $out = [];
        foreach ($fields as $key => $value) {
            $out['check_out_' . $key] = $value;
        }

        return $out;
    }
}
