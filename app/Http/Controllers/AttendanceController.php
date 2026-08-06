<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'accuracy' => ['nullable', 'numeric', 'min:0'],
            'wifi_ssid' => ['required_if:method,wifi', 'nullable', 'string', 'max:255'],
        ]);

        $location = AttendanceLocation::where('qr_token', $validated['qr_token'])->where('status', 'active')->firstOrFail();
        $user = $request->user();

        [$verified, $error] = $this->verifyPresence($location, $validated);
        if ($error) {
            return back()->withErrors(['location' => $error]);
        }

        try {
            return DB::transaction(function () use ($location, $user, $validated, $verified, $request) {
                // Locks the row (if one exists) for the duration of the
                // transaction so two near-simultaneous scans from the same
                // student — a nervous double-tap on a slow connection —
                // can't both read "no record yet" and both try to create one.
                $today = Attendance::where('user_id', $user->id)
                    ->where('attendance_location_id', $location->id)
                    ->whereDate('date', now()->toDateString())
                    ->lockForUpdate()
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
            });
        } catch (QueryException $e) {
            // Both requests raced past the lock (e.g. two separate page
            // loads) and the second hit the unique constraint on insert —
            // show a friendly message instead of a raw server error.
            if ((int) $e->getCode() === 23000) {
                return back()->with('status', "You're already marked for today.");
            }

            throw $e;
        }
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

            // Phones routinely report GPS fixes accurate to only 20-80m,
            // especially indoors or in a rush at check-out time. A hard
            // cutoff at the configured radius was rejecting genuine
            // students, so the phone's own reported accuracy is allowed as
            // extra slack — capped so a spoofed/huge accuracy value can't
            // bypass the geofence entirely.
            $accuracyBuffer = min((float) ($validated['accuracy'] ?? 0), 100);

            if (!$location->isWithinRadius((float) $validated['latitude'], (float) $validated['longitude'], $accuracyBuffer)) {
                return [null, "You're {$distance}m away from {$location->name}. You must be within {$location->radius_meters}m to mark attendance, or use the WiFi option instead."];
            }

            return [[
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'distance_meters' => $distance,
                'accuracy_meters' => isset($validated['accuracy']) ? (int) round($validated['accuracy']) : null,
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
