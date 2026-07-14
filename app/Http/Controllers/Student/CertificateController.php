<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\CertificateIssued;
use App\Models\Certificate;
use App\Models\VideoProgress;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

                try {
                    Mail::to($user->email)->send(new CertificateIssued($certificate->fresh(['user', 'course'])));
                } catch (\Throwable $e) {
                    report($e);
                }
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

        $certificate->load(['user', 'course']);

        $pdf = Pdf::loadView('certificates.pdf', ['certificate' => $certificate])
            ->setPaper('a4', 'landscape');

        return $pdf->download('RTech-Certificate-' . $certificate->cert_code . '.pdf');
    }
}
