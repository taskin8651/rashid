<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CertificateIssued;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use App\Models\CertificateSubject;
use App\Models\Enrollment;
use App\Notifications\CertificateApplicationRejectedNotification;
use App\Notifications\CertificateIssuedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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

        return view('admin.certificate-applications.index', compact('applications', 'status', 'pendingCount'));
    }

    public function downloadProof(CertificateApplication $application)
    {
        abort_unless($application->proof_path, 404);

        return Storage::disk('local')->download($application->proof_path);
    }

    public function approve(Request $request, CertificateApplication $application)
    {
        abort_unless($application->status === 'pending', 422);

        $certificate = Certificate::firstOrNew(['user_id' => $application->user_id, 'course_id' => $application->course_id]);

        $validated = $request->validate([
            'roll_no' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'batch_name' => ['nullable', 'string', 'max:255'],
            'subjects' => ['nullable', 'array'],
            'subjects.*.subject' => ['nullable', 'string', 'max:255'],
            'subjects.*.max_marks' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'subjects.*.marks_obtained' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $application->load(['user', 'course']);

        $enrollment = Enrollment::firstOrNew(['user_id' => $application->user_id, 'course_id' => $application->course_id]);
        if (!in_array($enrollment->status, ['paid', 'completed'], true)) {
            $enrollment->fill([
                'base_price' => $application->course->price,
                'discount_amount' => $application->course->price,
                'final_amount' => 0,
                'status' => 'completed',
                'enrolled_at' => $enrollment->enrolled_at ?? $application->completion_date,
                'completed_at' => $application->completion_date,
            ]);
            $enrollment->save();
        }

        $certificate->cert_code = $certificate->cert_code ?: Certificate::generateCode();
        $certificate->roll_no = $validated['roll_no'] ?? null;
        $certificate->father_name = $validated['father_name'] ?? null;
        $certificate->batch_name = $validated['batch_name'] ?? null;
        $certificate->status = 'issued';
        $certificate->issued_date = now();
        $certificate->save();

        $this->syncSubjects($certificate, $validated['subjects'] ?? []);

        $application->update([
            'status' => 'approved',
            'certificate_id' => $certificate->id,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $certificate = $certificate->fresh(['user', 'course']);

        try {
            Mail::to($application->user->email)->send(new CertificateIssued($certificate));
        } catch (\Throwable $e) {
            report($e);
        }

        $application->user->notify(new CertificateIssuedNotification($certificate));

        return back()->with('status', 'Certificate issued to ' . $application->user->name . '.');
    }

    public function updateDocuments(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'roll_no' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'batch_name' => ['nullable', 'string', 'max:255'],
            'subjects' => ['nullable', 'array'],
            'subjects.*.subject' => ['nullable', 'string', 'max:255'],
            'subjects.*.max_marks' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'subjects.*.marks_obtained' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        $certificate->update([
            'roll_no' => $validated['roll_no'] ?? null,
            'father_name' => $validated['father_name'] ?? null,
            'batch_name' => $validated['batch_name'] ?? null,
        ]);

        $this->syncSubjects($certificate, $validated['subjects'] ?? []);

        return back()->with('status', 'Marksheet updated.');
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
