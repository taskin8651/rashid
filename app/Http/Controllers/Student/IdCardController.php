<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class IdCardController extends Controller
{
    public function view(Request $request)
    {
        return $this->render($request->user(), inline: true);
    }

    public function download(Request $request)
    {
        return $this->render($request->user(), inline: false);
    }

    protected function render(User $user, bool $inline)
    {
        $user->ensureStudentCode();

        $enrollment = $user->enrollments()->whereIn('status', ['paid', 'completed'])->with('course')->latest('enrolled_at')->first();

        $pdf = Pdf::loadView('id-card.pdf', [
            'user' => $user,
            'course' => $enrollment?->course,
        ])->setPaper([0, 0, 242.65, 153.07]); // CR80 card size in points (85.6mm x 53.98mm)

        $filename = 'RTech-ID-Card-' . $user->student_code . '.pdf';

        return $inline ? $pdf->stream($filename) : $pdf->download($filename);
    }
}
