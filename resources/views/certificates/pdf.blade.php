<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<style>
    @page {
        margin: 0;
        size: 297mm 198mm;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Helvetica', 'DejaVu Sans', sans-serif;
        color: #16233f;
    }

    .sheet {
        position: relative;
        width: 297mm;
        height: 198mm;
    }

    .bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 297mm;
        height: 198mm;
    }

    .field {
        position: absolute;
        font-weight: bold;
        color: #17336f;
        margin: 0;
    }

    .field.nowrap {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .f-certno {
        left: 29mm;
        top: 79.7mm;
        width: 36.8mm;
        height: 5.4mm;
        line-height: 5.4mm;
        font-size: 10pt;
    }

    .f-issue {
        left: 29mm;
        top: 97.7mm;
        width: 34.8mm;
        height: 5.8mm;
        line-height: 5.8mm;
        font-size: 10pt;
    }

    .f-duration {
        left: 29mm;
        top: 116.6mm;
        width: 25.1mm;
        height: 5.8mm;
        line-height: 5.8mm;
        font-size: 10pt;
    }

    .f-grade {
        left: 29mm;
        top: 135mm;
        width: 19.3mm;
        height: 5.6mm;
        line-height: 5.6mm;
        font-size: 10pt;
    }

    .f-studentname {
        left: 91.9mm;
        top: 87.4mm;
        width: 118mm;
        height: 14.7mm;
        line-height: 14.7mm;
        text-align: center;
        font-size: 22pt;
        font-weight: bold;
        font-style: italic;
        color: #16233f;
    }

    .f-ribbon {
        left: 91.9mm;
        top: 112.8mm;
        width: 114mm;
        height: 10.1mm;
        line-height: 10.1mm;
        text-align: center;
        font-size: 15pt;
        font-weight: bold;
        letter-spacing: .5pt;
        color: #f1cc67;
    }

    .f-qr {
        position: absolute;
        left: 240.5mm;
        top: 75.2mm;
        width: 27.3mm;
        height: 27.3mm;
    }

    /* Signature Text */
    /* Premium Signature */
/* Signature */
.f-sign{
    position:absolute;
    left:109mm;
    top:120.5mm;
    width:46mm;

    text-align:center;
    line-height:1;

    font-family:"Times New Roman", serif;
    font-size:26pt;
    font-style:italic;
    font-weight:700;

    color:#102b6c;

    letter-spacing:0.8px;

    transform:rotate(-5deg);

    text-shadow:0.2mm 0.2mm 0 rgba(0,0,0,.08);
}
/* Thin Signature Line */
.f-sign-line{
    position:absolute;
    left:11mm;
    top:156.2mm;
    width:42mm;
    border-top:0.4mm solid #8d8d8d;
}

/* Name - Line ke bilkul neeche */
.f-sign-name{
    position:absolute;
    left:108mm;
    top:157.3mm;
    width:48mm;

    text-align:center;
    font-size:9.8pt;
    font-weight:bold;
    color:#16306c;
    line-height:1;
}

/* Designation - Name ke just neeche */
.f-sign-title{
    position:absolute;
    left:108mm;
    top:161.8mm;
    width:48mm;

    text-align:center;
    font-size:8pt;
    font-weight:600;
    color:#666;
    letter-spacing:1px;
    text-transform:uppercase;
    line-height:1;
}

</style>

</head>

<body>

<div class="sheet">

    <img class="bg"
         src="file://{{ str_replace('\\', '/', public_path('assets/img/certificate-bg.jpg')) }}">

    <div class="field nowrap f-certno">
        {{ $certificate->cert_code }}
    </div>

    <div class="field nowrap f-issue">
        {{ $certificate->issued_date->format('d/m/Y') }}
    </div>

    <div class="field nowrap f-duration">
        {{ $certificate->course->duration_text ?: '—' }}
    </div>

    <div class="field nowrap f-grade">
        {{ $certificate->hasMarksheetData() ? $certificate->grade() : '—' }}
    </div>

    @php

        $studentName = $certificate->user->name;

        $studentNameSize = match (true) {
            strlen($studentName) <= 20 => '22pt',
            strlen($studentName) <= 28 => '18pt',
            strlen($studentName) <= 36 => '15pt',
            default => '12pt',
        };

        $studentName = \Illuminate\Support\Str::limit($studentName, 42, '…');

        $courseName = strtoupper($certificate->course->name);

        $courseNameSize = match (true) {
            strlen($courseName) <= 22 => '15pt',
            strlen($courseName) <= 30 => '12pt',
            strlen($courseName) <= 40 => '10pt',
            default => '8.5pt',
        };

        $courseName = \Illuminate\Support\Str::limit($courseName, 48, '…');

    @endphp

    <div class="field nowrap f-studentname"
         style="font-size: {{ $studentNameSize }}">
        {{ $studentName }}
    </div>

    <div class="field nowrap f-ribbon"
         style="font-size: {{ $courseNameSize }}">
        {{ $courseName }}
    </div>

    @if(!empty($signatureImageDataUri))
        <img class="f-sign" src="{{ $signatureImageDataUri }}" alt="Signature">
    @else
        <div class="f-sign">Md Rashid</div>
    @endif

    <div class="f-sign-line"></div>

    <div class="f-sign-title">
        AUTHORIZED SIGNATORY
    </div>

    <img class="f-qr" src="{{ $qrDataUri }}">
</div>

</body>
</html>