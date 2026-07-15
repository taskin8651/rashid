  @if ($faqs->count())
    <!-- FAQ -->
    <section class="sec" id="faq">
      <div class="container" style="max-width:760px">
        <div class="text-center mb-5 rv">
          <div class="sec-lbl">Got Questions?</div>
          <h2 class="sec-h">Frequently Asked <em>Questions</em></h2>
        </div>
        <div class="accordion rv" id="faqAccordion">
          @foreach ($faqs as $faq)
            <div class="faq-item">
              <button class="faq-q" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                <span>{{ $faq->question }}</span>
                <i class="bi bi-plus-lg"></i>
              </button>
              <div class="collapse" id="faq{{ $faq->id }}" data-bs-parent="#faqAccordion">
                <div class="faq-a">{{ $faq->answer }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>
  @endif
