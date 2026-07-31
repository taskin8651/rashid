<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 0; size: A4 landscape; }
  * { box-sizing: border-box; }
  body {
    margin: 0;
    font-family: 'Helvetica', 'DejaVu Sans', sans-serif;
    color: #16233f;
  }
  .sheet {
    position: relative;
    width: 297mm;
    height: 210mm;
    background: #fdfcf8;
  }
  .border-outer {
    position: absolute;
    top: 6mm; left: 6mm; right: 6mm; bottom: 6mm;
    border: 3.2pt solid #16336e;
    border-radius: 9mm;
  }
  .border-inner {
    position: absolute;
    top: 9mm; left: 9mm; right: 9mm; bottom: 9mm;
    border: 1pt solid #c9a24b;
    border-radius: 7mm;
  }

  /* HEADER ROW: logo/brand left, credential badges right */
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

  .badges { position: absolute; right: 0; top: 0; width: 62mm; text-align: right; }
  .badge {
    display: inline-block;
    width: 20mm; height: 20mm;
    border-radius: 10mm;
    border: 1.4pt solid #16336e;
    text-align: center;
    padding-top: 3.2mm;
    margin-left: 3mm;
    vertical-align: top;
  }
  .badge.gold { border-color: #c9a24b; }
  .badge-stars { font-size: 5pt; color: #c9a24b; letter-spacing: 1pt; margin-bottom: 0.3mm; }
  .badge.gold .badge-stars { color: #16336e; }
  .badge-txt { font-size: 5.6pt; font-weight: bold; color: #16336e; line-height: 1.35; letter-spacing: 0.2pt; }
  .badge.gold .badge-txt { color: #b8860b; }

  /* TITLE */
  .title-block { position: absolute; top: 36mm; left: 16mm; right: 16mm; text-align: center; }
  .title { font-size: 24pt; font-weight: bold; color: #16233f; letter-spacing: 2pt; }
  .subtitle { font-size: 9.5pt; color: #5c6a8a; letter-spacing: 4pt; text-transform: uppercase; margin-top: 1mm; }
  .ornament { margin-top: 2.5mm; font-size: 8pt; color: #c9a24b; }
  .ornament .rl { display: inline-block; width: 20mm; height: 0.8pt; background: #c9a24b; vertical-align: middle; margin: 0 3mm; }

  /* LEFT ICON RAIL */
  .rail { position: absolute; top: 56mm; left: 16mm; width: 47mm; }
  .rail-item { margin-bottom: 3.4mm; padding-left: 9mm; position: relative; min-height: 7mm; }
  .rail-dot { position: absolute; left: 0; top: 0; width: 7mm; height: 7mm; border-radius: 3.5mm; background: #16336e; }
  .rail-label { font-size: 6.5pt; text-transform: uppercase; letter-spacing: 0.4pt; color: #8b96b3; }
  .rail-value {
    font-size: 8.8pt; font-weight: bold; color: #16233f; margin-top: 0.4mm;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 38mm;
  }

  /* STUDENT INFO — colon-format two column detail grid */
  .info-block { position: absolute; top: 56mm; left: 70mm; right: 62mm; }
  .info-col { position: absolute; top: 0; width: 48%; }
  .info-col-l { left: 0; }
  .info-col-r { right: 0; }
  .kv { margin-bottom: 3.2mm; font-size: 9.5pt; }
  .kv .k { color: #8b96b3; font-size: 7pt; text-transform: uppercase; letter-spacing: 0.4pt; }
  .kv .v {
    display: block; color: #16233f; font-weight: bold; font-size: 9.8pt; margin-top: 0.4mm;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }

  /* QR + SEAL — top right column */
  .qr-col { position: absolute; top: 40mm; right: 16mm; width: 40mm; text-align: center; }
  .qr-img { width: 20mm; height: 20mm; border: 1pt solid #c9a24b; padding: 1mm; background: #fff; }
  .qr-caption { font-size: 6pt; color: #5c6a8a; margin-top: 1mm; line-height: 1.3; }
  .seal {
    width: 18mm; height: 18mm;
    border: 1.4pt solid #c9a24b;
    border-radius: 9mm;
    margin: 2.5mm auto 0;
    text-align: center;
    padding-top: 3.8mm;
  }
  .seal-inner { font-size: 5pt; font-weight: bold; color: #b8860b; letter-spacing: 0.2pt; line-height: 1.4; }

  /* SUBJECTS TABLE */
  .subj-block { position: absolute; top: 98mm; left: 16mm; right: 16mm; }
  .subj-table { width: 100%; border-collapse: collapse; }
  .subj-table th {
    background: #16336e; color: #d9b65c; font-size: 8.5pt; text-transform: uppercase;
    letter-spacing: 0.4pt; padding: 2.6mm 4mm; text-align: left;
  }
  .subj-table th.num, .subj-table td.num { text-align: center; }
  .subj-table td {
    font-size: 9.5pt; padding: 2.1mm 4mm; border-bottom: 0.6pt solid #e4dcc8; color: #16233f;
  }
  .subj-table tr.total td {
    font-weight: bold; border-top: 1pt solid #c9a24b; border-bottom: none; background: #f6f1e3;
  }

  /* RESULT ROW */
  .result-row { position: absolute; top: 165mm; left: 16mm; right: 16mm; }
  .result-table { width: 100%; border-collapse: collapse; }
  .result-cell { width: 33.33%; text-align: center; }
  .result-lbl { font-size: 7pt; text-transform: uppercase; letter-spacing: 0.5pt; color: #8b96b3; }
  .result-val { font-size: 13pt; font-weight: bold; color: #16233f; margin-top: 0.8mm; }
  .result-val.pass { color: #1a7a3c; }
  .result-val.fail { color: #b3261e; }

  .sign-block { position: absolute; top: 177mm; right: 16mm; width: 60mm; text-align: center; }
  .sign-line { border-top: 0.8pt solid #45516e; width: 50mm; margin: 0 auto 1mm; padding-top: 1mm; }
  .sign-name { font-size: 9pt; font-weight: bold; color: #16233f; }
  .sign-role { font-size: 7.5pt; color: #5c6a8a; margin-top: 0.5mm; }

  /* FOOTER BAR */
  .footer-bar {
    position: absolute;
    left: 9mm; right: 9mm; bottom: 12mm;
    background: #16336e;
    color: #fff;
    padding: 2.2mm 8mm;
    font-size: 8pt;
  }
  .footer-table { width: 100%; border-collapse: collapse; }
  .footer-table td { color: rgba(255,255,255,.92); font-size: 7.8pt; }

  .disclaimer {
    position: absolute; left: 16mm; right: 16mm; bottom: 7mm;
    text-align: center; font-size: 5.6pt; color: #9aa3ba; line-height: 1.4;
  }
</style>
</head>
<body>
  <div class="sheet">
    <div class="border-outer"></div>
    <div class="border-inner"></div>

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
        <div class="badge"><div class="badge-txt"><div class="badge-stars">&#9733; &#9733; &#9733;</div>ISO<br>9001:2015<br>CERTIFIED</div></div>
        <div class="badge gold"><div class="badge-txt"><div class="badge-stars">&#9733; &#9733; &#9733;</div>UDYAM<br>MSME<br>REGISTERED</div></div>
      </div>
    </div>

    <div class="title-block">
      <div class="title">MARKSHEET</div>
      <div class="subtitle">Statement of Marks</div>
      <div class="ornament"><span class="rl"></span>&#9670;<span class="rl"></span></div>
    </div>

    <div class="rail">
      <div class="rail-item"><div class="rail-dot"></div><div class="rail-label">Student Name</div><div class="rail-value">{{ $certificate->user->name }}</div></div>
      <div class="rail-item"><div class="rail-dot"></div><div class="rail-label">Course</div><div class="rail-value">{{ $certificate->course->name }}</div></div>
      <div class="rail-item"><div class="rail-dot"></div><div class="rail-label">Batch</div><div class="rail-value">{{ $certificate->batch_name ?: '—' }}</div></div>
      <div class="rail-item"><div class="rail-dot"></div><div class="rail-label">Grade</div><div class="rail-value">{{ $certificate->grade() }}</div></div>
    </div>

    <div class="info-block">
      <div class="info-col info-col-l">
        <div class="kv"><span class="k">Student Name</span><span class="v">{{ $certificate->user->name }}</span></div>
        <div class="kv"><span class="k">Father's Name</span><span class="v">{{ $certificate->father_name ?: '—' }}</span></div>
        <div class="kv"><span class="k">Course</span><span class="v">{{ $certificate->course->name }}</span></div>
        <div class="kv"><span class="k">Enrollment No.</span><span class="v">{{ $certificate->cert_code }}</span></div>
      </div>
      <div class="info-col info-col-r">
        <div class="kv"><span class="k">Roll No.</span><span class="v">{{ $certificate->roll_no ?: $certificate->cert_code }}</span></div>
        <div class="kv"><span class="k">Batch</span><span class="v">{{ $certificate->batch_name ?: '—' }}</span></div>
        <div class="kv"><span class="k">Date of Issue</span><span class="v">{{ $certificate->issued_date->format('d M Y') }}</span></div>
        <div class="kv"><span class="k">Duration</span><span class="v">{{ $certificate->course->duration_text ?: '—' }}</span></div>
      </div>
    </div>

    <div class="qr-col">
      <img class="qr-img" src="{{ $qrDataUri }}">
      <div class="qr-caption">Scan QR Code<br>to Verify Marksheet</div>
      <div class="seal"><div class="seal-inner">EMPOWERING<br>SKILLS<br>BUILDING<br>CAREERS</div></div>
    </div>

    <div class="subj-block">
      <table class="subj-table">
        <thead>
          <tr>
            <th style="width:58%">Subjects</th>
            <th class="num" style="width:21%">Maximum Marks</th>
            <th class="num" style="width:21%">Marks Obtained</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($certificate->subjects as $subject)
            <tr>
              <td>{{ $subject->subject }}</td>
              <td class="num">{{ $subject->max_marks }}</td>
              <td class="num">{{ $subject->marks_obtained ?? '—' }}</td>
            </tr>
          @endforeach
          <tr class="total">
            <td>TOTAL MARKS</td>
            <td class="num">{{ $certificate->totalMaxMarks() }}</td>
            <td class="num">{{ $certificate->totalMarksObtained() }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="result-row">
      <table class="result-table">
        <tr>
          <td class="result-cell"><div class="result-lbl">Percentage</div><div class="result-val">{{ rtrim(rtrim(number_format($certificate->percentage(), 2), '0'), '.') }}%</div></td>
          <td class="result-cell"><div class="result-lbl">Grade</div><div class="result-val">{{ $certificate->grade() }}</div></td>
          <td class="result-cell"><div class="result-lbl">Result</div><div class="result-val {{ $certificate->result() === 'PASS' ? 'pass' : 'fail' }}">{{ $certificate->result() }}</div></td>
        </tr>
      </table>
    </div>

    <div class="sign-block">
      <div class="sign-line"></div>
      <div class="sign-name">Authorized Signatory</div>
      <div class="sign-role">R-Tech Computer</div>
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
