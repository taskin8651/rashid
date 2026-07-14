<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 0; size: 85.6mm 53.98mm; }
  * { box-sizing: border-box; }
  body { margin: 0; font-family: 'Helvetica', 'DejaVu Sans', sans-serif; }
  .side {
    position: relative;
    width: 85.6mm;
    height: 53.98mm;
    overflow: hidden;
    page-break-after: always;
  }
  .side:last-child { page-break-after: avoid; }

  /* FRONT */
  .front { background-color: #2563eb; color: #fff; }
  .front-accent { position: absolute; left: 0; right: 0; bottom: 0; height: 8mm; background-color: #1d4ed8; }
  .header { padding: 3.2mm 4mm 0; }
  .brand { font-size: 8.5pt; font-weight: bold; letter-spacing: 0.3pt; }
  .tagline { font-size: 5pt; color: rgba(255,255,255,.75); letter-spacing: 0.8pt; text-transform: uppercase; margin-top: 0.5mm; }

  .photo-wrap {
    position: absolute;
    top: 12mm; left: 4mm;
    width: 18mm; height: 18mm;
    border-radius: 2mm;
    background-color: rgba(255,255,255,.15);
    border: 0.6pt solid rgba(255,255,255,.5);
    text-align: center;
  }
  .photo-wrap img { width: 18mm; height: 18mm; border-radius: 2mm; object-fit: cover; }
  .photo-fallback { line-height: 18mm; font-size: 16pt; font-weight: bold; color: rgba(255,255,255,.7); }

  .info { position: absolute; top: 12mm; left: 25mm; right: 4mm; }
  .name { font-size: 9pt; font-weight: bold; margin-bottom: 1mm; }
  .role { font-size: 5.5pt; color: rgba(255,255,255,.8); text-transform: uppercase; letter-spacing: 0.5pt; margin-bottom: 2mm; }
  .row { font-size: 6pt; color: rgba(255,255,255,.95); margin-bottom: 1mm; }
  .row b { color: #fff; }
  .label { color: rgba(255,255,255,.65); }

  .valid { position: absolute; bottom: 1.6mm; left: 4mm; right: 4mm; font-size: 5pt; color: rgba(255,255,255,.85); z-index: 2; }

  /* BACK */
  .back { background-color: #f3f7fc; color: #0f172a; }
  .back-inner { padding: 3.5mm 4mm; }
  .back-title { font-size: 6.5pt; font-weight: bold; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5pt; margin-bottom: 1.5mm; }
  .terms { font-size: 5pt; line-height: 1.5; color: #45516e; margin-bottom: 2.5mm; }
  .code-box {
    border: 0.6pt solid #2563eb;
    border-radius: 1mm;
    padding: 1.5mm;
    text-align: center;
    font-family: 'Courier New', monospace;
    font-size: 8pt;
    letter-spacing: 1.5pt;
    color: #16233f;
    margin-bottom: 2.5mm;
  }
  .contact-line { font-size: 5.2pt; color: #45516e; margin-bottom: 0.8mm; }
  .contact-line b { color: #16233f; }
  .sign-area { position: absolute; bottom: 3.5mm; left: 4mm; right: 4mm; }
  .sign-line { border-top: 0.5pt solid #8b96b3; padding-top: 1mm; font-size: 5pt; color: #45516e; text-align: right; }
</style>
</head>
<body>
  <div class="side front">
    <div class="front-accent"></div>
    <div class="header">
      <div class="brand">R-TECH COMPUTER</div>
      <div class="tagline">Student Identity Card</div>
    </div>

    <div class="photo-wrap">
      @if ($user->photoPath())
        <img src="file://{{ str_replace('\\', '/', $user->photoPath()) }}">
      @else
        <div class="photo-fallback">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
      @endif
    </div>

    <div class="info">
      <div class="name">{{ $user->name }}</div>
      <div class="role">Student</div>
      <div class="row"><span class="label">ID:</span> <b>{{ $user->student_code }}</b></div>
      <div class="row"><span class="label">Course:</span> <b>{{ $course->name ?? 'R-Tech Computer' }}</b></div>
      <div class="row"><span class="label">Blood Group:</span> <b>{{ $user->blood_group ?: '—' }}</b></div>
    </div>

    <div class="valid">Valid Thru {{ now()->addYear()->format('m/Y') }}</div>
  </div>

  <div class="side back">
    <div class="back-inner">
      <div class="back-title">Terms of Use</div>
      <div class="terms">
        This card is the property of R-Tech Computer and must be carried at all times on campus.
        Non-transferable. Report loss immediately to the administration office.
        Misuse of this card may result in disciplinary action.
      </div>

      <div class="code-box">{{ $user->student_code }}</div>

      <div class="contact-line"><b>If found, please return to:</b></div>
      <div class="contact-line">R-Tech Computer, Mogalkuan, Etwari Bazar, Sohsarai Road, Bihar Sharif, Nalanda-803117, Bihar</div>
      <div class="contact-line"><b>Phone:</b> +91 9117744925 &middot; rtechcomputer.in</div>
    </div>

    <div class="sign-area">
      <div class="sign-line">Authorized Signatory</div>
    </div>
  </div>
</body>
</html>
