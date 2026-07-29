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

        if (Attendance::where('user_id', $user->id)->where('attendance_location_id', $location->id)->whereDate('date', now()->toDateString())->exists()) {
            return back()->with('status', 'Attendance already marked for today.');
        }

        $attendance = ['user_id' => $user->id, 'attendance_location_id' => $location->id, 'date' => now()->toDateString(), 'marked_at' => now(), 'method' => $validated['method']];

        if ($validated['method'] === 'gps') {
            $distance = $location->distanceTo((float) $validated['latitude'], (float) $validated['longitude']);

            if (!$location->isWithinRadius((float) $validated['latitude'], (float) $validated['longitude'])) {
                return back()->withErrors([
                    'location' => "You're {$distance}m away from {$location->name}. You must be within {$location->radius_meters}m to mark attendance, or use the WiFi option instead.",
                ]);
            }

            $attendance['latitude'] = $validated['latitude'];
            $attendance['longitude'] = $validated['longitude'];
            $attendance['distance_meters'] = $distance;
        } else {
            if (!$location->matchesWifi($validated['wifi_ssid'])) {
                return back()->withErrors([
                    'location' => "That WiFi network doesn't match {$location->name}'s registered network. Please check you're connected to the institute WiFi, or use GPS instead.",
                ]);
            }

            $attendance['wifi_ssid'] = $validated['wifi_ssid'];
        }

        Attendance::create($attendance);

        return redirect()->route('attendance.scan', $location->qr_token)->with('status', 'Attendance marked! See you in class.');
    }
}
