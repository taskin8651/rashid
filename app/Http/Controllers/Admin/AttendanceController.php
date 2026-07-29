<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $locationId = $request->query('location');
        $date = $request->query('date');

        $attendances = Attendance::with(['user', 'location'])
            ->when($locationId, fn ($q) => $q->where('attendance_location_id', $locationId))
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->latest('marked_at')
            ->paginate(30)
            ->withQueryString();

        $locations = AttendanceLocation::orderBy('name')->get();

        return view('admin.attendance.index', compact('attendances', 'locations', 'locationId', 'date'));
    }

    public function export(Request $request)
    {
        $locationId = $request->query('location');
        $date = $request->query('date');

        $attendances = Attendance::with(['user', 'location'])
            ->when($locationId, fn ($q) => $q->where('attendance_location_id', $locationId))
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->latest('marked_at')
            ->get();

        $rows = ["Student,Location,Date,Time,Method,Distance (m),WiFi\n"];
        foreach ($attendances as $a) {
            $rows[] = implode(',', [
                '"' . str_replace('"', '""', $a->user->name) . '"',
                '"' . str_replace('"', '""', $a->location->name) . '"',
                $a->date->format('Y-m-d'),
                $a->marked_at->format('H:i'),
                $a->method,
                $a->distance_meters ?? '',
                $a->wifi_ssid ?? '',
            ]) . "\n";
        }

        return Response::make(implode('', $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
