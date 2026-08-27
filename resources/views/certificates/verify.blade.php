@extends('layouts.site')

@section('title', 'Verify Certificate')

@section('content')
  <section class="sec">
    <div class="container" style="max-width:1100px">
      <div class="text-center mb-4 rv">
        <div class="sec-lbl">Trust &amp; Authenticity</div>
        <h2 class="sec-h">Verify a <em>Certificate</em></h2>
        <p style="font-size:14px;color:var(--muted);max-width:480px;margin:10px auto 0">Enter the certificate code printed on any R-Tech Computer certificate to confirm it's genuine.</p>
      </div>

      <div class="cbox rv">
        <form method="GET" action="{{ route('certificates.verify') }}" class="d-flex gap-2 flex-wrap mb-2">
          <input class="inp" style="flex:1;min-width:200px" type="text" name="code" value="{{ $code }}" placeholder="e.g. RTC-26-AB12CD" required>
          <button class="btn-enr" type="submit"><i class="bi bi-search me-1"></i>Verify</button>
        </form>

        @if ($searched)
          @if ($certificate)
            @php
              $hasMarksheet = $certificate->hasMarksheetData();
              $result = $hasMarksheet ? $certificate->result() : null;
              $photoUrl = optional($certificate->user)->photoUrl();
              $displayName = $certificate->student_name ?: $certificate->user->name;
              $displayCourseName = $certificate->course_name ?: $certificate->course->name;
              $initial = strtoupper(substr($displayName ?: 'S', 0, 1));
            @endphp
            <div class="cert-verify-card">
              <div class="cert-verify-banner">
                <div class="cert-verify-banner-icon"><i class="bi bi-patch-check-fill"></i></div>
                <div>
                  <div class="cert-verify-banner-title">Certificate Verified</div>
                  <div class="cert-verify-banner-sub">This is a valid certificate officially issued by R-Tech Computer.</div>
                </div>
              </div>

              <div class="cert-verify-body">
                <div class="cert-verify-profile">
                  @if ($photoUrl)
                    <img src="{{ $photoUrl }}" alt="{{ $displayName }}" class="cert-verify-photo">
                  @else
                    <span class="cert-verify-photo-fallback">{{ $initial }}</span>
                  @endif
                  <div>
                    <div class="cert-verify-name">{{ $displayName }}</div>
                    <div class="cert-verify-course"><i class="bi bi-mortarboard-fill me-1"></i>{{ $displayCourseName }}</div>
                  </div>
                </div>

                <div class="cert-verify-grid">
                  <div>
                    <div class="cert-verify-field-label">Certificate Code</div>
                    <div class="cert-verify-field-value">{{ $certificate->cert_code }}</div>
                  </div>
                  <div>
                    <div class="cert-verify-field-label">Issued On</div>
                    <div class="cert-verify-field-value">{{ $certificate->issued_date->format('d M Y') }}</div>
                  </div>
                  @if ($certificate->course_duration_text)
                    <div>
                      <div class="cert-verify-field-label">Course Duration</div>
                      <div class="cert-verify-field-value">{{ $certificate->course_duration_text }}</div>
                    </div>
                  @endif
                  @if ($certificate->batch_name)
                    <div>
                      <div class="cert-verify-field-label">Batch</div>
                      <div class="cert-verify-field-value">{{ $certificate->batch_name }}</div>
                    </div>
                  @endif
                  @if ($certificate->roll_no)
                    <div>
                      <div class="cert-verify-field-label">Roll No.</div>
                      <div class="cert-verify-field-value">{{ $certificate->roll_no }}</div>
                    </div>
                  @endif
                  @if ($certificate->father_name)
                    <div>
                      <div class="cert-verify-field-label">Father's Name</div>
                      <div class="cert-verify-field-value">{{ $certificate->father_name }}</div>
                    </div>
                  @endif
                </div>

                @if ($certificate->include_certificate)
                  <div class="cert-verify-section-title"><i class="bi bi-file-earmark-image-fill"></i>Certificate</div>

                  <div class="cert-verify-doc-frame">
                    <span class="cert-verify-doc-badge"><i class="bi bi-patch-check-fill"></i>Authentic</span>
                    <iframe src="{{ route('certificates.verify.preview', ['certificate' => $certificate->cert_code]) }}" title="{{ $displayName }}'s Certificate" loading="lazy"></iframe>
                  </div>
                  <div class="cert-verify-doc-actions">
                    <a href="{{ route('certificates.verify.preview', ['certificate' => $certificate->cert_code]) }}" target="_blank" rel="noopener" class="btn-enr" style="text-decoration:none"><i class="bi bi-arrow-up-right-square me-1"></i>Open Full Certificate</a>
                  </div>
                @endif

                @if ($hasMarksheet)
                  <div class="cert-verify-section-title"><i class="bi bi-clipboard-data-fill"></i>Marksheet</div>

                  <div style="overflow-x:auto">
                    <table class="cert-verify-mtable">
                      <thead>
                        <tr>
                          <th>Subject</th>
                          <th class="num">Max Marks</th>
                          <th class="num">Marks Obtained</th>
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
                      </tbody>
                    </table>
                  </div>

                  <div class="cert-verify-chart-wrap">
                    <div class="cert-verify-chart">
                      @include('partials.charts.donut', [
                        'segments' => [
                          ['label' => 'Marks Obtained', 'value' => $certificate->totalMarksObtained(), 'color' => $result === 'PASS' ? '40,180,90' : '220,38,38'],
                          ['label' => 'Marks Remaining', 'value' => max($certificate->totalMaxMarks() - $certificate->totalMarksObtained(), 0), 'color' => '107,127,163'],
                        ],
                        'centerValue' => $certificate->percentage() . '%',
                        'centerLabel' => 'Score',
                        'size' => 132,
                      ])
                    </div>

                    <div class="cert-verify-summary">
                      <div class="cert-verify-stat">
                        <div class="cert-verify-stat-label">Total Marks</div>
                        <div class="cert-verify-stat-value">{{ $certificate->totalMarksObtained() }}/{{ $certificate->totalMaxMarks() }}</div>
                      </div>
                      <div class="cert-verify-stat">
                        <div class="cert-verify-stat-label">Percentage</div>
                        <div class="cert-verify-stat-value">{{ $certificate->percentage() }}%</div>
                      </div>
                      <div class="cert-verify-stat">
                        <div class="cert-verify-stat-label">Grade</div>
                        <div class="cert-verify-stat-value">{{ $certificate->grade() }}</div>
                      </div>
                      <div class="cert-verify-stat {{ $result === 'PASS' ? 'pass' : 'fail' }}">
                        <div class="cert-verify-stat-label">Result</div>
                        <div class="cert-verify-stat-value">{{ $result }}</div>
                      </div>
                    </div>
                  </div>

                  <div class="cert-verify-section-title"><i class="bi bi-pie-chart-fill"></i>Subject-wise Performance</div>

                  <div class="cert-verify-subjects-grid">
                    @foreach ($certificate->subjects as $subject)
                      @php
                        $subMax = (int) $subject->max_marks;
                        $subObtained = (int) ($subject->marks_obtained ?? 0);
                        $subPct = $subMax > 0 ? round($subObtained / $subMax * 100) : 0;
                      @endphp
                      <div class="cert-verify-subject-card">
                        @include('partials.charts.donut', [
                          'segments' => [
                            ['label' => 'Obtained', 'value' => $subObtained, 'color' => '22,51,110'],
                            ['label' => 'Remaining', 'value' => max($subMax - $subObtained, 0), 'color' => '201,162,75'],
                          ],
                          'centerValue' => $subPct . '%',
                          'size' => 76,
                          'hideLegend' => true,
                        ])
                        <div class="cert-verify-subject-name" title="{{ $subject->subject }}">{{ $subject->subject }}</div>
                        <div class="cert-verify-subject-score">{{ $subObtained }}/{{ $subMax }} marks</div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>

              <div class="cert-verify-footer">
                <i class="bi bi-shield-check"></i>
                Verified against R-Tech Computer's official records on {{ now()->format('d M Y, h:i A') }}.
              </div>
            </div>
          @else
            <div class="cert-verify-result fail">
              <div class="cert-verify-icon"><i class="bi bi-x-circle-fill"></i></div>
              <div>
                <div class="cert-verify-title">Certificate Not Found</div>
                <p style="font-size:13px;color:var(--muted);margin:4px 0 0">We couldn't find an issued certificate with that code. Double-check the code and try again.</p>
              </div>
            </div>
          @endif
        @endif
      </div>
    </div>
  </section>
@endsection
