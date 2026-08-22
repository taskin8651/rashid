<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CertificateIssued;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\CertificateSubject;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Notifications\CertificateApplicationRejectedNotification;
use App\Notifications\CertificateIssuedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class CertificateApplicationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $applications = CertificateApplication::with(['user', 'course.modules', 'franchiseBooking', 'certificate.subjects'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $pendingCount = CertificateApplication::where('status', 'pending')->count();
        $courses = Course::where('status', 'active')->orderBy('name')->get();

        return view('admin.certificate-applications.index', compact('applications', 'status', 'pendingCount', 'courses'));
    }

    public function certificatesIndex(Request $request)
    {
        $search = $request->query('search');

        $certificates = Certificate::with(['user', 'course', 'subjects'])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('student_name', 'like', "%{$search}%")
                    ->orWhere('student_email', 'like', "%{$search}%")
                    ->orWhere('cert_code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $courses = Course::where('status', 'active')->orderBy('name')->get();

        $totalIssued = Certificate::where('status', 'issued')->count();
        $issuedThisMonth = Certificate::where('status', 'issued')
            ->whereMonth('issued_date', now()->month)
            ->whereYear('issued_date', now()->year)
            ->count();
        $withMarksheet = Certificate::where('status', 'issued')->where('include_marksheet', true)->count();

        return view('admin.certificates.index', compact('certificates', 'courses', 'search', 'totalIssued', 'issuedThisMonth', 'withMarksheet'));
    }

    public function downloadProof(CertificateApplication $application)
    {
        abort_unless($application->proof_path, 404);

        return Storage::disk('local')->download($application->proof_path);
    }

    public function download(Certificate $certificate)
    {
        abort_unless($certificate->status === 'issued', 404);

        $filename = 'RTech-Certificate-' . $certificate->cert_code . '.pdf';
        $certificate->load(['user', 'course']);

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
            'signatureImageDataUri' => $this->signatureImageDataUri(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    public function view(Certificate $certificate)
    {
        abort_unless($certificate->status === 'issued', 404);

        $filename = 'RTech-Certificate-' . $certificate->cert_code . '.pdf';
        $certificate->load(['user', 'course']);

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
            'signatureImageDataUri' => $this->signatureImageDataUri(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    public function downloadMarksheet(Certificate $certificate)
    {
        abort_unless($certificate->status === 'issued', 404);
        abort_unless($certificate->hasMarksheetData(), 404);

        $filename = 'RTech-Marksheet-' . $certificate->cert_code . '.pdf';
        $certificate->load(['user', 'course', 'subjects']);

        $pdf = Pdf::loadView('certificates.marksheet-pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
            'signatureImageDataUri' => $this->signatureImageDataUri(),
            'studentPhotoDataUri' => $this->studentPhotoDataUri($certificate),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    public function viewMarksheet(Certificate $certificate)
    {
        abort_unless($certificate->status === 'issued', 404);
        abort_unless($certificate->hasMarksheetData(), 404);

        $filename = 'RTech-Marksheet-' . $certificate->cert_code . '.pdf';
        $certificate->load(['user', 'course', 'subjects']);

        $pdf = Pdf::loadView('certificates.marksheet-pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
            'signatureImageDataUri' => $this->signatureImageDataUri(),
            'studentPhotoDataUri' => $this->studentPhotoDataUri($certificate),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream($filename);
    }

    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'student_phone' => ['nullable', 'string', 'max:20'],
            'course_id' => ['required', 'exists:courses,id'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'course_duration_text' => ['nullable', 'string', 'max:255'],
            'roll_no' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'batch_name' => ['nullable', 'string', 'max:255'],
            'subjects' => ['nullable', 'array'],
            'subjects.*.subject' => ['nullable', 'string', 'max:255'],
            'subjects.*.max_marks' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'subjects.*.marks_obtained' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'include_certificate' => ['nullable', 'boolean'],
            'include_marksheet' => ['nullable', 'boolean'],
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $user = null;

        if (!empty($validated['student_email'])) {
            $user = User::where('email', $validated['student_email'])->first();
        }

        if (!$user) {
            $user = User::create([
                'name' => $validated['student_name'],
                'email' => $validated['student_email'] ?: 'manual-' . Str::slug($validated['student_name']) . '-' . Str::random(4) . '@example.com',
                'phone' => $validated['student_phone'] ?? null,
                'password' => Hash::make(Str::random(16)),
            ]);
            if (class_exists(\Spatie\Permission\Models\Role::class) && \Spatie\Permission\Models\Role::where('name', 'student')->exists()) {
                $user->assignRole('student');
            }
        }

        $certificate = Certificate::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'student_name' => $validated['student_name'],
            'student_email' => $validated['student_email'] ?? $user->email,
            'student_phone' => $validated['student_phone'] ?? $user->phone,
            'course_name' => $validated['course_name'] ?? $course->name,
            'course_duration_text' => $validated['course_duration_text'] ?? $course->duration_text,
            'roll_no' => $validated['roll_no'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'photo_path' => $request->hasFile('photo') ? $request->file('photo')->store('certificate-photos', 'public') : null,
            'batch_name' => $validated['batch_name'] ?? null,
            'status' => 'issued',
            'issued_date' => now(),
            'cert_code' => Certificate::generateCode(),
            'source' => 'manual',
            'include_certificate' => $request->boolean('include_certificate', true),
            'include_marksheet' => $request->boolean('include_marksheet', true),
        ]);

        $application = CertificateApplication::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'completion_date' => now(),
            'status' => 'approved',
            'certificate_id' => $certificate->id,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $enrollment = Enrollment::firstOrNew(['user_id' => $user->id, 'course_id' => $course->id]);
        if (!in_array($enrollment->status, ['paid', 'completed'], true)) {
            $enrollment->fill([
                'base_price' => $course->price,
                'discount_amount' => $course->price,
                'final_amount' => 0,
                'status' => 'completed',
                'enrolled_at' => $enrollment->enrolled_at ?? now(),
                'completed_at' => now(),
            ]);
            $enrollment->save();
        }

        if ($certificate->student_email) {
            try {
                Mail::to($certificate->student_email)->send(new CertificateIssued($certificate->fresh(['user', 'course'])));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $user->notify(new CertificateIssuedNotification($certificate->fresh(['user', 'course'])));

        return redirect()->route('admin.certificates.index')->with('status', 'Certificate created for ' . $validated['student_name'] . '.');
    }

    private function signatureImageDataUri(): string
    {
        $file = public_path('assets/img/sign.png');

        if (!is_file($file)) {
            return '';
        }

        $content = file_get_contents($file);

        return $content === false ? '' : 'data:image/png;base64,' . base64_encode($content);
    }

    private function studentPhotoDataUri(Certificate $certificate): string
    {
        if (!$certificate->photo_path || !Storage::disk('public')->exists($certificate->photo_path)) {
            return '';
        }

        $content = Storage::disk('public')->get($certificate->photo_path);
        $mime = Storage::disk('public')->mimeType($certificate->photo_path) ?: 'image/jpeg';

        return 'data:' . $mime . ';base64,' . base64_encode($content);
    }

    private function verificationQrDataUri(Certificate $certificate): string
    {
        return Builder::create()
            ->writer(new PngWriter())
            ->data(route('certificates.verify', ['code' => $certificate->cert_code]))
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build()
            ->getDataUri();
    }

    public function approve(Request $request, CertificateApplication $application)
    {
        abort_unless($application->status === 'pending', 422);

        $certificate = Certificate::firstOrNew(['user_id' => $application->user_id, 'course_id' => $application->course_id]);

        $validated = $request->validate([
            'student_name' => ['nullable', 'string', 'max:255'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'student_phone' => ['nullable', 'string', 'max:20'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'course_duration_text' => ['nullable', 'string', 'max:255'],
            'roll_no' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'batch_name' => ['nullable', 'string', 'max:255'],
            'subjects' => ['nullable', 'array'],
            'subjects.*.subject' => ['nullable', 'string', 'max:255'],
            'subjects.*.max_marks' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'subjects.*.marks_obtained' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'include_certificate' => ['nullable', 'boolean'],
            'include_marksheet' => ['nullable', 'boolean'],
        ]);

        $application->load(['user', 'course']);

        $course = $application->course;
        $user = $application->user;

        if ($application->user_id && $course) {
            $enrollment = Enrollment::firstOrNew(['user_id' => $application->user_id, 'course_id' => $application->course_id]);
            if (!in_array($enrollment->status, ['paid', 'completed'], true)) {
                $enrollment->fill([
                    'base_price' => $course->price,
                    'discount_amount' => $course->price,
                    'final_amount' => 0,
                    'status' => 'completed',
                    'enrolled_at' => $enrollment->enrolled_at ?? $application->completion_date,
                    'completed_at' => $application->completion_date,
                ]);
                $enrollment->save();
            }
        }

        $certificate->cert_code = $certificate->cert_code ?: Certificate::generateCode();
        $certificate->student_name = $validated['student_name'] ?? $user?->name ?? $certificate->student_name;
        $certificate->student_email = $validated['student_email'] ?? $user?->email ?? $certificate->student_email;
        $certificate->student_phone = $validated['student_phone'] ?? $user?->phone ?? $certificate->student_phone;
        $certificate->course_name = $validated['course_name'] ?? $course?->name ?? $certificate->course_name;
        $certificate->course_duration_text = $validated['course_duration_text'] ?? $course?->duration_text ?? $certificate->course_duration_text;
        $certificate->roll_no = $validated['roll_no'] ?? null;
        $certificate->father_name = $validated['father_name'] ?? null;
        if ($request->hasFile('photo')) {
            if ($certificate->photo_path) {
                Storage::disk('public')->delete($certificate->photo_path);
            }
            $certificate->photo_path = $request->file('photo')->store('certificate-photos', 'public');
        }
        $certificate->batch_name = $validated['batch_name'] ?? null;
        $certificate->status = 'issued';
        $certificate->issued_date = now();
        $certificate->source = $certificate->source ?: 'manual';
        $certificate->include_certificate = $request->boolean('include_certificate', true);
        $certificate->include_marksheet = $request->boolean('include_marksheet', true);
        $certificate->save();

        $this->syncSubjects($certificate, $validated['subjects'] ?? []);

        $application->update([
            'status' => 'approved',
            'certificate_id' => $certificate->id,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $certificate = $certificate->fresh(['user', 'course']);

        if ($certificate->student_email) {
            try {
                Mail::to($certificate->student_email)->send(new CertificateIssued($certificate));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($application->user) {
            $application->user->notify(new CertificateIssuedNotification($certificate));
        }

        $recipientName = $certificate->student_name ?: optional($application->user)->name ?: 'Student';

        return back()->with('status', 'Certificate issued to ' . $recipientName . '.');
    }

    public function updateDocuments(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'student_name' => ['nullable', 'string', 'max:255'],
            'student_email' => ['nullable', 'email', 'max:255'],
            'student_phone' => ['nullable', 'string', 'max:20'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'course_duration_text' => ['nullable', 'string', 'max:255'],
            'issued_date' => ['nullable', 'date'],
            'roll_no' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'batch_name' => ['nullable', 'string', 'max:255'],
            'subjects' => ['nullable', 'array'],
            'subjects.*.subject' => ['nullable', 'string', 'max:255'],
            'subjects.*.max_marks' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'subjects.*.marks_obtained' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'include_certificate' => ['nullable', 'boolean'],
            'include_marksheet' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('photo')) {
            if ($certificate->photo_path) {
                Storage::disk('public')->delete($certificate->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('certificate-photos', 'public');
        }

        $certificate->update([
            'student_name' => $validated['student_name'] ?? $certificate->student_name,
            'student_email' => $validated['student_email'] ?? null,
            'student_phone' => $validated['student_phone'] ?? null,
            'course_name' => $validated['course_name'] ?? $certificate->course_name,
            'course_duration_text' => $validated['course_duration_text'] ?? null,
            'issued_date' => $validated['issued_date'] ?? $certificate->issued_date,
            'roll_no' => $validated['roll_no'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'photo_path' => $validated['photo_path'] ?? $certificate->photo_path,
            'batch_name' => $validated['batch_name'] ?? null,
            'include_certificate' => $request->boolean('include_certificate', true),
            'include_marksheet' => $request->boolean('include_marksheet', true),
        ]);

        $this->syncSubjects($certificate, $validated['subjects'] ?? []);

        return back()->with('status', 'Certificate updated.');
    }

    private function syncSubjects(Certificate $certificate, array $rows): void
    {
        $certificate->subjects()->delete();

        $sortOrder = 0;
        foreach ($rows as $row) {
            if (empty($row['subject'])) {
                continue;
            }

            CertificateSubject::create([
                'certificate_id' => $certificate->id,
                'subject' => $row['subject'],
                'max_marks' => filled($row['max_marks'] ?? null) ? (int) $row['max_marks'] : 100,
                'marks_obtained' => filled($row['marks_obtained'] ?? null) ? (int) $row['marks_obtained'] : null,
                'sort_order' => $sortOrder++,
            ]);
        }
    }

    public function reject(Request $request, CertificateApplication $application)
    {
        abort_unless($application->status === 'pending', 422);

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $application->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $application->user->notify(new CertificateApplicationRejectedNotification($application->fresh(['course'])));

        return back()->with('status', 'Application rejected.');
    }
}
