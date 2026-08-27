<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class CertificateVerificationController extends Controller
{
    public function show(Request $request)
    {
        $code = $request->query('code');
        $certificate = null;
        $searched = false;

        if ($code) {
            $searched = true;
            $certificate = Certificate::where('cert_code', trim($code))
                ->where('status', 'issued')
                ->with(['user', 'course', 'subjects'])
                ->first();
        }

        return view('certificates.verify', compact('certificate', 'searched', 'code'));
    }

    /**
     * Streams the certificate PDF inline for embedding on the public verify
     * page. Not auth-gated like the student/franchise/admin view() routes —
     * showing the actual document is the entire point of a verification
     * page, and the cert_code binding here is the same unguessable token
     * already required to reach this page in the first place.
     */
    public function preview(Certificate $certificate)
    {
        abort_unless($certificate->status === 'issued', 404);
        abort_unless($certificate->include_certificate, 404);

        $certificate->load(['user', 'course']);

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
            'qrDataUri' => $this->verificationQrDataUri($certificate),
            'signatureImageDataUri' => $this->signatureImageDataUri(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('RTech-Certificate-' . $certificate->cert_code . '.pdf');
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
