<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class DailyReportController extends Controller
{
    protected function portal(Request $request): string
    {
        return $request->user()->hasRole('teacher') ? 'teacher' : 'staff';
    }

    public function index(Request $request)
    {
        $reports = $request->user()->dailyReports()->latest('report_date')->paginate(20);

        $stats = [
            'this_month' => $request->user()->dailyReports()->whereMonth('report_date', now()->month)->whereYear('report_date', now()->year)->count(),
            'pending' => $request->user()->dailyReports()->where('review_status', 'pending')->count(),
        ];

        return view($this->portal($request) . '.reports.index', compact('reports', 'stats'));
    }

    public function create(Request $request)
    {
        return view($this->portal($request) . '.reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'report_date' => [
                'required', 'date', 'before_or_equal:today',
                Rule::unique('daily_reports')->where('user_id', $request->user()->id),
            ],
            'task_subject' => ['nullable', 'string', 'max:255'],
            'hours_worked' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'status' => ['required', Rule::in(['present', 'absent', 'leave'])],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ], [
            'report_date.unique' => 'You already submitted a report for that date.',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('daily-report-attachments');
            $validated['attachment_original_name'] = $request->file('attachment')->getClientOriginalName();
        }

        $request->user()->dailyReports()->create($validated);

        return redirect()->route($this->portal($request) . '.reports.index')->with('status', 'Report submitted.');
    }

    public function edit(Request $request, DailyReport $dailyReport)
    {
        abort_unless($dailyReport->user_id === $request->user()->id, 404);
        abort_unless($dailyReport->isEditable(), 403, 'This report has already been reviewed and can no longer be edited.');

        return view($this->portal($request) . '.reports.edit', compact('dailyReport'));
    }

    public function update(Request $request, DailyReport $dailyReport)
    {
        abort_unless($dailyReport->user_id === $request->user()->id, 404);
        abort_unless($dailyReport->isEditable(), 403, 'This report has already been reviewed and can no longer be edited.');

        $validated = $request->validate([
            'report_date' => [
                'required', 'date', 'before_or_equal:today',
                Rule::unique('daily_reports')->where('user_id', $request->user()->id)->ignore($dailyReport->id),
            ],
            'task_subject' => ['nullable', 'string', 'max:255'],
            'hours_worked' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'status' => ['required', Rule::in(['present', 'absent', 'leave'])],
            'description' => ['required', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ], [
            'report_date.unique' => 'You already submitted a report for that date.',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('daily-report-attachments');
            $validated['attachment_original_name'] = $request->file('attachment')->getClientOriginalName();
        }

        $dailyReport->update($validated);

        return redirect()->route($this->portal($request) . '.reports.index')->with('status', 'Report updated.');
    }

    public function download(Request $request, DailyReport $dailyReport): Response
    {
        abort_unless($dailyReport->user_id === $request->user()->id, 404);
        abort_unless($dailyReport->attachment_path, 404);

        return response()->download(storage_path('app/' . $dailyReport->attachment_path), $dailyReport->attachment_original_name);
    }
}
