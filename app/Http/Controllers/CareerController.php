<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Models\User;
use App\Notifications\JobApplicationSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareerController extends Controller
{
    public function index()
    {
        $jobs = JobPosting::open()->with('course')->latest()->get();

        return view('careers', compact('jobs'));
    }

    public function show(JobPosting $career)
    {
        abort_unless($career->status === 'open', 404);

        return view('careers.show', ['job' => $career]);
    }

    public function apply(Request $request, JobPosting $career)
    {
        abort_unless($career->status === 'open', 404);

        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'cover_note' => ['nullable', 'string', 'max:1000'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $resumePath = $request->file('resume')->store('resumes', 'local');

        $application = JobApplication::create([
            'job_posting_id' => $career->id,
            'user_id' => $user?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'resume_path' => $resumePath,
            'cover_note' => $validated['cover_note'] ?? null,
            'status' => 'pending',
        ]);

        foreach (User::role('admin')->get() as $admin) {
            $admin->notify(new JobApplicationSubmittedNotification($application->load('jobPosting')));
        }

        return back()->with('status', 'Application submitted! We will get in touch if you are shortlisted.');
    }
}
