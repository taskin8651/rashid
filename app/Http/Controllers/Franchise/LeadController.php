<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Concerns\EnrollsStudentsOffline;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FranchiseBooking;
use App\Models\FranchiseTeamMember;
use App\Models\StudentLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    use AuthorizesFranchiseAccess;
    use EnrollsStudentsOffline;

    protected function bookingIdsWithAnyOf(Request $request, array $permissions)
    {
        $user = $request->user();

        return $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => collect($permissions)->contains(fn ($p) => $user->hasFranchisePermission($id, $p)))
            ->values();
    }

    /**
     * Bookings the user can fully manage leads on — create, edit, convert,
     * delete. Narrower than viewableBookingIds(), which also includes
     * bookings where they only hold the lighter follow-up-leads permission.
     */
    protected function manageableBookingIds(Request $request)
    {
        return $this->bookingIdsWithAnyOf($request, ['manage-leads']);
    }

    protected function viewableBookingIds(Request $request)
    {
        return $this->bookingIdsWithAnyOf($request, ['manage-leads', 'follow-up-leads']);
    }

    protected function staffFor($bookingIds)
    {
        $ownerIds = FranchiseBooking::whereIn('id', $bookingIds)->pluck('user_id')->filter();
        $memberIds = FranchiseTeamMember::whereIn('franchise_booking_id', $bookingIds)->pluck('user_id');

        return User::whereIn('id', $ownerIds->merge($memberIds)->unique())->orderBy('name')->get();
    }

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermissions($request, ['manage-leads', 'follow-up-leads']);

        $viewableIds = $this->viewableBookingIds($request);
        $manageableIds = $this->manageableBookingIds($request);

        $query = StudentLead::with(['course', 'assignedTo'])->whereIn('franchise_booking_id', $viewableIds);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        $counts = StudentLead::whereIn('franchise_booking_id', $viewableIds)
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');

        $canManage = $manageableIds->isNotEmpty();
        $courses = Course::whereIn('franchise_booking_id', $manageableIds)->where('status', 'active')->orderBy('name')->get();
        $bookings = $request->user()->accessibleFranchiseBookingsQuery()->whereIn('id', $manageableIds)->get();
        $staff = $this->staffFor($manageableIds);

        return view('franchise.leads.index', [
            'leads' => $leads,
            'counts' => $counts,
            'courses' => $courses,
            'bookings' => $bookings,
            'staff' => $staff,
            'canManage' => $canManage,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(Request $request, StudentLead $lead)
    {
        $this->authorizeFranchiseAnyOf($request, $lead->franchise_booking_id ?? 0, ['manage-leads', 'follow-up-leads']);

        $lead->load(['course', 'franchiseBooking', 'assignedTo', 'notes.author', 'convertedEnrollment.course']);

        $canManage = $request->user()->hasFranchisePermission($lead->franchise_booking_id ?? 0, 'manage-leads');
        $bookingIds = [$lead->franchise_booking_id];
        $courses = Course::whereIn('franchise_booking_id', $bookingIds)->where('status', 'active')->orderBy('name')->get();
        $staff = $this->staffFor($bookingIds);

        return view('franchise.leads.show', compact('lead', 'canManage', 'courses', 'staff'));
    }

    public function store(Request $request)
    {
        $bookingIds = $this->manageableBookingIds($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'franchise_booking_id' => ['required', Rule::in($bookingIds->all())],
            'source' => ['required', Rule::in(StudentLead::SOURCES)],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'next_follow_up_date' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $note = $validated['note'] ?? null;
        unset($validated['note']);
        $validated['created_by'] = $request->user()->id;

        $lead = StudentLead::create($validated);

        if ($note) {
            $lead->notes()->create(['note' => $note, 'created_by' => $request->user()->id]);
        }

        return back()->with('status', 'Lead added.');
    }

    public function update(Request $request, StudentLead $lead)
    {
        $this->authorizeFranchisePermission($request, $lead->franchise_booking_id ?? 0, 'manage-leads');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'next_follow_up_date' => ['nullable', 'date'],
        ]);

        $lead->update($validated);

        return back()->with('status', 'Lead updated.');
    }

    public function updateStatus(Request $request, StudentLead $lead)
    {
        $this->authorizeFranchiseAnyOf($request, $lead->franchise_booking_id ?? 0, ['manage-leads', 'follow-up-leads']);

        $validated = $request->validate([
            'status' => ['required', Rule::in(StudentLead::STATUSES)],
            'lost_reason' => ['nullable', 'string', 'max:255'],
        ]);

        abort_if($validated['status'] === 'converted', 422, 'Use the Convert action to mark a lead converted.');

        $lead->update([
            'status' => $validated['status'],
            'lost_reason' => $validated['status'] === 'lost' ? ($validated['lost_reason'] ?? null) : null,
        ]);

        return back()->with('status', 'Lead status updated.');
    }

    public function addNote(Request $request, StudentLead $lead)
    {
        $this->authorizeFranchiseAnyOf($request, $lead->franchise_booking_id ?? 0, ['manage-leads', 'follow-up-leads']);

        $validated = $request->validate([
            'note' => ['required', 'string', 'max:1000'],
            'next_follow_up_date' => ['nullable', 'date'],
        ]);

        $lead->notes()->create(['note' => $validated['note'], 'created_by' => $request->user()->id]);

        $lead->update([
            'next_follow_up_date' => $validated['next_follow_up_date'] ?? $lead->next_follow_up_date,
            'status' => $lead->status === 'new' ? 'contacted' : $lead->status,
        ]);

        return back()->with('status', 'Note added.');
    }

    public function convert(Request $request, StudentLead $lead)
    {
        $this->authorizeFranchisePermission($request, $lead->franchise_booking_id ?? 0, 'manage-leads');

        abort_if($lead->status === 'converted', 422, 'This lead is already converted.');

        $bookingIds = $this->manageableBookingIds($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6'],
            'course_id' => ['required', 'exists:courses,id'],
            'total_fee' => ['required', 'numeric', 'min:0'],
            'first_payment_amount' => ['nullable', 'numeric', 'min:0.01'],
            'first_payment_method' => ['nullable', 'string', 'max:30'],
            'first_payment_note' => ['nullable', 'string', 'max:255'],
        ]);

        $course = Course::whereIn('franchise_booking_id', $bookingIds)->findOrFail($validated['course_id']);

        $student = $this->createOrFindStudent($validated);

        $enrollment = $this->enrollStudentOffline($student, $course, (float) $validated['total_fee'], [
            'amount' => $validated['first_payment_amount'] ?? null,
            'method' => $validated['first_payment_method'] ?? null,
            'note' => $validated['first_payment_note'] ?? null,
        ], $request->user()->id);

        $lead->update(['status' => 'converted', 'converted_enrollment_id' => $enrollment->id]);

        return back()->with('status', 'Lead converted — ' . $student->name . ' enrolled in ' . $course->name . '.');
    }

    public function destroy(Request $request, StudentLead $lead)
    {
        $this->authorizeFranchisePermission($request, $lead->franchise_booking_id ?? 0, 'manage-leads');

        $lead->delete();

        return redirect()->route('franchise.leads.index')->with('status', 'Lead deleted.');
    }
}
