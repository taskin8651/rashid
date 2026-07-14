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
    top: 8mm; left: 8mm; right: 8mm; bottom: 8mm;
    border: 2.6pt solid #16336e;
  }
  .border-inner {
    position: absolute;
    top: 11mm; left: 11mm; right: 11mm; bottom: 11mm;
    border: 0.9pt solid #c9a24b;
  }
  .corner {
    position: absolute;
    width: 16mm;
    height: 16mm;
    border-color: #c9a24b;
  }
  .corner-tl { top: 8mm; left: 8mm; border-top: 3pt solid; border-left: 3pt solid; }
  .corner-tr { top: 8mm; right: 8mm; border-top: 3pt solid; border-right: 3pt solid; }
  .corner-bl { bottom: 8mm; left: 8mm; border-bottom: 3pt solid; border-left: 3pt solid; }
  .corner-br { bottom: 8mm; right: 8mm; border-bottom: 3pt solid; border-right: 3pt solid; }

  .content { position: relative; padding: 20mm 26mm 0; text-align: center; }

  .brand-row { margin-bottom: 2mm; }
  .brand-name { font-size: 20pt; font-weight: bold; color: #16336e; letter-spacing: 1pt; }
  .brand-tag { font-size: 8pt; color: #b8860b; letter-spacing: 3pt; text-transform: uppercase; margin-top: 1mm; }

  .rule {
    width: 26mm; height: 0.8pt; background: #c9a24b;
    margin: 6mm auto;
  }

  .title { font-size: 27pt; font-weight: bold; color: #16233f; letter-spacing: 1.5pt; margin-bottom: 3mm; }
  .subtitle { font-size: 10.5pt; color: #5c6a8a; letter-spacing: 3pt; text-transform: uppercase; margin-bottom: 10mm; }

  .lead { font-size: 11.5pt; color: #45516e; margin-bottom: 5mm; }
  .student-name {
    font-size: 26pt;
    font-weight: bold;
    color: #16336e;
    padding-bottom: 3mm;
    margin: 0 auto 6mm;
    display: inline-block;
    border-bottom: 1pt solid #c9a24b;
    min-width: 120mm;
  }

  .body-text { font-size: 11.5pt; color: #45516e; line-height: 1.9; margin: 0 auto; max-width: 190mm; }
  .course-name { font-size: 16pt; font-weight: bold; color: #b8860b; margin: 3mm 0; }

  .footer {
    position: absolute;
    left: 26mm; right: 26mm; bottom: 22mm;
  }
  .footer-table { width: 100%; border-collapse: collapse; }
  .footer-table td { width: 33.33%; vertical-align: bottom; font-size: 9pt; color: #5c6a8a; }
  .sig-line { border-top: 0.8pt solid #45516e; width: 55mm; margin: 0 auto 2mm; padding-top: 2mm; }
  .meta-label { text-transform: uppercase; letter-spacing: 0.6pt; font-size: 7.5pt; color: #8b96b3; }
  .meta-value { font-size: 9.5pt; color: #16233f; font-weight: bold; margin-top: 0.5mm; }

  .seal {
    width: 26mm; height: 26mm;
    border: 2pt solid #b8860b;
    border-radius: 13mm;
    margin: 0 auto;
    text-align: center;
    padding-top: 7.5mm;
  }
  .seal-inner {
    font-size: 7.5pt;
    font-weight: bold;
    color: #b8860b;
    letter-spacing: 0.5pt;
    line-height: 1.5;
  }

  .credentials {
    position: absolute;
    left: 26mm; right: 26mm; bottom: 12.5mm;
    text-align: center;
    font-size: 7.5pt;
    color: #8b96b3;
    letter-spacing: 0.3pt;
  }
  .credentials b { color: #5c6a8a; }
</style>
</head>
<body>
  <div class="sheet">
    <div class="border-outer"></div>
    <div class="border-inner"></div>
    <div class="corner corner-tl"></div>
    <div class="corner corner-tr"></div>
    <div class="corner corner-bl"></div>
    <div class="corner corner-br"></div>

    <div class="content">
      <div class="brand-row">
        <div class="brand-name">R-TECH COMPUTER</div>
        <div class="brand-tag">Skill Se Placement Tak</div>
      </div>

      <div class="rule"></div>

      <div class="title">CERTIFICATE</div>
      <div class="subtitle">of Completion</div>

      <div class="lead">This is to proudly certify that</div>
      <div class="student-name">{{ $certificate->user->name }}</div>

      <div class="body-text">
        has successfully completed the course
        <div class="course-name">{{ $certificate->course->name }}</div>
        with dedication and diligence, meeting all requirements set by R-Tech Computer,
        and is hereby awarded this certificate of completion.
      </div>

      <div class="footer">
        <table class="footer-table">
          <tr>
            <td style="text-align:left">
              <div class="meta-label">Certificate No.</div>
              <div class="meta-value">{{ $certificate->cert_code }}</div>
              <div class="meta-label" style="margin-top:2.5mm">Date of Issue</div>
              <div class="meta-value">{{ $certificate->issued_date->format('d F Y') }}</div>
            </td>
            <td style="text-align:center">
              <div class="seal"><div class="seal-inner">R-TECH<br>VERIFIED<br>&#9733;&#9733;&#9733;</div></div>
            </td>
            <td style="text-align:right">
              <div class="sig-line" style="margin-left:auto;margin-right:0"></div>
              <div class="meta-value" style="text-align:right">Authorized Signatory</div>
              <div class="meta-label" style="text-align:right">R-Tech Computer</div>
            </td>
          </tr>
        </table>
      </div>

      <div class="credentials">
        <b>ISO 9001:2015</b> Certified Institute (UKAF CERT &middot; Cert. No. UK-1370-6420) &nbsp;|&nbsp;
        <b>Udyam Registered</b> MSME, Govt. of India (UDYAM-BR-24-0042559) &nbsp;|&nbsp;
        Verify at rtechcomputer.in with Cert No. {{ $certificate->cert_code }}
      </div>
    </div>
  </div>
</body>
</html>
