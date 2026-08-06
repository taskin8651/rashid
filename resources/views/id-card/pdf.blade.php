<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 0; size: 53.98mm 85.6mm; }
  * { box-sizing: border-box; }
  body { margin: 0; font-family: 'Helvetica', 'DejaVu Sans', sans-serif; }
  .side {
    position: relative;
    width: 53.98mm;
    height: 85.6mm;
    overflow: hidden;
    page-break-after: always;
  }
  .side:last-child { page-break-after: avoid; }

  /* FRONT — matches admin panel brand gradient (#2563eb -> #3b82f6) */
  .front {
    background-color: #2563eb;
    background-image: linear-gradient(150deg, #1d4ed8 0%, #2563eb 55%, #3b82f6 100%);
    color: #fff;
    text-align: center;
  }
  .front-watermark {
    position: absolute;
    top: -12mm; right: -12mm;
    width: 30mm; height: 30mm;
    border-radius: 50%;
    border: 2mm solid rgba(255,255,255,.07);
  }
  .front-watermark2 {
    position: absolute;
    bottom: -16mm; left: -16mm;
    width: 34mm; height: 34mm;
    border-radius: 50%;
    border: 0.6pt solid rgba(255,255,255,.12);
  }

  .logo-badge {
    position: relative;
    margin: 2.8mm auto 0;
    width: 11mm; height: 11mm;
    border-radius: 50%;
    background-color: #ffffff;
    border: 0.8pt solid rgba(255,255,255,.85);
    text-align: center;
    z-index: 2;
  }
  .logo-badge img { width: 8mm; height: auto; margin-top: 3.75mm; }

  .brand { font-size: 7pt; font-weight: bold; letter-spacing: 0.5pt; color: #ffffff; margin-top: 1.2mm; z-index: 2; position: relative; }
  .tagline { font-size: 4pt; color: rgba(255,255,255,.8); letter-spacing: 1.1pt; text-transform: uppercase; margin-top: 0.5mm; z-index: 2; position: relative; }

  .gold-rule { margin: 1.4mm auto 0; width: 32mm; height: 0.5pt; background-color: rgba(255,255,255,.35); z-index: 2; position: relative; }

  .photo-wrap {
    position: relative;
    margin: 2mm auto 0;
    width: 15mm; height: 15mm;
    border-radius: 50%;
    background-color: rgba(255,255,255,.12);
    border: 0.9pt solid rgba(255,255,255,.85);
    text-align: center;
    z-index: 2;
    overflow: hidden;
  }
  .photo-wrap img { width: 15mm; height: 15mm; }
  .photo-fallback { line-height: 15mm; font-size: 12pt; font-weight: bold; color: rgba(255,255,255,.85); }

  .name { font-size: 8.4pt; font-weight: bold; color: #fff; margin-top: 1.8mm; padding: 0 3mm; z-index: 2; position: relative; }
  .role {
    display: inline-block;
    font-size: 4.4pt; font-weight: bold; color: #1d4ed8; background-color: #ffffff;
    text-transform: uppercase; letter-spacing: 0.5pt;
    padding: 0.6mm 2.2mm; border-radius: 3mm; margin-top: 0.9mm; z-index: 2; position: relative;
  }

  .info { margin-top: 2mm; padding: 0 5.5mm; text-align: left; z-index: 2; position: relative; }
  .row { font-size: 5.6pt; color: #fff; font-weight: bold; margin-bottom: 1.6mm; line-height: 1.2; }
  .label { display: block; color: rgba(255,255,255,.62); text-transform: uppercase; font-size: 4pt; font-weight: normal; letter-spacing: 0.3pt; margin-bottom: 0.2mm; }

  .front-accent { position: absolute; left: 0; right: 0; bottom: 0; height: 6.6mm; background-color: #1d4ed8; background-image: linear-gradient(90deg, #1e3fa8 0%, #1d4ed8 50%, #1e3fa8 100%); z-index: 2; }
  .valid { position: absolute; bottom: 2.3mm; left: 0; right: 0; font-size: 4.4pt; font-weight: bold; color: #fff; letter-spacing: 0.2pt; z-index: 3; text-align: center; }

  /* BACK — light theme surface, matches admin card styling */
  .back { background-color: #f3f7fc; color: #0f172a; }
  .back-topbar {
    background-color: #2563eb; background-image: linear-gradient(150deg, #1d4ed8 0%, #3b82f6 100%);
    height: 12mm; position: relative; text-align: center; padding-top: 1.8mm;
  }
  .back-logo {
    width: 7.4mm; height: 7.4mm; border-radius: 50%; background-color: #fff; margin: 0 auto;
    border: 0.6pt solid rgba(255,255,255,.85); text-align: center;
  }
  .back-logo img { width: 5.4mm; height: auto; margin-top: 2.5mm; }
  .back-topbar .brand { font-size: 5.6pt; font-weight: bold; color: #fff; letter-spacing: 0.4pt; margin-top: 0.9mm; }

  .back-inner { padding: 2mm 4.5mm 0; }
  .sec-title { font-size: 5.2pt; font-weight: bold; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5pt; margin-bottom: 0.8mm; border-bottom: 0.5pt solid rgba(37,99,235,.35); padding-bottom: 0.5mm; }
  .terms { font-size: 4.1pt; line-height: 1.3; color: #5b6b85; margin-bottom: 1.1mm; }

  .details { margin-bottom: 0.9mm; }
  .drow { font-size: 4.8pt; color: #0f172a; font-weight: bold; margin-bottom: 0.5mm; line-height: 1.15; }
  .dlabel { display: block; color: #5b6b85; text-transform: uppercase; font-size: 3.6pt; font-weight: normal; letter-spacing: 0.3pt; margin-bottom: 0.15mm; }

  .code-box {
    background-color: #2563eb;
    background-image: linear-gradient(150deg, #1d4ed8 0%, #3b82f6 100%);
    border-radius: 1mm;
    padding: 1.1mm;
    text-align: center;
    font-family: 'Courier New', monospace;
    font-size: 6.4pt;
    font-weight: bold;
    letter-spacing: 1.2pt;
    color: #fff;
    margin-bottom: 1.1mm;
  }
  .contact-line { font-size: 4pt; color: #5b6b85; margin-bottom: 0.5mm; text-align: center; }
  .contact-line b { color: #0f172a; }

  .sign-area { margin-top: 1mm; border-top: 0.5pt solid rgba(15,23,42,.12); padding-top: 0.6mm; text-align: center; }
  .stamp-img { width: 19mm; height: auto; }
  .sign-caption { font-size: 3.8pt; color: #5b6b85; letter-spacing: 0.3pt; margin-top: 0.2mm; }
</style>
</head>
<body>
  <div class="side front">
    <div class="front-watermark"></div>
    <div class="front-watermark2"></div>

    <div class="logo-badge">
      @if (file_exists(public_path('assets/img/logo.png')))
        <img src="file://{{ str_replace('\\', '/', public_path('assets/img/rtech-logo-generated1.png')) }}">
      @endif
    </div>
    <div class="brand">R-TECH COMPUTER</div>
    <div class="tagline">Student Identity Card</div>
    <div class="gold-rule"></div>

    <div class="photo-wrap">
      @if ($user->photoPath())
        <img src="file://{{ str_replace('\\', '/', $user->photoPath()) }}">
      @else
        <div class="photo-fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
      @endif
    </div>

    <div class="name">{{ $user->name }}</div>
    <div class="role">Student</div>

    <div class="info">
      <div class="row"><span class="label">ID</span>{{ $user->student_code }}</div>
      <div class="row"><span class="label">Course</span>{{ $course->name ?? 'R-Tech Computer' }}</div>
      <div class="row"><span class="label">Date of Birth</span>{{ optional($user->date_of_birth)->format('d M Y') ?? '—' }}</div>
      <div class="row"><span class="label">Blood Group</span>{{ $user->blood_group ?: '—' }}</div>
    </div>

    <div class="front-accent"></div>
    <div class="valid">Valid Thru {{ now()->addYear()->format('m/Y') }}</div>
  </div>

  <div class="side back">
    <div class="back-topbar">
      <div class="back-logo">
        @if (file_exists(public_path('assets/img/rtech-logo-generated1.png')))
          <img src="file://{{ str_replace('\\', '/', public_path('assets/img/rtech-logo-generated.png')) }}">
        @endif
      </div>
      <div class="brand">R-TECH COMPUTER</div>
    </div>
    <div class="back-inner">
      <div class="sec-title">Student Details</div>
      <div class="details">
        <div class="drow"><span class="dlabel">Phone</span>{{ $user->phone ?: '—' }}</div>
        <div class="drow"><span class="dlabel">Guardian / Father's Name</span>{{ $user->guardian_name ?: '—' }}</div>
        <div class="drow"><span class="dlabel">Emergency Contact</span>{{ $user->emergency_contact ?: '—' }}</div>
        <div class="drow" style="margin-bottom:0"><span class="dlabel">Address</span>{{ $user->address ?: '—' }}</div>
      </div>

      <div class="sec-title">Terms of Use</div>
      <div class="terms">
        This card is R-Tech Computer property and must be carried on campus at all times.
        Non-transferable — report loss immediately. Misuse may lead to disciplinary action.
      </div>

      <div class="code-box">{{ $user->student_code }}</div>

      <div class="contact-line"><b>If found, please return to:</b></div>
      <div class="contact-line">R-Tech Computer, Mogalkuan, Etwari Bazar,<br>Sohsarai Road, Bihar Sharif, Nalanda-803117, Bihar</div>
      <div class="contact-line"><b>Phone:</b> +91 9117744925 &middot; rtechcomputer.in</div>

      <div class="sign-area">
        @if (file_exists(public_path('assets/img/sign2.png')))
          <img class="stamp-img" src="file://{{ str_replace('\\', '/', public_path('assets/img/sign2.png')) }}">
        @endif
        <div class="sign-caption">Authorized Signatory &amp; Seal</div>
      </div>
    </div>
  </div>
</body>
</html>
