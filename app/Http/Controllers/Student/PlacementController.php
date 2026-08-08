<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Placement;
use App\Models\User;
use App\Notifications\PlacementSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlacementController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $placements = $user->placements()->with('course')->latest()->get();

        $courses = $user->enrollments()
            ->whereIn('status', ['paid', 'completed'])
            ->with('course')
            ->get()
            ->pluck('course')
            ->filter()
            ->unique('id')
            ->values();

        return view('student.placements', compact('placements', 'courses'));
    }

    public function store(Request $request)
    {
        $courseIds = $request->user()->enrollments()->whereIn('status', ['paid', 'completed'])->pluck('course_id');

        $validated = $request->validate([
            'course_id' => ['nullable', 'integer', Rule::in($courseIds)],
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', Rule::in(['Full-time', 'Part-time', 'Internship', 'Freelance'])],
            'work_mode' => ['nullable', 'string', Rule::in(['Onsite', 'Remote', 'Hybrid'])],
            'location' => ['nullable', 'string', 'max:255'],
            'package' => ['nullable', 'string', 'max:100'],
            'joining_date' => ['nullable', 'date'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'testimonial' => ['nullable', 'string', 'max:1000'],
            'offer_letter' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $offerLetterPath = $request->hasFile('offer_letter')
            ? $request->file('offer_letter')->store('placement-proofs', 'local')
            : null;

        $placement = Placement::create([
            'user_id' => $request->user()->id,
            'course_id' => $validated['course_id'] ?? null,
            'submitted_by' => $request->user()->id,
            'company_name' => $validated['company_name'],
            'job_title' => $validated['job_title'],
            'job_type' => $validated['job_type'] ?? null,
            'work_mode' => $validated['work_mode'] ?? null,
            'location' => $validated['location'] ?? null,
            'package' => $validated['package'] ?? null,
            'joining_date' => $validated['joining_date'] ?? null,
            'offer_letter_path' => $offerLetterPath,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'testimonial' => $validated['testimonial'] ?? null,
            'status' => 'pending',
        ]);

        foreach (User::role('admin')->get() as $admin) {
            $admin->notify(new PlacementSubmittedNotification($placement));
        }

        return back()->with('status', 'Placement submitted! We will review it and publish it on the website once approved.');
    }

    public function destroy(Request $request, Placement $placement)
    {
        abort_unless($placement->user_id === $request->user()->id, 403);
        abort_unless($placement->status === 'pending', 422);

        $placement->delete();

        return back()->with('status', 'Placement submission withdrawn.');
    }
}
