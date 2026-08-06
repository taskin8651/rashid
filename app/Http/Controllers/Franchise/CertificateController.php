<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Concerns\AuthorizesFranchiseAccess;
use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificateApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    use AuthorizesFranchiseAccess;

    protected function ownBookingIds(Request $request)
    {
        $user = $request->user();

        return $user->accessibleFranchiseBookingsQuery()->where('status', 'paid')->pluck('id')
            ->filter(fn ($id) => $user->hasFranchisePermission($id, 'view-students'))
            ->values();
    }

    public function index(Request $request)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');

        $bookingIds = $this->ownBookingIds($request);

        $applications = CertificateApplication::with(['user', 'course'])
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->latest()
            ->paginate(20, ['*'], 'applications_page');

        $certificates = Certificate::with(['user', 'course', 'subjects'])
            ->whereHas('course', fn ($q) => $q->whereIn('franchise_booking_id', $bookingIds))
            ->latest()
            ->paginate(20, ['*'], 'certificates_page');

        return view('franchise.certificates.index', compact('applications', 'certificates'));
    }

    public function downloadProof(Request $request, CertificateApplication $application)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');
        abort_unless($this->ownBookingIds($request)->contains($application->course?->franchise_booking_id), 404);
        abort_unless($application->proof_path, 404);

        return Storage::disk('local')->download($application->proof_path);
    }

    public function download(Request $request, Certificate $certificate)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');
        abort_unless($this->ownBookingIds($request)->contains($certificate->course?->franchise_booking_id), 404);
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

    public function downloadMarksheet(Request $request, Certificate $certificate)
    {
        $this->authorizeAnyFranchisePermission($request, 'view-students');
        abort_unless($this->ownBookingIds($request)->contains($certificate->course?->franchise_booking_id), 404);
        abort_unless($certificate->status === 'issued', 404);
        abort_unless($certificate->hasMarksheetData(), 404);

        $filename = 'RTech-Marksheet-' . $certificate->cert_code . '.pdf';
        $certificate->load(['user', 'course', 'subjects']);

        $pdf = Pdf::loadView('certificates.marksheet-pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
            'signatureImageDataUri' => $this->signatureImageDataUri(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
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
}
