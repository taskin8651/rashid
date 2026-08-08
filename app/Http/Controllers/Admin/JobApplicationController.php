<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\JobApplicationStatusUpdated;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class JobApplicationController extends Controller
{
    public function index(Request $request)
    {
        $jobId = $request->query('job');
        $status = $request->query('status');

        $applications = JobApplication::with(['jobPosting', 'user'])
            ->when($jobId, fn ($q) => $q->where('job_posting_id', $jobId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $jobs = JobPosting::orderBy('title')->get();

        return view('admin.careers.applications', compact('applications', 'jobs', 'jobId', 'status'));
    }

    public function updateStatus(Request $request, JobApplication $application)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'shortlisted', 'rejected', 'hired'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $application->admin_notes,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        try {
            Mail::to($application->email)->send(new JobApplicationStatusUpdated($application->fresh('jobPosting')));
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('status', 'Application status updated.');
    }

    public function downloadResume(JobApplication $application)
    {
        $extension = pathinfo($application->resume_path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($application->resume_path, $application->name . ' - Resume.' . $extension);
    }

    public function destroy(JobApplication $application)
    {
        $application->delete();

        return back()->with('status', 'Application removed.');
    }
}
