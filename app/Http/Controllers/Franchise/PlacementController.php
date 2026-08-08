<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FranchiseBooking;
use App\Models\Placement;
use App\Models\User;
use App\Notifications\PlacementSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlacementController extends Controller
{
    use AuthorizesFranchiseAccess;

    protected function ownBookingIds(Request $request)
    {
        $user = $request->user();

        return $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'manage-placements'))
            ->values();
    }

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'manage-placements');

        $bookingIds = $this->ownBookingIds($request);

        $placements = Placement::whereIn('franchise_booking_id', $bookingIds)
            ->with(['user', 'course'])
            ->latest()
            ->get();

        $courses = Course::whereIn('franchise_booking_id', $bookingIds)->where('status', 'active')->orderBy('name')->get();

        $students = User::role('student')
            ->whereHas('enrollments', fn ($q) => $q->whereIn('status', ['paid', 'completed'])->whereHas('course', fn ($q2) => $q2->whereIn('franchise_booking_id', $bookingIds)))
            ->orderBy('name')
            ->get();

        $bookings = FranchiseBooking::whereIn('id', $bookingIds)->orderBy('city')->get(['id', 'city']);

        return view('franchise.placements', compact('placements', 'courses', 'students', 'bookings'));
    }

    public function store(Request $request)
    {
        $bookingIds = $this->ownBookingIds($request);

        $studentIds = User::role('student')
            ->whereHas('enrollments', fn ($q) => $q->whereIn('status', ['paid', 'completed'])->whereHas('course', fn ($q2) => $q2->whereIn('franchise_booking_id', $bookingIds)))
            ->pluck('id');

        $validated = $request->validate([
            'franchise_booking_id' => ['required', 'integer', Rule::in($bookingIds)],
            'user_id' => ['required', 'integer', Rule::in($studentIds)],
            'course_id' => ['nullable', 'integer', Rule::exists('courses', 'id')->where('franchise_booking_id', $request->input('franchise_booking_id'))],
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

        $this->authorizeFranchisePermission($request, (int) $validated['franchise_booking_id'], 'manage-placements');

        $offerLetterPath = $request->hasFile('offer_letter')
            ? $request->file('offer_letter')->store('placement-proofs', 'local')
            : null;

        $placement = Placement::create([
            'user_id' => $validated['user_id'],
            'course_id' => $validated['course_id'] ?? null,
            'franchise_booking_id' => $validated['franchise_booking_id'],
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
            $admin->notify(new PlacementSubmittedNotification($placement->load('user')));
        }

        return back()->with('status', 'Placement submitted for approval.');
    }

    public function destroy(Request $request, Placement $placement)
    {
        $bookingIds = $this->ownBookingIds($request);

        abort_unless($bookingIds->contains($placement->franchise_booking_id), 404);
        abort_unless($placement->status === 'pending', 422);

        $placement->delete();

        return back()->with('status', 'Placement submission removed.');
    }
}
