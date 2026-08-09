@php
    $studentName = $certificate->student_name ?: (optional($certificate->user)->name ?: 'Student Name');
    $fatherName = $certificate->father_name ?: 'Father Name';
    $courseName = $certificate->course_name ?: (optional($certificate->course)->name ?: 'Course Name');
    $batchName = $certificate->batch_name ?: 'Batch/Time';
    $rollNo = $certificate->roll_no ?: 'Roll No.';
    $duration = $certificate->course_duration_text ?: (optional($certificate->course)->duration_text ?: 'Course Duration');
    $issueDate = $certificate->issued_date ? $certificate->issued_date->format('d M Y') : 'DD MM YYYY';
    $enrollmentNo = $certificate->cert_code ?: 'Enrollment No.';
    $subjects = $certificate->subjects;
    $result = $certificate->result() ?: 'PASS';
    $percentage = rtrim(rtrim(number_format($certificate->percentage(), 2), '0'), '.');
    $grade = $certificate->grade() ?: '—';

    $fit = function ($value, $limit = 22) {
        return \Illuminate\Support\Str::limit((string) $value, $limit, '...');
    };
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css" integrity="sha512-ApSLB1Pd3/bZN8fWB/RG9YhN/7bd9Hkf3AGaE2mPfebjrxagjuBtx2GcgdqIlJkUzwylBo61r9Xa9NmgBI0swA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
  @page { margin: 0; size: A4 landscape; }
  * { box-sizing: border-box; }
  body { margin: 0; font-family: 'Helvetica', 'DejaVu Sans', sans-serif; color: #16233f; }
  .sheet { position: relative; width: 297mm; height: 210mm; background: #fdfcf8; }

  .border-outer { position: absolute; top: 6mm; left: 6mm; right: 6mm; bottom: 6mm; border: 3.2pt solid #16336e; border-radius: 9mm; }
  .border-inner { position: absolute; top: 9mm; left: 9mm; right: 9mm; bottom: 9mm; border: 1pt solid #c9a24b; border-radius: 7mm; }
  .accent-spine { position: absolute; top: 9mm; bottom: 9mm; left: 9mm; width: 3mm; background: #c9a24b; border-top-left-radius: 7mm; border-bottom-left-radius: 7mm; }

  /* HEADER */
  .header-row { position: absolute; top: 13mm; left: 16mm; right: 16mm; height: 26mm; }
  .brand-block { position: absolute; left: 0; top: 0; }
  .brand-logo-wrap {
    position: absolute; left: 0; top: 0; width: 20mm; height: 20mm;
    background: #fff; border: 0.6pt solid #e4dcc8; border-radius: 2mm;
    text-align: center; padding-top: 2mm;
  }
  .brand-logo { width: 16mm; height: 16mm; }
  .brand-text { position: absolute; left: 24mm; top: 0; width: 130mm; }
  .brand-name { font-size: 19pt; font-weight: bold; color: #16336e; letter-spacing: 0.5pt; }
  .brand-sub { font-size: 9pt; color: #45516e; margin-top: 1.5mm; }
  .brand-sub b { color: #16336e; }
  .brand-reg { font-size: 7.5pt; color: #5c6a8a; margin-top: 1mm; }
  .brand-reg b { color: #16336e; }

  .badges { position: absolute; right: 0; top: 0; width: 95mm; text-align: right; }
  .badge-box { position: relative; display: inline-block; width: 24mm; height: 24mm; overflow: hidden; margin-left: 2mm; vertical-align: top; }
  .badge-img { position: absolute; top: -6mm; left: -6mm; width: 36mm; height: 36mm; }

  /* TITLE */
  .title-block { position: absolute; top: 38mm; left: 16mm; right: 16mm; text-align: center; font-family: 'Times New Roman', 'DejaVu Serif', serif; }
  .title { font-size: 36pt; font-weight: bold; color: #16233f; letter-spacing: 2pt; }
  .subtitle { font-size: 12pt; color: #5c6a8a; letter-spacing: 5pt; text-transform: uppercase; margin-top: 1mm; }
  .ornament { margin-top: 2.5mm; font-size: 8pt; color: #c9a24b; }
  .ornament .rl { display: inline-block; width: 22mm; height: 0.8pt; background: #c9a24b; vertical-align: middle; margin: 0 3mm; }

  /* STUDENT STRIP */
  .ms-student { position: absolute; top: 65mm; left: 16mm; right: 96mm; text-align: center; font-family: 'Times New Roman', 'DejaVu Serif', serif; }
  .ms-student-name {
    font-size: 21pt; font-weight: bold; font-style: italic; color: #16336e;
    padding-bottom: 1.6mm; display: inline-block; border-bottom: 1pt solid #c9a24b;
    max-width: 195mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .ms-student-sub { font-size: 9pt; color: #5c6a8a; margin-top: 1.6mm; }
  .ms-student-sub b { color: #16233f; }

  /* INFO ROWS (details + summary) — horizontal strips matching certificate language */
  .ms-info { position: absolute; left: 20mm; right: 96mm; z-index: 5; }
  .ms-info:after { content: ""; display: table; clear: both; }
  .ms-info-item { float: left; text-align: center; padding: 0 2mm; box-sizing: border-box; }
  .ms-info-icon { color: #16336e; margin: 0 auto 1.6mm; font-size: 13pt; display: block; }
  .ms-info-label { font-size: 7.6pt; text-transform: uppercase; letter-spacing: 0.4pt; color: #8b96b3; }
  .ms-info-value { font-size: 10pt; font-weight: bold; color: #16233f; margin-top: 0.6mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ms-info-value.accent { color: #c9924b; }
  .ms-info-value.pass { color: #1c7a44; }
  .ms-info-value.fail { color: #a91515; }

  /* MARKS TABLE */
  .ms-marks { position: absolute; top: 105mm; left: 16mm; right: 96mm; }
  .ms-marks table { width: 100%; border-collapse: collapse; }
  .ms-marks th {
    height: 7mm; background: #16336e; color: #d9b65c; border: 0.5pt solid #c9a24b;
    font-size: 8.3pt; text-transform: uppercase; font-weight: bold; letter-spacing: 0.3pt;
  }
  .ms-marks td {
    height: 5.8mm; padding: 0 3mm; border: 0.5pt solid #c9a24b; color: #16233f; font-size: 8.3pt; font-weight: bold;
  }
  .ms-marks td.num { text-align: center; }
  .ms-marks tr.total td { background: #16336e; color: #fff; font-size: 9pt; }

  /* SIGNATURE + QR (right sidebar) */
  .ms-sign-col { position: absolute; left: 221mm; top: 65mm; width: 60mm; text-align: center; }
  .ms-seal-img { width: 28mm; height: 16mm; margin-bottom: 1.5mm; }
  .ms-sign-name { font-size: 10pt; font-weight: bold; color: #16233f; }
  .ms-sign-role { font-size: 8.5pt; color: #5c6a8a; margin-top: 0.5mm; }

  .ms-qr-col { position: absolute; left: 221mm; top: 112mm; width: 60mm; text-align: center; }
  .ms-qr-img { width: 26mm; height: 26mm; border: 1pt solid #c9a24b; padding: 1.2mm; background: #fff; }
  .ms-qr-caption { font-size: 7pt; color: #5c6a8a; margin-top: 1.5mm; line-height: 1.3; }

  /* FOOTER */
  .footer-bar {
    position: absolute; left: 9mm; right: 9mm; bottom: 20mm;
    background: #16336e; color: #fff; padding: 2.6mm 8mm; font-size: 8pt;
  }
  .footer-table { width: 100%; border-collapse: collapse; }
  .footer-table td { color: rgba(255,255,255,.92); font-size: 7.8pt; }

  .disclaimer {
    position: absolute; left: 18mm; right: 18mm; bottom: 9.5mm;
    text-align: center; font-size: 6.3pt; color: #8b96b3; line-height: 1.4;
    font-family: 'Times New Roman', 'DejaVu Serif', serif; font-style: italic; letter-spacing: 0.15pt;
  }
</style>
</head>
<body>
  <div class="sheet">
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="accent-spine"></div>

    <div class="header-row">
      <div class="brand-block">
        @if (file_exists(public_path('assets/img/logo.png')))
          <div class="brand-logo-wrap">
            <img class="brand-logo" src="file://{{ str_replace('\\', '/', public_path('assets/img/logo.png')) }}">
          </div>
        @endif
        <div class="brand-text">
          <div class="brand-name">R-TECH COMPUTER</div>
          <div class="brand-sub">An <b>ISO 9001:2015</b> Certified Institute</div>
          <div class="brand-reg">Udyam Registration No.: <b>UDYAM-BR-24-0042559</b></div>
        </div>
      </div>
      <div class="badges">
        @if (file_exists(public_path('assets/img/logoiso.png')))
          <div class="badge-box">
            <img class="badge-img" src="file://{{ str_replace('\\', '/', public_path('assets/img/logoiso.png')) }}">
          </div>
        @endif
        @if (file_exists(public_path('assets/img/logo-msme.png')))
          <div class="badge-box">
            <img class="badge-img" src="file://{{ str_replace('\\', '/', public_path('assets/img/logo-msme.png')) }}">
          </div>
        @endif
      </div>
    </div>

    <div class="title-block">
      <div class="title">MARKSHEET</div>
      <div class="subtitle">Statement of Marks</div>
      <div class="ornament"><span class="rl"></span>&#9670;<span class="rl"></span></div>
    </div>

    <div class="ms-student">
      <div class="ms-student-name">{{ $fit($studentName, 34) }}</div>
      <div class="ms-student-sub">S/o-D/o <b>{{ $fit($fatherName, 26) }}</b> &nbsp;|&nbsp; Roll No. <b>{{ $fit($rollNo, 16) }}</b></div>
    </div>

    <div class="ms-info" style="top: 86mm;">
      <div class="ms-info-item" style="width: 20%">
        <i class="fas fa-graduation-cap ms-info-icon"></i>
        <div class="ms-info-label">Course</div>
        <div class="ms-info-value accent">{{ $fit(strtoupper($courseName), 18) }}</div>
      </div>
      <div class="ms-info-item" style="width: 20%">
        <i class="fas fa-id-card ms-info-icon"></i>
        <div class="ms-info-label">Enrollment No.</div>
        <div class="ms-info-value">{{ $fit($enrollmentNo, 16) }}</div>
      </div>
      <div class="ms-info-item" style="width: 20%">
        <i class="fas fa-users ms-info-icon"></i>
        <div class="ms-info-label">Batch</div>
        <div class="ms-info-value">{{ $fit($batchName, 14) }}</div>
      </div>
      <div class="ms-info-item" style="width: 20%">
        <i class="fas fa-clock ms-info-icon"></i>
        <div class="ms-info-label">Duration</div>
        <div class="ms-info-value">{{ $fit($duration, 14) }}</div>
      </div>
      <div class="ms-info-item" style="width: 20%">
        <i class="fas fa-calendar-alt ms-info-icon"></i>
        <div class="ms-info-label">Issue Date</div>
        <div class="ms-info-value">{{ $issueDate }}</div>
      </div>
    </div>

    <div class="ms-marks">
      <table>
        <thead>
          <tr>
            <th style="width:46%">Subject</th>
            <th style="width:27%">Maximum Marks</th>
            <th style="width:27%">Marks Obtained</th>
          </tr>
        </thead>
        <tbody>
          @for ($i = 0; $i < max(6, $subjects->count()); $i++)
            @php $subject = $subjects->get($i); @endphp
            <tr>
              <td>{!! $subject ? e($fit($subject->subject, 40)) : '&nbsp;' !!}</td>
              <td class="num">{!! $subject ? e($subject->max_marks ?? '') : '&nbsp;' !!}</td>
              <td class="num">{!! $subject && $subject->marks_obtained !== null ? e($subject->marks_obtained) : '&nbsp;' !!}</td>
            </tr>
          @endfor
          <tr class="total">
            <td>TOTAL MARKS</td>
            <td class="num">{{ $certificate->totalMaxMarks() }}</td>
            <td class="num">{{ $certificate->totalMarksObtained() }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="ms-info" style="top: 157mm;">
      <div class="ms-info-item" style="width: 33.33%">
        <i class="fas fa-percent ms-info-icon"></i>
        <div class="ms-info-label">Percentage</div>
        <div class="ms-info-value">{{ $percentage }}%</div>
      </div>
      <div class="ms-info-item" style="width: 33.33%">
        <i class="fas fa-award ms-info-icon"></i>
        <div class="ms-info-label">Grade</div>
        <div class="ms-info-value accent">{{ $grade }}</div>
      </div>
      <div class="ms-info-item" style="width: 33.33%">
        <i class="fas fa-check-circle ms-info-icon"></i>
        <div class="ms-info-label">Result</div>
        <div class="ms-info-value {{ $result === 'PASS' ? 'pass' : 'fail' }}">{{ $result }}</div>
      </div>
    </div>

    <div class="ms-sign-col">
      @if (!empty($signatureImageDataUri))
        <img class="ms-seal-img" src="{{ $signatureImageDataUri }}">
      @endif
      <div class="ms-sign-name">Authorized Signatory</div>
      <div class="ms-sign-role">R-Tech Computer</div>
    </div>

    <div class="ms-qr-col">
      <img class="ms-qr-img" src="{{ $qrDataUri }}">
      <div class="ms-qr-caption">Scan QR Code<br>to Verify Marksheet</div>
    </div>

    <div class="footer-bar">
      <table class="footer-table">
        <tr>
          <td style="width:34%">Mogalkuan, Etwari Bazar, Sohsarai Road,<br>Bihar Sharif, Nalanda &ndash; 803117</td>
          <td style="width:20%">+91 9117744925</td>
          <td style="width:24%">www.rtechcomputer.in</td>
          <td style="width:22%">rtechcomputer40@gmail.com</td>
        </tr>
      </table>
    </div>
    <div class="disclaimer">This marksheet is issued by R-Tech Computer. ISO 9001:2015 and Udyam Registration relate to the institute&rsquo;s certification and registration status and do not constitute government approval or accreditation of the individual course or student.</div>
  </div>
</body>
</html>
