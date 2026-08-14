@php
    $studentName = $certificate->student_name ?: (optional($certificate->user)->name ?: 'Student Name');
    $fatherName = $certificate->father_name ?: 'Father Name';
    $courseName = $certificate->course_name ?: (optional($certificate->course)->name ?: 'Course Name');
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
<style>
  @page { margin: 0; size: A4 portrait; }
  * { box-sizing: border-box; }
  body { margin: 0; font-family: 'Helvetica', 'DejaVu Sans', sans-serif; color: #16233f; }
  .sheet { position: relative; width: 210mm; height: 297mm; background: #fdfcf8; }

  /* Self-hosted icon font — dompdf can't fetch remote CDN CSS (enable_remote is off)
     and modern Font Awesome uses CSS custom properties dompdf can't parse, so a local
     legacy-format webfont with plain :before content is used instead. */
  @font-face {
    font-family: 'CertIcons';
    src: url('file://{{ str_replace('\\', '/', public_path('assets/fonts/fontawesome-webfont.ttf')) }}') format('truetype');
    font-weight: normal; font-style: normal;
  }
  .fa-ico { font-family: 'CertIcons'; font-weight: normal; font-style: normal; }

  .border-outer { position: absolute; top: 6mm; left: 6mm; right: 6mm; bottom: 6mm; border: 3.2pt solid #16336e; border-radius: 9mm; box-shadow: 0 0 0 1pt rgba(201,162,75,.35), 0 0 0 2.4mm rgba(201,162,75,.08); }
  .border-inner { position: absolute; top: 9mm; left: 9mm; right: 9mm; bottom: 9mm; border: 1pt solid #c9a24b; border-radius: 7mm; }
  .accent-spine { position: absolute; top: 9mm; bottom: 9mm; left: 9mm; width: 3mm; background: #c9a24b; border-top-left-radius: 7mm; border-bottom-left-radius: 7mm; }

  /* CORNER MARKS */
  .corner-mark { position: absolute; width: 3.4mm; height: 3.4mm; border: 0.9pt solid #c9a24b; background: #fdfcf8; transform: rotate(45deg); }
  .corner-mark.tl { top: 11.3mm; left: 11.3mm; }
  .corner-mark.tr { top: 11.3mm; right: 11.3mm; }
  .corner-mark.bl { bottom: 11.3mm; left: 11.3mm; }
  .corner-mark.br { bottom: 11.3mm; right: 11.3mm; }

  /* HEADER */
  .header-row { position: absolute; top: 13mm; left: 16mm; right: 16mm; height: 26mm; }
  .brand-block { position: absolute; left: 0; top: 0; }
  .brand-logo-wrap {
    position: absolute; left: 0; top: 0; width: 20mm; height: 20mm;
    background: #fff; border: 0.6pt solid #e4dcc8; border-radius: 2mm;
    text-align: center; padding-top: 2mm;
  }
  .brand-logo { width: 16mm; height: 16mm; }
  .brand-text { position: absolute; left: 24mm; top: 0; width: 105mm; }
  .brand-name { font-size: 20pt; font-weight: bold; color: #16336e; letter-spacing: 0.5pt; }
  .brand-sub { font-size: 10pt; color: #45516e; margin-top: 1.5mm; }
  .brand-sub b { color: #16336e; }
  .brand-reg { font-size: 8.5pt; color: #5c6a8a; margin-top: 1mm; }
  .brand-reg b { color: #16336e; }

  .badges { position: absolute; right: 0; top: 0; }
  .badge-table { border-collapse: collapse; }
  .badge-cell { text-align: center; vertical-align: top; padding: 0 0 0 4mm; }
  .badge-chip {
    width: 17mm; height: 17mm; margin: 0 auto 1.2mm; background: #fff;
    border: 0.8pt solid #c9a24b; border-radius: 2.6mm; text-align: center; padding-top: 0.8mm;
    box-shadow: 0 1.6mm 2.6mm -1.6mm rgba(13,30,60,.35), inset 0 0 0 0.5pt rgba(201,162,75,.25);
  }
  .badge-img { height: 15.5mm; width: auto; max-width: 20mm; }
  .badge-cap {
    font-family: 'Helvetica', 'DejaVu Sans', sans-serif; font-size: 5.8pt; font-weight: bold;
    color: #16336e; text-transform: uppercase; letter-spacing: 0.3pt; line-height: 1.3;
  }

  /* TITLE */
  .title-block { position: absolute; top: 42mm; left: 16mm; right: 16mm; text-align: center; font-family: 'Times New Roman', 'DejaVu Serif', serif; }
  .title { font-size: 40pt; font-weight: bold; color: #16233f; letter-spacing: 2pt; }
  .subtitle { font-size: 13pt; color: #5c6a8a; letter-spacing: 5pt; text-transform: uppercase; margin-top: 1mm; }
  .ornament { margin-top: 2.5mm; font-size: 8.5pt; color: #c9a24b; }
  .ornament .rl { display: inline-block; width: 22mm; height: 0.8pt; background: #c9a24b; vertical-align: middle; margin: 0 3mm; }

  /* STUDENT STRIP */
  .ms-student { position: absolute; top: 74mm; left: 16mm; right: 16mm; text-align: center; font-family: 'Times New Roman', 'DejaVu Serif', serif; }
  .ms-student-name {
    font-size: 24pt; font-weight: bold; font-style: italic; color: #16336e;
    padding-bottom: 1.6mm; display: inline-block; border-bottom: 1pt solid #c9a24b;
    max-width: 176mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .ms-student-sub { font-size: 10.5pt; color: #5c6a8a; margin-top: 1.6mm; }
  .ms-student-sub b { color: #16233f; }

  /* INFO ROWS (details + summary) — horizontal strips matching certificate language */
  .ms-info { position: absolute; left: 20mm; right: 16mm; z-index: 5; }
  .ms-info:after { content: ""; display: table; clear: both; }
  .ms-info-item { float: left; text-align: center; padding: 0 2mm; box-sizing: border-box; }
  .ms-info-icon-ring { display: table; width: 7mm; height: 7mm; margin: 0 auto 1.4mm; }
  .ms-info-icon-cell { display: table-cell; width: 7mm; height: 7mm; border-radius: 50%; border: 0.8pt solid rgba(201,162,75,.65); background: #fff; text-align: center; vertical-align: middle; }
  .ms-info-icon { color: #16336e; font-size: 9.5pt; }
  .ms-info-label { font-size: 8.5pt; text-transform: uppercase; letter-spacing: 0.4pt; color: #8b96b3; }
  .ms-info-value { font-size: 11.5pt; font-weight: bold; color: #16233f; margin-top: 0.6mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .ms-info-value.accent { color: #c9924b; }
  .ms-info-value.pass { color: #1c7a44; }
  .ms-info-value.fail { color: #a91515; }

  /* MARKS TABLE */
  .ms-marks { position: absolute; top: 120mm; left: 16mm; right: 16mm; }
  .ms-marks table { width: 100%; border-collapse: collapse; }
  .ms-marks th {
    height: 9.5mm; padding: 0 3mm; background: #16336e; color: #d9b65c; border: 0.5pt solid #c9a24b;
    font-size: 11pt; text-transform: uppercase; font-weight: bold; letter-spacing: 0.3pt;
    box-shadow: inset 0 -0.7pt 0 rgba(217,182,92,.5);
    text-align: center; vertical-align: middle;
  }
  .ms-marks th:first-child { text-align: left; }
  .ms-marks td {
    height: 8mm; padding: 0 3mm; border: 0.5pt solid #c9a24b; color: #16233f; font-size: 11pt; font-weight: bold;
    text-align: left; vertical-align: middle;
  }
  .ms-marks td.num { text-align: center; }
  .ms-marks tr.total td { background: #16336e; color: #fff; font-size: 12.5pt; }

  /* SIGNATURE + QR (bottom row) */
  .ms-sign-col { position: absolute; left: 35mm; top: 219mm; width: 60mm; text-align: center; }
  .ms-seal-img-wrap { width: 20mm; height: 20mm; margin: 0 auto 1.5mm; border-radius: 50%; overflow: hidden; box-shadow: 0 0 0 1pt rgba(201,162,75,.55), 0 2mm 3mm -1.5mm rgba(0,0,0,.25); }
  .ms-seal-img { width: 100%; height: 100%; }
  .ms-sign-name { font-size: 11pt; font-weight: bold; color: #16233f; }
  .ms-sign-role { font-size: 9.5pt; color: #5c6a8a; margin-top: 0.5mm; }

  .ms-qr-col { position: absolute; left: 115mm; top: 219mm; width: 60mm; text-align: center; }
  .ms-qr-wrap { position: relative; display: inline-block; }
  .ms-qr-img { width: 26mm; height: 26mm; border: 1pt solid #c9a24b; padding: 1.2mm; background: #fff; box-shadow: 0 2mm 3mm -1.5mm rgba(0,0,0,.25); }
  .ms-qr-bracket { position: absolute; width: 4mm; height: 4mm; border-color: #16336e; border-style: solid; border-width: 0; }
  .ms-qr-bracket.tl { top: -1.6mm; left: -1.6mm; border-top-width: 1.1pt; border-left-width: 1.1pt; }
  .ms-qr-bracket.tr { top: -1.6mm; right: -1.6mm; border-top-width: 1.1pt; border-right-width: 1.1pt; }
  .ms-qr-bracket.bl { bottom: -1.6mm; left: -1.6mm; border-bottom-width: 1.1pt; border-left-width: 1.1pt; }
  .ms-qr-bracket.br { bottom: -1.6mm; right: -1.6mm; border-bottom-width: 1.1pt; border-right-width: 1.1pt; }
  .ms-qr-caption { font-size: 8pt; color: #5c6a8a; margin-top: 1.5mm; line-height: 1.3; }

  .ms-issue-date { position: absolute; top: 259mm; left: 16mm; right: 16mm; text-align: center; font-size: 9.5pt; color: #5c6a8a; }
  .ms-issue-date b { color: #16233f; }

  /* FOOTER */
  .footer-bar {
    position: absolute; left: 9mm; right: 9mm; bottom: 20mm;
    background: #16336e; color: #fff; padding: 2.6mm 8mm; font-size: 9pt;
    border-top: 0.9pt solid #c9a24b;
  }
  .footer-table { width: 100%; border-collapse: collapse; }
  .footer-table td { color: rgba(255,255,255,.92); font-size: 8.8pt; }

  .disclaimer {
    position: absolute; left: 18mm; right: 18mm; bottom: 9.5mm;
    text-align: center; font-size: 7pt; color: #8b96b3; line-height: 1.4;
    font-family: 'Times New Roman', 'DejaVu Serif', serif; font-style: italic; letter-spacing: 0.15pt;
  }
</style>
</head>
<body>
  <div class="sheet">
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="accent-spine"></div>
    <div class="corner-mark tl"></div>
    <div class="corner-mark tr"></div>
    <div class="corner-mark bl"></div>
    <div class="corner-mark br"></div>

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
        <table class="badge-table"><tr>
          @if (file_exists(public_path('assets/img/logoiso.png')))
            <td class="badge-cell">
              <div class="badge-chip"><img class="badge-img" src="file://{{ str_replace('\\', '/', public_path('assets/img/logoiso.png')) }}"></div>
              <div class="badge-cap">ISO 9001:2015</div>
            </td>
          @endif
          @if (file_exists(public_path('assets/img/logo-msme.png')))
            <td class="badge-cell">
              <div class="badge-chip"><img class="badge-img" src="file://{{ str_replace('\\', '/', public_path('assets/img/logo-msme.png')) }}"></div>
              <div class="badge-cap">MSME Registered</div>
            </td>
          @endif
        </tr></table>
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

    <div class="ms-info" style="top: 98mm;">
      <div class="ms-info-item" style="width: 33.33%">
        <span class="ms-info-icon-ring"><span class="ms-info-icon-cell"><i class="fa-ico ms-info-icon" style="font-size:6.8pt">&#xf19d;</i></span></span>
        <div class="ms-info-label">Course</div>
        <div class="ms-info-value accent">{{ $fit(strtoupper($courseName), 22) }}</div>
      </div>
      <div class="ms-info-item" style="width: 33.33%">
        <span class="ms-info-icon-ring"><span class="ms-info-icon-cell"><i class="fa-ico ms-info-icon" style="font-size:7pt">&#xf2c2;</i></span></span>
        <div class="ms-info-label">Enrollment No.</div>
        <div class="ms-info-value">{{ $fit($enrollmentNo, 20) }}</div>
      </div>
      <div class="ms-info-item" style="width: 33.33%">
        <span class="ms-info-icon-ring"><span class="ms-info-icon-cell"><i class="fa-ico ms-info-icon" style="font-size:13pt">&#xf017;</i></span></span>
        <div class="ms-info-label">Duration</div>
        <div class="ms-info-value">{{ $fit($duration, 18) }}</div>
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

    <div class="ms-info" style="top: 196mm;">
      <div class="ms-info-item" style="width: 33.33%">
        <span class="ms-info-icon-ring"><span class="ms-info-icon-cell"><i class="fa-ico ms-info-icon" style="font-size:11.3pt">&#xf295;</i></span></span>
        <div class="ms-info-label">Percentage</div>
        <div class="ms-info-value">{{ $percentage }}%</div>
      </div>
      <div class="ms-info-item" style="width: 33.33%">
        <span class="ms-info-icon-ring"><span class="ms-info-icon-cell"><i class="fa-ico ms-info-icon" style="font-size:9.3pt">&#xf005;</i></span></span>
        <div class="ms-info-label">Grade</div>
        <div class="ms-info-value accent">{{ $grade }}</div>
      </div>
      <div class="ms-info-item" style="width: 33.33%">
        <span class="ms-info-icon-ring"><span class="ms-info-icon-cell"><i class="fa-ico ms-info-icon" style="font-size:9.3pt">&#xf058;</i></span></span>
        <div class="ms-info-label">Result</div>
        <div class="ms-info-value {{ $result === 'PASS' ? 'pass' : 'fail' }}">{{ $result }}</div>
      </div>
    </div>

    <div class="ms-sign-col">
      @if (!empty($signatureImageDataUri))
        <div class="ms-seal-img-wrap">
          <img class="ms-seal-img" src="{{ $signatureImageDataUri }}">
        </div>
      @endif
      <div class="ms-sign-name">Authorized Signatory</div>
      <div class="ms-sign-role">R-Tech Computer</div>
    </div>

    <div class="ms-qr-col">
      <div class="ms-qr-wrap">
        <span class="ms-qr-bracket tl"></span><span class="ms-qr-bracket tr"></span><span class="ms-qr-bracket bl"></span><span class="ms-qr-bracket br"></span>
        <img class="ms-qr-img" src="{{ $qrDataUri }}">
      </div>
      <div class="ms-qr-caption">Scan QR Code<br>to Verify Marksheet</div>
    </div>

    <div class="ms-issue-date">Issue Date: <b>{{ $issueDate }}</b></div>

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
