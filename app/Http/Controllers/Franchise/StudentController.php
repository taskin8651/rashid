<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Concerns\EnrollsStudentsOffline;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use App\Models\VideoProgress;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    use AuthorizesFranchiseAccess;
    use EnrollsStudentsOffline;

    protected function permittedBookingIds(Request $request, string $permission = 'view-students')
    {
        $user = $request->user();

        return $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, $permission))
            ->values();
    }

    /**
     * Confirms the given student has a paid/completed enrollment in one of
     * this franchise's own courses before letting a franchise-scoped action
     * touch them — {student} route-model-binds globally, so without this a
     * franchise could view/edit any student on the platform by URL alone.
     */
    protected function findOwnStudent(Request $request, User $student, string $permission = 'view-students'): User
    {
        abort_unless($student->hasRole('student'), 404);

        $bookingIds = $this->permittedBookingIds($request, $permission);

        $owns = Enrollment::where('user_id', $student->id)
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->exists();

        abort_unless($owns, 404);

        return $student;
    }

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');

        $user = $request->user();
        $bookingIds = $this->permittedBookingIds($request, 'view-students');

        $students = Enrollment::with(['user', 'course', 'payments'])
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->where('status', 'paid')
            ->latest('enrolled_at')
            ->get();

        $manageableBookingIds = $this->permittedBookingIds($request, 'manage-students');

        $canManage = $manageableBookingIds->isNotEmpty();
        $courses = Course::whereIn('franchise_booking_id', $manageableBookingIds)->where('status', 'active')->orderBy('name')->get();

        return view('franchise.students', compact('students', 'canManage', 'courses'));
    }

    public function show(Request $request, User $student)
    {
        $this->findOwnStudent($request, $student, 'view-students');
        $bookingIds = $this->permittedBookingIds($request, 'view-students');

        $enrollments = $student->enrollments()
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->with(['course', 'payment', 'payments'])
            ->latest('enrolled_at')
            ->get();

        $courses = $enrollments->map(function ($e) use ($student) {
            $videoIds = $e->course->videos()->where('status', 'active')->whereHas('media')->pluck('id');
            $totalVideos = $videoIds->count();
            $watched = VideoProgress::where('user_id', $student->id)
                ->whereIn('video_id', $videoIds)
                ->where('completed', true)
                ->count();
            $percent = $totalVideos > 0 ? round(($watched / $totalVideos) * 100) : 0;

            return ['enrollment' => $e, 'course' => $e->course, 'percent' => $percent, 'watched' => $watched];
        });

        $courseIds = $enrollments->pluck('course_id');
        $enrollmentIds = $enrollments->pluck('id');

        $certificates = $student->certificates()->whereIn('course_id', $courseIds)->with(['course.modules', 'subjects'])->get();
        $quizAttempts = $student->quizAttempts()->whereIn('course_id', $courseIds)->with('course')->latest('submitted_at')->get();
        $payments = Payment::where('payable_type', 'course_enrollment')->whereIn('payable_id', $enrollmentIds)->latest()->get();

        $stats = [
            'courses' => $enrollments->count(),
            'certificates' => $certificates->where('status', 'issued')->count(),
            'videos_watched' => $courses->sum('watched'),
            'total_spent' => $payments->where('status', 'paid')->sum('amount'),
        ];

        return view('franchise.students.show', compact('student', 'courses', 'certificates', 'quizAttempts', 'payments', 'stats'));
    }

    public function update(Request $request, User $student)
    {
        $this->findOwnStudent($request, $student, 'manage-students');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($student->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'guardian_name' => ['nullable', 'string', 'max:255'],
            'blood_group' => ['nullable', 'string', 'max:5'],
            'emergency_contact' => ['nullable', 'string', 'max:20'],
            'is_active' => ['boolean'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $photo = $request->hasFile('photo');
        unset($validated['photo']);

        $student->update($validated);

        if ($photo) {
            $student->addMediaFromRequest('photo')->toMediaCollection('photo');
        }

        return back()->with('status', 'Student updated.');
    }

    public function idCardView(Request $request, User $student)
    {
        return $this->renderIdCard($request, $student, inline: true);
    }

    public function idCardDownload(Request $request, User $student)
    {
        return $this->renderIdCard($request, $student, inline: false);
    }

    protected function renderIdCard(Request $request, User $student, bool $inline)
    {
        $this->findOwnStudent($request, $student, 'view-students');
        $bookingIds = $this->permittedBookingIds($request, 'view-students');

        $student->ensureStudentCode();

        $enrollment = $student->enrollments()
            ->whereIn('status', ['paid', 'completed'])
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->with('course')
            ->latest('enrolled_at')
            ->first();

        $pdf = Pdf::loadView('id-card.pdf', [
            'user' => $student,
            'course' => $enrollment?->course,
        ])->setPaper([0, 0, 153.07, 242.65]);

        $filename = 'RTech-ID-Card-' . $student->student_code . '.pdf';

        return $inline ? $pdf->stream($filename) : $pdf->download($filename);
    }

    public function export(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');

        $bookingIds = $this->permittedBookingIds($request, 'view-students');
        $courseIds = Course::whereIn('franchise_booking_id', $bookingIds)->pluck('id');

        $students = User::role('student')
            ->whereHas('enrollments', fn ($q) => $q->whereIn('course_id', $courseIds))
            ->with(['enrollments' => fn ($q) => $q->whereIn('course_id', $courseIds)])
            ->latest()
            ->get();

        $rows = ["Name,Email,Phone,Courses Enrolled,Joined,Status\n"];
        foreach ($students as $s) {
            $rows[] = implode(',', [
                '"' . str_replace('"', '""', $s->name) . '"',
                $s->email,
                $s->phone ?? '—',
                $s->enrollments->where('status', 'paid')->count(),
                $s->created_at->format('Y-m-d'),
                $s->is_active ? 'Active' : 'Inactive',
            ]) . "\n";
        }

        return Response::make(implode('', $rows), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function storeOffline(Request $request)
    {
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

        $user = $request->user();
        $bookingIds = $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'manage-students'))
            ->values();

        $course = Course::whereIn('franchise_booking_id', $bookingIds)->findOrFail($validated['course_id']);

        $student = $this->createOrFindStudent($validated);

        $this->enrollStudentOffline($student, $course, (float) $validated['total_fee'], [
            'amount' => $validated['first_payment_amount'] ?? null,
            'method' => $validated['first_payment_method'] ?? null,
            'note' => $validated['first_payment_note'] ?? null,
        ], $request->user()->id);

        return back()->with('status', 'Student registered and allotted to ' . $course->name . '.');
    }

    public function allotCourse(Request $request, User $student)
    {
        abort_unless($student->hasRole('student'), 404);

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'total_fee' => ['required', 'numeric', 'min:0'],
            'first_payment_amount' => ['nullable', 'numeric', 'min:0.01'],
            'first_payment_method' => ['nullable', 'string', 'max:30'],
            'first_payment_note' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();
        $bookingIds = $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'manage-students'))
            ->values();

        $course = Course::whereIn('franchise_booking_id', $bookingIds)->findOrFail($validated['course_id']);

        $this->enrollStudentOffline($student, $course, (float) $validated['total_fee'], [
            'amount' => $validated['first_payment_amount'] ?? null,
            'method' => $validated['first_payment_method'] ?? null,
            'note' => $validated['first_payment_note'] ?? null,
        ], $request->user()->id);

        return back()->with('status', $course->name . ' allotted to ' . $student->name . '.');
    }

    public function storePayment(Request $request, Enrollment $enrollment)
    {
        $this->authorizeFranchisePermission($request, $enrollment->course->franchise_booking_id ?? 0, 'manage-students');

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:255'],
            'paid_at' => ['nullable', 'date'],
        ]);

        if ($validated['amount'] > $enrollment->balance_due + 0.01) {
            return back()->withErrors(['amount' => 'Amount exceeds the remaining balance of ₹' . number_format($enrollment->balance_due, 2) . '.']);
        }

        Payment::create([
            'payable_type' => 'course_enrollment',
            'payable_id' => $enrollment->id,
            'user_id' => $enrollment->user_id,
            'amount' => $validated['amount'],
            'method' => $validated['method'],
            'note' => $validated['note'] ?? null,
            'recorded_by' => $request->user()->id,
            'paid_at' => $validated['paid_at'] ?? now(),
            'status' => 'paid',
        ]);

        return back()->with('status', 'Payment recorded.');
    }

    public function updateFee(Request $request, Enrollment $enrollment)
    {
        $this->authorizeFranchisePermission($request, $enrollment->course->franchise_booking_id ?? 0, 'manage-students');

        $validated = $request->validate([
            'total_fee' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validated['total_fee'] < $enrollment->amount_paid) {
            return back()->withErrors(['total_fee' => 'Total fee cannot be less than the amount already paid (₹' . number_format($enrollment->amount_paid, 2) . ').']);
        }

        $enrollment->update([
            'final_amount' => $validated['total_fee'],
            'discount_amount' => max(0, $enrollment->base_price - $validated['total_fee']),
        ]);

        return back()->with('status', 'Fee updated.');
    }
}
