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
          @foreach ($featuredStudents as $student)
            <div class="col-6 col-md-3 rv">
              @include('partials.student-card', ['student' => $student, 'index' => $loop->index])
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
