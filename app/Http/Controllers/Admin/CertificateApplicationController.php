<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CertificateIssued;
use App\Models\Certificate;
use App\Models\CertificateApplication;
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

        $applications = CertificateApplication::with(['user', 'course', 'franchiseBooking'])
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

        $certificate = Certificate::firstOrNew(['user_id' => $application->user_id, 'course_id' => $application->course_id]);
        $certificate->cert_code = $certificate->cert_code ?: Certificate::generateCode();
        $certificate->status = 'issued';
        $certificate->issued_date = now();
        $certificate->save();

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
