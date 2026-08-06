<?php

namespace App\Http\Controllers\Admin;

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
    use EnrollsStudentsOffline;

    public function index(Request $request)
    {
        $query = User::role('student')->with(['enrollments.course']);

        if ($search = $request->query('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        }

        $students = $query->latest()->paginate(20)->withQueryString();
        $courses = Course::where('status', 'active')->orderBy('name')->get();

        return view('admin.students.index', compact('students', 'search', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $student = User::create($validated);
        $student->assignRole('student');

        return back()->with('status', $student->name . ' added. Allot them a course whenever they\'re ready.');
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

        $course = Course::findOrFail($validated['course_id']);

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

        $course = Course::findOrFail($validated['course_id']);

        $this->enrollStudentOffline($student, $course, (float) $validated['total_fee'], [
            'amount' => $validated['first_payment_amount'] ?? null,
            'method' => $validated['first_payment_method'] ?? null,
            'note' => $validated['first_payment_note'] ?? null,
        ], $request->user()->id);

        return back()->with('status', $course->name . ' allotted to ' . $student->name . '.');
    }

    public function storePayment(Request $request, Enrollment $enrollment)
    {
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

    public function export()
    {
        $students = User::role('student')->with('enrollments')->latest()->get();

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

    public function show(User $student)
    {
        abort_unless($student->hasRole('student'), 404);

        $enrollments = $student->enrollments()->with(['course', 'payment', 'payments'])->latest('enrolled_at')->get();

        $courses = $enrollments->map(function ($e) use ($student) {
            $totalVideos = $e->course->videos()->where('status', 'active')->whereHas('media')->count();
            $watched = VideoProgress::where('user_id', $student->id)
                ->whereIn('video_id', $e->course->videos()->where('status', 'active')->whereHas('media')->pluck('id'))
                ->where('completed', true)
                ->count();
            $percent = $totalVideos > 0 ? round(($watched / $totalVideos) * 100) : 0;

            return ['enrollment' => $e, 'course' => $e->course, 'percent' => $percent];
        });

        $certificates = $student->certificates()->with(['course.modules', 'subjects'])->get();
        $quizAttempts = $student->quizAttempts()->with('course')->latest('submitted_at')->get();
        $payments = Payment::where('user_id', $student->id)->latest()->get();

        $stats = [
            'courses' => $enrollments->count(),
            'certificates' => $certificates->where('status', 'issued')->count(),
            'videos_watched' => $student->videoProgress()->where('completed', true)->count(),
            'total_spent' => $payments->where('status', 'paid')->sum('amount'),
        ];

        return view('admin.students.show', compact('student', 'courses', 'certificates', 'quizAttempts', 'payments', 'stats'));
    }

    public function idCardView(User $student)
    {
        return $this->renderIdCard($student, inline: true);
    }

    public function idCardDownload(User $student)
    {
        return $this->renderIdCard($student, inline: false);
    }

    protected function renderIdCard(User $student, bool $inline)
    {
        abort_unless($student->hasRole('student'), 404);

        $student->ensureStudentCode();

        $enrollment = $student->enrollments()->whereIn('status', ['paid', 'completed'])->with('course')->latest('enrolled_at')->first();

        $pdf = Pdf::loadView('id-card.pdf', [
            'user' => $student,
            'course' => $enrollment?->course,
        ])->setPaper([0, 0, 153.07, 242.65]);

        $filename = 'RTech-ID-Card-' . $student->student_code . '.pdf';

        return $inline ? $pdf->stream($filename) : $pdf->download($filename);
    }

    public function update(Request $request, User $student)
    {
        abort_unless($student->hasRole('student'), 404);

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

    public function toggleFeature(User $student)
    {
        abort_unless($student->hasRole('student'), 404);

        $student->update(['is_featured_student' => !$student->is_featured_student]);

        return back()->with('status', $student->is_featured_student
            ? $student->name . ' is now shown on the website.'
            : $student->name . ' removed from the website.');
    }

    public function destroy(User $student)
    {
        $student->delete();
        return back()->with('status', 'Student deleted.');
    }
}
