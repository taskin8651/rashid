<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Placement;
use App\Notifications\PlacementApprovedNotification;
use App\Notifications\PlacementRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlacementController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $placements = Placement::with(['user', 'course', 'franchiseBooking'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount = Placement::where('status', 'pending')->count();
        $courses = Course::where('status', 'active')->orderBy('name')->get();

        return view('admin.placements.index', compact('placements', 'status', 'pendingCount', 'courses'));
    }

    public function approve(Request $request, Placement $placement)
    {
        abort_unless($placement->status === 'pending', 422);

        $this->applyDetails($request, $placement);

        $placement->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $placement->user?->notify(new PlacementApprovedNotification($placement->fresh()));

        return back()->with('status', 'Placement approved and published.');
    }

    public function reject(Request $request, Placement $placement)
    {
        abort_unless($placement->status === 'pending', 422);

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $placement->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $placement->user?->notify(new PlacementRejectedNotification($placement));

        return back()->with('status', 'Placement rejected.');
    }

    public function update(Request $request, Placement $placement)
    {
        $this->applyDetails($request, $placement);
        $placement->save();

        return back()->with('status', 'Placement updated.');
    }

    public function toggleFeatured(Placement $placement)
    {
        $placement->update(['is_featured' => !$placement->is_featured]);

        return back()->with('status', $placement->is_featured ? 'Placement featured on homepage.' : 'Placement unfeatured.');
    }

    public function downloadProof(Placement $placement)
    {
        abort_unless($placement->offer_letter_path, 404);

        return Storage::disk('local')->download($placement->offer_letter_path);
    }

    public function destroy(Placement $placement)
    {
        $placement->delete();

        return back()->with('status', 'Placement removed.');
    }

    private function applyDetails(Request $request, Placement $placement): void
    {
        $validated = $request->validate([
            'course_id' => ['nullable', 'exists:courses,id'],
            'company_name' => ['required', 'string', 'max:255'],
            'job_title' => ['required', 'string', 'max:255'],
            'job_type' => ['nullable', 'string', 'max:50'],
            'work_mode' => ['nullable', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'package' => ['nullable', 'string', 'max:100'],
            'joining_date' => ['nullable', 'date'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'testimonial' => ['nullable', 'string', 'max:1000'],
        ]);

        $placement->fill([
            'course_id' => $validated['course_id'] ?? null,
            'company_name' => $validated['company_name'],
            'job_title' => $validated['job_title'],
            'job_type' => $validated['job_type'] ?? null,
            'work_mode' => $validated['work_mode'] ?? null,
            'location' => $validated['location'] ?? null,
            'package' => $validated['package'] ?? null,
            'joining_date' => $validated['joining_date'] ?? null,
            'linkedin_url' => $validated['linkedin_url'] ?? null,
            'testimonial' => $validated['testimonial'] ?? null,
        ]);
    }
}
