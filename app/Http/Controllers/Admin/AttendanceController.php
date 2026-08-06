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

        $filtered = fn () => Attendance::query()
            ->when($locationId, fn ($q) => $q->where('attendance_location_id', $locationId))
            ->when($date, fn ($q) => $q->whereDate('date', $date));

        $attendances = $filtered()
            ->with(['user', 'location'])
            ->latest('marked_at')
            ->paginate(30)
            ->withQueryString();

        $locations = AttendanceLocation::orderBy('name')->get();

        $stats = [
            'total' => $filtered()->count(),
            'still_in' => $filtered()->whereNull('check_out_at')->count(),
            'completed' => $filtered()->whereNotNull('check_out_at')->count(),
            'avg_duration_minutes' => $filtered()->whereNotNull('check_out_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, marked_at, check_out_at)) as avg_min')
                ->value('avg_min'),
        ];

        return view('admin.attendance.index', compact('attendances', 'locations', 'locationId', 'date', 'stats'));
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
