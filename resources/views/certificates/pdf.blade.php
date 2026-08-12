<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 0; size: A4 landscape; }
  * { box-sizing: border-box; }
  body { margin: 0; font-family: 'Helvetica', 'DejaVu Sans', sans-serif; color: #16233f; }
  .sheet { position: relative; width: 297mm; height: 210mm; background: #fdfcf8; }

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
  .brand-text { position: absolute; left: 24mm; top: 0; width: 130mm; }
  .brand-name { font-size: 19pt; font-weight: bold; color: #16336e; letter-spacing: 0.5pt; }
  .brand-sub { font-size: 9pt; color: #45516e; margin-top: 1.5mm; }
  .brand-sub b { color: #16336e; }
  .brand-reg { font-size: 7.5pt; color: #5c6a8a; margin-top: 1mm; }
  .brand-reg b { color: #16336e; }

  .badges { position: absolute; right: 0; top: 0.5mm; }
  .badge-table { border-collapse: collapse; }
  .badge-cell { text-align: center; vertical-align: top; padding: 0 0 0 4mm; }
  .badge-chip {
    width: 19mm; height: 19mm; margin: 0 auto 1.4mm; background: #fff;
    border: 0.8pt solid #c9a24b; border-radius: 3mm; text-align: center; padding-top: 1.8mm;
    box-shadow: 0 1.6mm 2.6mm -1.6mm rgba(13,30,60,.35), inset 0 0 0 0.5pt rgba(201,162,75,.25);
  }
  .badge-img { height: 14mm; width: auto; }
  .badge-cap {
    font-family: 'Helvetica', 'DejaVu Sans', sans-serif; font-size: 6.1pt; font-weight: bold;
    color: #16336e; text-transform: uppercase; letter-spacing: 0.4pt; line-height: 1.3;
  }

  /* TITLE */
  .title-block { position: absolute; top: 40mm; left: 16mm; right: 16mm; text-align: center; font-family: 'Times New Roman', 'DejaVu Serif', serif; }
  .title { font-size: 38pt; font-weight: bold; color: #16233f; letter-spacing: 2pt; }
  .subtitle { font-size: 13pt; color: #5c6a8a; letter-spacing: 5pt; text-transform: uppercase; margin-top: 1mm; }
  .ornament { margin-top: 3mm; font-size: 8pt; color: #c9a24b; }
  .ornament .rl { display: inline-block; width: 22mm; height: 0.8pt; background: #c9a24b; vertical-align: middle; margin: 0 3mm; }

  /* BODY */
  .body-block { position: absolute; top: 78mm; left: 16mm; right: 16mm; text-align: center; font-family: 'Times New Roman', 'DejaVu Serif', serif; }
  .lead { font-size: 12.5pt; color: #45516e; margin-bottom: 2.5mm; }
  .student-name {
    font-size: 28pt; font-weight: bold; font-style: italic; color: #16336e;
    padding-bottom: 2mm; display: inline-block; border-bottom: 1pt solid #c9a24b;
    min-width: 130mm; max-width: 240mm; margin-bottom: 3mm;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .lead2 { font-size: 12.5pt; color: #45516e; margin-bottom: 2mm; }
  .course-ribbon {
    display: inline-block; background: #16336e; color: #d9b65c; font-weight: bold; font-size: 17pt;
    letter-spacing: 0.5pt; padding: 3mm 16mm; border-radius: 6mm; margin-bottom: 2mm;
    max-width: 240mm; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    box-shadow: 0 2mm 3mm -1.5mm rgba(13,30,60,.4), inset 0 0 0 0.7pt rgba(217,182,92,.55);
  }
  .desc { font-size: 10.5pt; color: #5c6a8a; line-height: 1.5; max-width: 155mm; margin: 0 auto; }

  /* WATERMARK */
  .watermark { position: absolute; width: 110mm; height: 110mm; top: 100mm; left: 93.5mm; opacity: 0.06; }

  /* LEFT ACCENT SPINE */
  .accent-spine { position: absolute; top: 9mm; bottom: 9mm; left: 9mm; width: 3mm; background: #c9a24b; border-top-left-radius: 7mm; border-bottom-left-radius: 7mm; }

  /* INFO ROW — horizontal strip, all certificate data, painted above the watermark */
  .info-row { position: absolute; top: 155mm; left: 20mm; right: 16mm; z-index: 5; }
  .info-row:after { content: ""; display: table; clear: both; }
  .info-item { float: left; width: 33%; text-align: center; padding: 0 3mm; box-sizing: border-box; }
  .info-icon-ring { display: table; width: 8mm; height: 8mm; margin: 0 auto 1.5mm; }
  .info-icon-cell { display: table-cell; width: 8mm; height: 8mm; border-radius: 50%; border: 0.8pt solid rgba(201,162,75,.65); background: #fff; text-align: center; vertical-align: middle; }
  .info-icon { color: #16336e; font-size: 11pt; }
  .info-label { font-size: 9.3pt; text-transform: uppercase; letter-spacing: 0.5pt; color: #8b96b3; }
  .info-value { font-size: 13.5pt; font-weight: bold; color: #16233f; margin-top: 0.6mm; }

  /* SIGNATURE + SEAL */
  .sign-col { position: absolute; left: 230mm; top: 55mm; width: 55mm; text-align: center; }
  .seal-img-wrap { width: 26mm; height: 26mm; margin: 0 auto 2mm; border-radius: 50%; overflow: hidden; box-shadow: 0 0 0 1pt rgba(201,162,75,.55), 0 2mm 3mm -1.5mm rgba(0,0,0,.25); }
  .seal-img { width: 100%; height: 100%; }
  .sign-name { font-size: 10pt; font-weight: bold; color: #16233f; }
  .sign-role { font-size: 8.5pt; color: #5c6a8a; margin-top: 0.5mm; }

  /* QR */
  .qr-col { position: absolute; right: 16mm; top: 105mm; width: 46mm; text-align: center; }
  .qr-wrap { position: relative; display: inline-block; }
  .qr-img { width: 28mm; height: 28mm; border: 1pt solid #c9a24b; padding: 1.2mm; background: #fff; box-shadow: 0 2mm 3mm -1.5mm rgba(0,0,0,.25); }
  .qr-bracket { position: absolute; width: 4mm; height: 4mm; border-color: #16336e; border-style: solid; border-width: 0; }
  .qr-bracket.tl { top: -1.6mm; left: -1.6mm; border-top-width: 1.1pt; border-left-width: 1.1pt; }
  .qr-bracket.tr { top: -1.6mm; right: -1.6mm; border-top-width: 1.1pt; border-right-width: 1.1pt; }
  .qr-bracket.bl { bottom: -1.6mm; left: -1.6mm; border-bottom-width: 1.1pt; border-left-width: 1.1pt; }
  .qr-bracket.br { bottom: -1.6mm; right: -1.6mm; border-bottom-width: 1.1pt; border-right-width: 1.1pt; }
  .qr-caption { font-size: 7pt; color: #5c6a8a; margin-top: 1.5mm; line-height: 1.3; }

  /* DECORATIVE SEAL (LEFT) — mirrors the QR column for visual balance */
  .seal-decor { position: absolute; left: 16mm; top: 88mm; width: 46mm; text-align: center; }
  .achievement-img { width: 50mm; height: auto; }
  .seal-decor-orn { margin-top: 2mm; font-size: 7pt; color: #c9a24b; }
  .seal-decor-orn .srl { display: inline-block; width: 8mm; height: 0.7pt; background: #c9a24b; vertical-align: middle; margin: 0 2mm; }

  /* FOOTER */
  .footer-bar {
    position: absolute; left: 9mm; right: 9mm; bottom: 20mm;
    background: #16336e; color: #fff; padding: 2.6mm 8mm; font-size: 8pt;
    border-top: 0.9pt solid #c9a24b;
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
    @if (file_exists(public_path('assets/img/logo.png')))
      <img class="watermark" src="file://{{ str_replace('\\', '/', public_path('assets/img/logo.png')) }}">
    @endif
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
      <div class="title">CERTIFICATE</div>
      <div class="subtitle">of Completion</div>
      <div class="ornament"><span class="rl"></span>&#9670;<span class="rl"></span></div>
    </div>

    @php
      $studentName = $certificate->student_name ?: $certificate->user->name;
      $courseName = strtoupper($certificate->course_name ?: $certificate->course->name);
      $courseNameSize = match (true) {
        strlen($courseName) <= 22 => '17pt',
        strlen($courseName) <= 30 => '14pt',
        strlen($courseName) <= 40 => '12pt',
        default => '10pt',
      };
    @endphp

    <div class="body-block">
      <div class="lead">This is to certify that</div>
      <div class="student-name">{{ $studentName }}</div>
      <div class="lead2">has successfully completed the</div>
      <div class="course-ribbon" style="font-size: {{ $courseNameSize }}">{{ $courseName }}</div>
      <div class="desc">The student has successfully fulfilled all the training requirements and demonstrated satisfactory performance during the course.</div>
    </div>

   <div class="info-row">

    <div class="info-item">
        <span class="info-icon-ring"><span class="info-icon-cell"><i class="fa-ico info-icon">&#xf0a3;</i></span></span>
        <div class="info-label">Certificate No.</div>
        <div class="info-value">{{ $certificate->cert_code }}</div>
    </div>

    <div class="info-item">
        <span class="info-icon-ring"><span class="info-icon-cell"><i class="fa-ico info-icon">&#xf073;</i></span></span>
        <div class="info-label">Issue Date</div>
        <div class="info-value">{{ $certificate->issued_date->format('d M Y') }}</div>
    </div>

    <div class="info-item">
        <span class="info-icon-ring"><span class="info-icon-cell"><i class="fa-ico info-icon">&#xf017;</i></span></span>
        <div class="info-label">Course Duration</div>
        <div class="info-value">{{ $certificate->course_duration_text ?: $certificate->course->duration_text ?: '—' }}</div>
    </div>

    

</div>
    <div class="sign-col">
      @if (!empty($signatureImageDataUri))
        <div class="seal-img-wrap">
          <img class="seal-img" src="{{ $signatureImageDataUri }}">
        </div>
      @endif
      <div class="sign-name">Authorized Signatory</div>
      <div class="sign-role">R-Tech Computer</div>
    </div>

    <div class="seal-decor">
      @if (file_exists(public_path('assets/img/achievement.png')))
        <img class="achievement-img" src="file://{{ str_replace('\\', '/', public_path('assets/img/achievement.png')) }}">
      @endif
      <div class="seal-decor-orn"><span class="srl"></span>&#9670;<span class="srl"></span></div>
    </div>

    <div class="qr-col">
      <div class="qr-wrap">
        <span class="qr-bracket tl"></span><span class="qr-bracket tr"></span><span class="qr-bracket bl"></span><span class="qr-bracket br"></span>
        <img class="qr-img" src="{{ $qrDataUri }}">
      </div>
      <div class="qr-caption">Scan QR Code<br>to Verify Certificate</div>
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
    <div class="disclaimer">This certificate is issued by R-Tech Computer. ISO 9001:2015 and Udyam Registration relate to the institute&rsquo;s certification and registration status and do not constitute government approval or accreditation of the individual course or student.</div>
  </div>
</body>
</html>
