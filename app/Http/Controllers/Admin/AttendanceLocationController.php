<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLocation;
use App\Models\FranchiseBooking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceLocationController extends Controller
{
    public function index()
    {
        $locations = AttendanceLocation::with('franchiseBooking')->withCount('attendances')->latest()->get();
        $franchises = FranchiseBooking::where('status', 'paid')
            ->whereDoesntHave('attendanceLocation')
            ->orderBy('city')
            ->get(['id', 'city']);

        return view('admin.attendance-locations.index', compact('locations', 'franchises'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'franchise_booking_id' => ['nullable', 'exists:franchise_bookings,id', 'unique:attendance_locations,franchise_booking_id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:20', 'max:2000'],
            'wifi_ssid' => ['nullable', 'string', 'max:255'],
        ]);

        AttendanceLocation::create($validated);

        return back()->with('status', 'Attendance location created.');
    }

    public function update(Request $request, AttendanceLocation $location)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meters' => ['required', 'integer', 'min:20', 'max:2000'],
            'wifi_ssid' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $location->update($validated);

        return back()->with('status', 'Attendance location updated.');
    }

    public function destroy(AttendanceLocation $location)
    {
        $location->delete();

        return back()->with('status', 'Attendance location deleted.');
    }
}
