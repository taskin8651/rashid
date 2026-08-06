<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\EnrollsStudentsOffline;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FranchiseBooking;
use App\Models\StudentLead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    use EnrollsStudentsOffline;

    public function index(Request $request)
    {
        $query = StudentLead::with(['course', 'franchiseBooking', 'assignedTo']);

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $leads = $query->latest()->paginate(20)->withQueryString();

        $counts = StudentLead::selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status');
        $courses = Course::where('status', 'active')->orderBy('name')->get();
        $bookings = FranchiseBooking::where('status', 'paid')->orderBy('city')->get();
        $bookings->each->ensureLeadFormToken();
        $staff = User::role('admin')->orderBy('name')->get();

        return view('admin.leads.index', [
            'leads' => $leads,
            'counts' => $counts,
            'courses' => $courses,
            'bookings' => $bookings,
            'staff' => $staff,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function show(StudentLead $lead)
    {
        $lead->load(['course', 'franchiseBooking', 'assignedTo', 'notes.author', 'convertedEnrollment.course']);
        $courses = Course::where('status', 'active')->orderBy('name')->get();
        $staff = User::role('admin')->orderBy('name')->get();

        return view('admin.leads.show', compact('lead', 'courses', 'staff'));
    }

    public function export()
    {
        $leads = StudentLead::with(['course', 'franchiseBooking'])->latest()->get();

        $rows = ["Name,Phone,Email,Course,Franchise,Source,Status,Received\n"];
        foreach ($leads as $l) {
            $rows[] = implode(',', [
                '"' . str_replace('"', '""', $l->name) . '"',
                $l->phone,
                $l->email ?? '—',
                '"' . str_replace('"', '""', $l->course->name ?? '—') . '"',
                '"' . str_replace('"', '""', $l->franchiseBooking->city ?? 'Head Office') . '"',
                $l->source,
                $l->status,
                $l->created_at->format('Y-m-d'),
            ]) . "\n";
        }

        return Response::make(implode('', $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="leads-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'franchise_booking_id' => ['nullable', 'exists:franchise_bookings,id'],
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
        abort_if($lead->status === 'converted', 422, 'This lead is already converted.');

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

        $course = Course::findOrFail($validated['course_id']);

        $student = $this->createOrFindStudent($validated);

        $enrollment = $this->enrollStudentOffline($student, $course, (float) $validated['total_fee'], [
            'amount' => $validated['first_payment_amount'] ?? null,
            'method' => $validated['first_payment_method'] ?? null,
            'note' => $validated['first_payment_note'] ?? null,
        ], $request->user()->id);

        $lead->update(['status' => 'converted', 'converted_enrollment_id' => $enrollment->id]);

        return back()->with('status', 'Lead converted — ' . $student->name . ' enrolled in ' . $course->name . '.');
    }

    public function destroy(StudentLead $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }
}
