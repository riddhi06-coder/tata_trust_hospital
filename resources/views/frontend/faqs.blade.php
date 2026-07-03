
<!DOCTYPE html>
<html lang="en">
  <head>

    @include('components.frontend.head')

  </head>
  <body>

    @include('components.frontend.header')


    <!-- main-area -->
    <main class="fix">


        <!-- Breadcrumb -->
        <section class="breadcrumb gallery-breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                @if($faq_settings && !empty($faq_settings->banner_image))
                    <img src="{{ asset('home/faq/'.$faq_settings->banner_image) }}"
                        alt="{{ $faq_settings->banner_heading ?? 'FAQs' }}">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/about-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                @php
                    if (!empty($faq_settings->banner_heading)) {
                        // Admin-authored — escape and insert <br> after the first comma
                        $headingHtml = preg_replace('/,/', ',<br>', e($faq_settings->banner_heading), 1);
                    } else {
                        // Fallback keeps the original static line-break
                        $headingHtml = 'When you\'re with experts you trust,<br class="bread-br-mob"> every question is a good question.';
                    }
                @endphp
                <h1 class="breadcrumb-title">{!! $headingHtml !!}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>FAQs</li>
                </ul>
            </div>
        </section>


        <section class="faqs-custom-main-sec">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section__title section_title-two text-center">
                            <h2 class="title" data-aos="fade-up" data-aos-delay="100">
                                {{ $faq_settings->section_heading ?? 'Frequently Asked Questions' }}
                            </h2>
                        </div>
                    </div>
                </div>

                <!-- FAQ accordion -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="faq-list-wrapper" data-aos="fade-up" data-aos-delay="100">

                            @forelse($faqs as $index => $faq)
                                <div class="faq-item{{ $index === 0 ? ' open' : '' }}">
                                    <button class="faq-trigger" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}">
                                        <span class="faq-num">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        <span class="faq-question">{{ $faq->question }}</span>
                                        <span class="faq-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="6 9 12 15 18 9" />
                                            </svg>
                                        </span>
                                    </button>
                                    <div class="faq-body">
                                        <div class="faq-answer">
                                            {!! $faq->answer !!}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted py-4">No FAQs available yet.</p>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-page-contact-us-footer-top">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="why__we-are-content">
                            <div class="section__title section_title_none mb-0">
                                <h2 class="title" data-aos="fade-right">Appointment &amp; Emergency help is available 24<span class="title-span">x</span>7</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="home-appointment-emergency-footer-btn-sec">
                            <a href="{{ url('contact-us') }}" class="btn">Contact Us<img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                    alt="" class="injectable"></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


    </main>
    <!-- main-area-end -->

    @include('components.frontend.footer')

    @include('components.frontend.main-js')

  </body>
</html>
