@php
    $studentName = optional($certificate->user)->name ?: 'Student Name';
    $fatherName = $certificate->father_name ?: 'Father Name';
    $courseName = optional($certificate->course)->name ?: 'Course Name';
    $batchName = $certificate->batch_name ?: 'Batch/Time';
    $rollNo = $certificate->roll_no ?: 'Roll No.';
    $duration = optional($certificate->course)->duration_text ?: 'Course Duration';
    $issueDate = $certificate->issued_date ? $certificate->issued_date->format('d / m / Y') : 'DD / MM / YYYY';
    $enrollmentNo = $certificate->cert_code ?: 'Enrollment No.';
    $subjects = $certificate->subjects;
    $result = $certificate->result();
    $percentage = rtrim(rtrim(number_format($certificate->percentage(), 2), '0'), '.');

    $fit = function ($value, $limit = 32) {
        return \Illuminate\Support\Str::limit((string) $value, $limit, '...');
    };

    $logoPath = 'file://' . str_replace('\\', '/', public_path('assets/img/rtech-logo-generated.png'));
    $isoPath = 'file://' . str_replace('\\', '/', public_path('assets/img/logoiso.png'));
    $msmePath = 'file://' . str_replace('\\', '/', public_path('assets/img/logo-msme.png'));
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 0; size: A4 landscape; }
    * { box-sizing: border-box; }
    body {
        margin: 0;
        padding: 0;
        font-family: DejaVu Sans, Helvetica, Arial, sans-serif;
        color: #040b38;
        background: #ffffff;
    }
    .sheet {
        position: relative;
        width: 297mm;
        height: 210mm;
        overflow: hidden;
        background: #fffefa;
    }

    .outer {
        position: absolute;
        top: 5mm;
        left: 5mm;
        right: 5mm;
        bottom: 5mm;
        border: 2.1mm solid #020b35;
    }
    .inner-gold {
        position: absolute;
        top: 7.6mm;
        left: 7.6mm;
        right: 7.6mm;
        bottom: 7.6mm;
        border: .75mm solid #d7ad45;
    }
    .inner-blue {
        position: absolute;
        top: 9mm;
        left: 9mm;
        right: 9mm;
        bottom: 9mm;
        border: .45mm solid #020b35;
    }
    .corner {
        position: absolute;
        width: 56mm;
        height: 56mm;
        border: 3.8mm solid #d7ad45;
        border-radius: 50%;
    }
    .corner.tl { left: 8mm; top: 8mm; border-right-color: transparent; border-bottom-color: transparent; }
    .corner.br { right: 8mm; bottom: 8mm; border-left-color: transparent; border-top-color: transparent; }
    .swoosh {
        position: absolute;
        left: -20mm;
        right: -20mm;
        bottom: 5mm;
        height: 34mm;
        background: #020b35;
        transform: skewY(-3deg);
    }
    .swoosh-gold {
        position: absolute;
        right: -22mm;
        bottom: 10mm;
        width: 96mm;
        height: 15mm;
        border-top: 5mm solid #d7ad45;
        border-radius: 50%;
        transform: rotate(-17deg);
    }
    .white-mask {
        position: absolute;
        left: 18mm;
        right: 33mm;
        bottom: 17mm;
        height: 23mm;
        background: #fffefa;
        transform: skewY(-3deg);
    }

    .header {
        position: absolute;
        left: 23mm;
        right: 22mm;
        top: 15mm;
        height: 38mm;
    }
    .brand-logo {
        position: absolute;
        left: 0;
        top: 1mm;
        width: 48mm;
        height: 31mm;
        object-fit: contain;
    }
    .brand {
        position: absolute;
        left: 52mm;
        right: 64mm;
        top: 1mm;
        text-align: center;
    }
    .brand h1 {
        margin: 0;
        color: #020b35;
        font-size: 31pt;
        line-height: 1;
        font-weight: 900;
        letter-spacing: .8pt;
    }
    .iso-line {
        margin-top: 6mm;
        color: #101845;
        font-size: 11.2pt;
        font-weight: 700;
    }
    .iso-line b { color: #020b35; }
    .iso-line:before,
    .iso-line:after {
        content: "";
        display: inline-block;
        width: 23mm;
        height: .7mm;
        margin: 0 4mm 1.2mm;
        background: #d7ad45;
    }
    .reg {
        margin-top: 3mm;
        color: #182153;
        font-size: 9.5pt;
        font-weight: 700;
    }
    .badges {
        position: absolute;
        right: 0;
        top: 0;
        width: 60mm;
        height: 31mm;
        white-space: nowrap;
        text-align: right;
    }
    .badges img {
        position: absolute;
        top: 0;
        object-fit: contain;
        background: #fff;
    }
    .iso-badge { left: 0; width: 36mm; height: 26mm; }
    .msme-badge { right: 0; width: 30mm; height: 31mm; }

    .title {
        position: absolute;
        top: 49mm;
        left: 60mm;
        right: 60mm;
        text-align: center;
    }
    .title h2 {
        margin: 0;
        color: #050a36;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 33pt;
        line-height: 1;
        letter-spacing: 7pt;
        font-weight: 700;
    }
    .title .rule {
        width: 112mm;
        height: .55mm;
        margin: 4mm auto 0;
        background: #d7ad45;
    }
    .title .ornament {
        margin-top: -2.3mm;
        color: #c99121;
        font-size: 13pt;
        letter-spacing: 1pt;
    }

    .left-panel {
        position: absolute;
        left: 16mm;
        top: 70mm;
        width: 50mm;
        height: 87mm;
        border-right: .45mm solid #d7ad45;
        padding-top: 1mm;
    }
    .side-item {
        position: relative;
        min-height: 15mm;
        margin: 0 7mm 5.4mm 0;
        padding: 1.5mm 0 2.5mm 16mm;
        border-bottom: .3mm solid #dfc37d;
    }
    .side-icon {
        position: absolute;
        left: 0;
        top: 1mm;
        width: 11.8mm;
        height: 11.8mm;
        border-radius: 50%;
        border: .8mm solid #d7ad45;
        background: #020b35;
        color: #fff;
        text-align: center;
        line-height: 10.2mm;
        font-size: 8pt;
        font-weight: 900;
    }
    .side-label {
        color: #06103e;
        font-size: 8pt;
        font-weight: 900;
        text-transform: uppercase;
        line-height: 1.15;
    }
    .side-value {
        margin-top: 1.6mm;
        color: #121a4b;
        font-size: 8pt;
        font-weight: 700;
        line-height: 1.2;
    }
    .gold { color: #c99121; }

    .details {
        position: absolute;
        top: 70mm;
        left: 74mm;
        width: 151mm;
        height: 30mm;
        font-size: 8.6pt;
        font-weight: 700;
    }
    .col {
        position: absolute;
        top: 0;
        width: 72mm;
    }
    .col.left { left: 0; }
    .col.right { right: 0; }
    .row {
        position: relative;
        height: 7mm;
        white-space: nowrap;
    }
    .label {
        display: inline-block;
        width: 30mm;
        color: #06103e;
        font-weight: 900;
        text-transform: uppercase;
    }
    .right .label { width: 31mm; }
    .colon {
        display: inline-block;
        width: 4mm;
        color: #06103e;
        text-align: center;
    }
    .value {
        display: inline-block;
        max-width: 36mm;
        color: #121a4b;
        overflow: hidden;
        text-overflow: ellipsis;
        vertical-align: top;
    }
    .right .value { max-width: 34mm; }

    .qr-block {
        position: absolute;
        top: 73mm;
        right: 28mm;
        width: 32mm;
        text-align: center;
    }
    .qr-block img {
        width: 23mm;
        height: 23mm;
        padding: 1.3mm;
        border: .65mm solid #111;
        background: #fff;
        object-fit: contain;
    }
    .qr-block p {
        margin: 3mm 0 0;
        color: #141a3f;
        font-size: 7.6pt;
        line-height: 1.25;
        font-weight: 700;
    }

    .marks {
        position: absolute;
        top: 97mm;
        left: 74mm;
        width: 151mm;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    .marks th {
        height: 8mm;
        background: #020b35;
        color: #fff;
        border: .35mm solid #d7ad45;
        font-size: 8.7pt;
        text-transform: uppercase;
        font-weight: 900;
        text-align: center;
    }
    .marks td {
        height: 6.2mm;
        padding: 0 3mm;
        border: .35mm solid #d7ad45;
        color: #06103e;
        font-size: 8.2pt;
        font-weight: 700;
        vertical-align: middle;
    }
    .marks td.num { text-align: center; }
    .marks tr.total td {
        height: 7.2mm;
        background: #020b35;
        color: #fff;
        font-size: 9.4pt;
        font-weight: 900;
    }
    .bracket {
        display: inline-block;
        min-width: 18mm;
        color: inherit;
    }

    .summary {
        position: absolute;
        top: 155mm;
        left: 74mm;
        width: 151mm;
    }
    .summary td {
        height: 11.5mm;
        border: .4mm solid #06103e;
        text-align: center;
        color: #06103e;
        font-size: 8.6pt;
        font-weight: 900;
        text-transform: uppercase;
    }
    .summary span {
        display: block;
        margin-top: 1.2mm;
        color: #c99121;
        font-size: 10.5pt;
    }
    .summary .result-pass { color: #c99121; }
    .summary .result-fail { color: #a91515; }

    .signature {
        position: absolute;
        top: 112mm;
        right: 19mm;
        width: 50mm;
        text-align: center;
        color: #06103e;
    }
    .sign-name {
        color: #1b2464;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 16pt;
        font-style: italic;
        line-height: 1;
        border-bottom: .35mm solid #d7ad45;
        padding-bottom: 1mm;
    }
    .signature b {
        display: block;
        margin-top: 1mm;
        font-size: 8pt;
        line-height: 1.2;
    }
    .signature img {
        display: block;
        width: 34mm;
        height: 16mm;
        margin: 0 auto 2mm;
        object-fit: contain;
    }

    .footer {
        position: absolute;
        left: 14mm;
        right: 16mm;
        bottom: 13mm;
        height: 16mm;
        color: #fff;
        font-size: 7.5pt;
        font-weight: 700;
        border-top: .45mm solid #d7ad45;
        border-bottom: .45mm solid #d7ad45;
        background: rgba(2, 11, 53, .96);
    }
    .footer td {
        color: #fff;
        vertical-align: middle;
        border-right: .35mm solid rgba(215, 173, 69, .8);
        padding: 2.4mm 4mm 2mm;
        line-height: 1.25;
    }
    .footer td:last-child { border-right: 0; }
    .foot-label {
        display: block;
        color: #f2cf75;
        font-size: 5.4pt;
        letter-spacing: .8pt;
        text-transform: uppercase;
        margin-bottom: 1mm;
    }
    .foot-value {
        display: block;
        color: #fff;
        font-size: 7.6pt;
        font-weight: 800;
    }
    .disclaimer {
        position: absolute;
        left: 30mm;
        right: 30mm;
        bottom: 31.5mm;
        color: #06103e;
        font-size: 6.2pt;
        line-height: 1.35;
        text-align: center;
        font-weight: 700;
    }
</style>
</head>
<body>
<div class="sheet">
    <div class="swoosh"></div>
    <div class="swoosh-gold"></div>
    <div class="white-mask"></div>
    <div class="outer"></div>
    <div class="inner-gold"></div>
    <div class="inner-blue"></div>
    <div class="corner tl"></div>
    <div class="corner br"></div>

    <div class="header">
        <img class="brand-logo" src="{{ $logoPath }}" alt="R-Tech Computer">
        <div class="brand">
            <h1>R-TECH COMPUTER</h1>
            <div class="iso-line">An <b>ISO 9001:2015</b> Certified Institute</div>
            <div class="reg">Udyam Registration No.: <b>UDYAM-BR-24-0042559</b></div>
        </div>
        <div class="badges">
            <img class="iso-badge" src="{{ $isoPath }}" alt="ISO 9001:2015">
            <img class="msme-badge" src="{{ $msmePath }}" alt="Udyam MSME">
            
        </div>
        
    </div>

    <div class="title">
        <h2>MARKSHEET</h2>
        <div class="rule"></div>
        <div class="ornament">~ ~ ~</div>
    </div>

    <div class="left-panel">
        <div class="side-item">
            <div class="side-icon">S</div>
            <div class="side-label">Student Name</div>
            <div class="side-value">{{ $fit($studentName, 22) }}</div>
        </div>
        <div class="side-item">
            <div class="side-icon">C</div>
            <div class="side-label">Course</div>
            <div class="side-value gold">{{ $fit(strtoupper($courseName), 23) }}</div>
        </div>
       
        <div class="side-item">
            <div class="side-icon">D</div>
            <div class="side-label">Duration</div>
            <div class="side-value">{{ $fit($duration, 22) }}</div>
        </div>
        <div class="side-item">
            <div class="side-icon">G</div>
            <div class="side-label">Grade</div>
            <div class="side-value">{{ $certificate->grade() ?: 'Grade' }}</div>
        </div>
    </div>

    <div class="details">
        <div class="col left">
            <div class="row"><span class="label">Student Name</span><span class="colon">:</span><span class="value">{{ $fit($studentName, 29) }}</span></div>
            <div class="row"><span class="label">Father's Name</span><span class="colon">:</span><span class="value">{{ $fit($fatherName, 29) }}</span></div>
            <div class="row"><span class="label">Course</span><span class="colon">:</span><span class="value gold">{{ $fit(strtoupper($courseName), 29) }}</span></div>
            <div class="row"><span class="label">Enrollment No.</span><span class="colon">:</span><span class="value">{{ $fit($enrollmentNo, 28) }}</span></div>
        </div>
        <div class="col right">
            <div class="row"><span class="label">Roll No.</span><span class="colon">:</span><span class="value">{{ $fit($rollNo, 26) }}</span></div>
            <div class="row"><span class="label">Batch</span><span class="colon">:</span><span class="value">{{ $fit($batchName, 26) }}</span></div>
            <div class="row"><span class="label">Date of Issue</span><span class="colon">:</span><span class="value">{{ $issueDate }}</span></div>
            <div class="row"><span class="label">Duration</span><span class="colon">:</span><span class="value">{{ $fit($duration, 26) }}</span></div>
        </div>
    </div>

    <div class="qr-block">
        <img src="{{ $qrDataUri }}" alt="Verification QR Code">
        <p>Scan QR Code<br>to Verify Marksheet</p>
    </div>
    <div class="signature">    
        @if(!empty($signatureImageDataUri))
            <img src="{{ $signatureImageDataUri }}" alt="Signature">
        @else
            <div class="sign-name">Md Rashid</div>
        @endif
        <b>Authorized Signatory<br>R-Tech Computer</b>
    </div>

    <div class="marks">
        <table>
            <thead>
                <tr>
                    <th style="width:46%">Subjects</th>
                    <th style="width:26%">Maximum Marks</th>
                    <th style="width:28%">Marks Obtained</th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < max(6, $subjects->count()); $i++)
                    @php $subject = $subjects->get($i); @endphp
                    <tr>
                        <td>{!! $subject ? e($fit($subject->subject, 42)) : '&nbsp;' !!}</td>
                        <td class="num">{!! $subject ? e($subject->max_marks ?? '') : '&nbsp;' !!}</td>
                        <td class="num"><span class="bracket"> {{ $subject && $subject->marks_obtained !== null ? $subject->marks_obtained : '' }} </span></td>
                    </tr>
                @endfor
                <tr class="total">
                    <td>TOTAL MARKS</td>
                    <td class="num">{{ $certificate->totalMaxMarks() }}</td>
                    <td class="num"><span class="bracket">[ {{ $certificate->totalMarksObtained() }} ]</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <table>
            <tr>
                <td>Percentage <span>[ {{ $percentage }}% ]</span></td>
                <td>Grade <span>[ {{ $certificate->grade() ?: 'GRADE' }} ]</span></td>
                <td>Result <span class="{{ $result === 'PASS' ? 'result-pass' : 'result-fail' }}">{{ $result ?: 'PASS' }}</span></td>
            </tr>
        </table>
    </div>

    

    <div class="footer">
        <table>
            <tr>
                <td style="width:38%">
                    <span class="foot-label">Institute Address</span>
                    <span class="foot-value">Mogalkuan, Etwari Bazar, Sohsarai Road,<br>Bihar Sharif, Nalanda, Bihar - 803117</span>
                </td>
                <td style="width:18%">
                    <span class="foot-label">Phone</span>
                    <span class="foot-value">9117744925</span>
                </td>
                <td style="width:20%">
                    <span class="foot-label">Website</span>
                    <span class="foot-value">www.rtechcomputer.in</span>
                </td>
                <td style="width:24%">
                    <span class="foot-label">Email</span>
                    <span class="foot-value">rtechcomputer40@gmail.com</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="disclaimer">
        This marksheet is issued by R-Tech Computer. ISO 9001:2015 and Udyam Registration relate to the institute's
        certification and registration status and do not constitute government approval or accreditation of the individual course or student.
    </div>
</div>
</body>
</html>
