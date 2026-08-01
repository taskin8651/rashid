<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\CertificateIssued;
use App\Models\Certificate;
use App\Models\VideoProgress;
use App\Notifications\CertificateIssuedNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $enrollments = $user->enrollments()
            ->whereIn('status', ['paid', 'completed'])
            ->with('course')
            ->get();

        $rows = $enrollments->map(function ($enrollment) use ($user) {
            $totalVideos = $enrollment->course->videos()->where('status', 'active')->whereHas('media')->count();
            $watched = VideoProgress::where('user_id', $user->id)
                ->whereIn('video_id', $enrollment->course->videos()->where('status', 'active')->whereHas('media')->pluck('id'))
                ->where('completed', true)
                ->count();
            $percent = $totalVideos > 0 ? round(($watched / $totalVideos) * 100) : 0;

            $certificate = Certificate::firstOrCreate(
                ['user_id' => $user->id, 'course_id' => $enrollment->course_id],
                ['cert_code' => Certificate::generateCode(), 'status' => 'pending']
            );

            if ($percent >= 100 && $certificate->status !== 'issued') {
                $certificate->update(['status' => 'issued', 'issued_date' => now()]);
                $enrollment->update(['status' => 'completed', 'completed_at' => $enrollment->completed_at ?? now()]);

                $issued = $certificate->fresh(['user', 'course']);

                try {
                    Mail::to($user->email)->send(new CertificateIssued($issued));
                } catch (\Throwable $e) {
                    report($e);
                }

                $user->notify(new CertificateIssuedNotification($issued));
            }

            return [
                'course' => $enrollment->course,
                'percent' => $percent,
                'certificate' => $certificate->fresh(),
            ];
        });

        return view('student.certificates', ['rows' => $rows]);
    }

    public function download(Certificate $certificate)
    {
        abort_unless($certificate->user_id === auth()->id(), 403);
        abort_unless($certificate->status === 'issued', 404);

        $filename = 'RTech-Certificate-' . $certificate->cert_code . '.pdf';

        $certificate->load(['user', 'course']);

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
        ])->setPaper([0, 0, 841.89, 561.26]);

        return $pdf->download($filename);
    }

    public function downloadMarksheet(Certificate $certificate)
    {
        abort_unless($certificate->user_id === auth()->id(), 403);
        abort_unless($certificate->status === 'issued', 404);
        abort_unless($certificate->hasMarksheetData(), 404);

        $certificate->load(['user', 'course', 'subjects']);

        $filename = 'RTech-Marksheet-' . $certificate->cert_code . '.pdf';

        $pdf = Pdf::loadView('certificates.marksheet-pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function verificationQrDataUri(Certificate $certificate): string
{
    $result = new Builder(
        writer: new PngWriter(),
        data: route('certificates.verify', ['code' => $certificate->cert_code]),
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::Low,
        size: 300,
        margin: 10,
        roundBlockSizeMode: RoundBlockSizeMode::Margin
    );

    return $result->build()->getDataUri();
}
}
