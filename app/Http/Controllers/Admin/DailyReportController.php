<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DailyReportController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role');
        $userId = $request->query('user');
        $reviewStatus = $request->query('review_status');
        $from = $request->query('from');
        $to = $request->query('to');

        $filtered = fn () => DailyReport::query()
            ->when($role, fn ($q) => $q->whereHas('user', fn ($u) => $u->role($role)))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($reviewStatus, fn ($q) => $q->where('review_status', $reviewStatus))
            ->when($from, fn ($q) => $q->whereDate('report_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('report_date', '<=', $to));

        $reports = $filtered()
            ->with(['user', 'reviewer'])
            ->latest('report_date')
            ->paginate(30)
            ->withQueryString();

        $members = User::role(['staff', 'teacher'])->orderBy('name')->get();

        $stats = [
            'total' => $filtered()->count(),
            'pending' => $filtered()->where('review_status', 'pending')->count(),
            'approved' => $filtered()->where('review_status', 'approved')->count(),
            'rejected' => $filtered()->where('review_status', 'rejected')->count(),
        ];

        return view('admin.daily-reports.index', compact('reports', 'members', 'role', 'userId', 'reviewStatus', 'from', 'to', 'stats'));
    }

    public function approve(DailyReport $dailyReport)
    {
        $dailyReport->update([
            'review_status' => 'approved',
            'admin_comment' => null,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Report approved.');
    }

    public function reject(Request $request, DailyReport $dailyReport)
    {
        $validated = $request->validate([
            'admin_comment' => ['required', 'string'],
        ]);

        $dailyReport->update([
            'review_status' => 'rejected',
            'admin_comment' => $validated['admin_comment'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('status', 'Report rejected.');
    }

    public function export(Request $request)
    {
        $role = $request->query('role');
        $userId = $request->query('user');
        $reviewStatus = $request->query('review_status');
        $from = $request->query('from');
        $to = $request->query('to');

        $reports = DailyReport::with(['user', 'reviewer'])
            ->when($role, fn ($q) => $q->whereHas('user', fn ($u) => $u->role($role)))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($reviewStatus, fn ($q) => $q->where('review_status', $reviewStatus))
            ->when($from, fn ($q) => $q->whereDate('report_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('report_date', '<=', $to))
            ->latest('report_date')
            ->get();

        $rows = ["Name,Role,Date,Task/Subject,Hours,Status,Description,Review Status,Admin Comment,Reviewed By\n"];
        foreach ($reports as $r) {
            $rows[] = implode(',', [
                '"' . str_replace('"', '""', $r->user->name) . '"',
                $r->user->getRoleNames()->first(),
                $r->report_date->format('Y-m-d'),
                '"' . str_replace('"', '""', $r->task_subject ?? '') . '"',
                $r->hours_worked ?? '',
                $r->status,
                '"' . str_replace('"', '""', $r->description) . '"',
                $r->review_status,
                '"' . str_replace('"', '""', $r->admin_comment ?? '') . '"',
                $r->reviewer->name ?? '',
            ]) . "\n";
        }

        return Response::make(implode('', $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="daily-reports-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }
}
