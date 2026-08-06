  @if ($featuredStudents->isNotEmpty())
    <!-- STUDENT PROFILES -->
    <section class="sec sec-alt">
      <div class="container">
        <div class="text-center mb-5 rv">
          <div class="sec-lbl">Our Students</div>
          <h2 class="sec-h">Faces Behind the <em>Success Stories</em></h2>
          <p style="font-size:14px;color:var(--muted);max-width:560px;margin:10px auto 0">A few of the students we're proud to have trained.</p>
        </div>
        <div class="row g-4">
          @php $grads = ['var(--grad)', 'linear-gradient(135deg,#9c27b0,#e91e63)', 'linear-gradient(135deg,#009688,#4caf50)', 'linear-gradient(135deg,#f59e0b,#ef4444)']; @endphp
          @foreach ($featuredStudents as $student)
            <div class="col-6 col-md-3 rv">
              <div class="tc text-center">
                @if ($student->photoUrl())
                  <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}" class="mx-auto mb-3" style="width:72px;height:72px;border-radius:50%;object-fit:cover;display:block">
                @else
                  <div class="ra mx-auto mb-3" style="width:72px;height:72px;font-size:24px;background:{{ $grads[$loop->index % 4] }}">{{ strtoupper(substr($student->name, 0, 1)) }}</div>
                @endif
                <div class="rname" style="font-size:15px">{{ $student->name }}</div>
                @if ($student->featuredCourse())
                  <div class="rrole">{{ $student->featuredCourse()->name }}</div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
