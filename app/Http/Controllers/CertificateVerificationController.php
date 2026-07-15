<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
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
                ->with(['user', 'course'])
                ->first();
        }

        return view('certificates.verify', compact('certificate', 'searched', 'code'));
    }
}
