
<!DOCTYPE html>
<html lang="en">
  <head>

    @include('components.frontend.head')

  </head>
  <body>

    @include('components.frontend.header')


    <!-- main-area -->
    <main class="fix">


        <section class="breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                @if($about && !empty($about->banner_image))
                    <img src="{{ asset('about_us/'.$about->banner_image) }}" alt="{{ $about->banner_heading ?? 'About Us' }}">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/about-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                @php
                    $aboutHeading = $about->banner_heading ?? 'Built on compassion. Learn our story and mission';
                    // Break the heading onto a second line after the first period.
                    $aboutHeadingHtml = preg_replace('/\.\s*/', '.<br>', e($aboutHeading), 1);
                @endphp
                <h1 class="breadcrumb-title">{!! $aboutHeadingHtml !!}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>About</li>
                </ul>
            </div>
        </section>

        <section class="about-us-page-new-custom-one-sec">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="a-us-custom-img-sec">
                            @if($about && !empty($about->about_image))
                                <img src="{{ asset('about_us/'.$about->about_image) }}" alt="img" class="auo-img-one">
                            @else
                                <img src="{{ asset('frontend/assets/img/images/about-us-img-1.jpeg') }}" alt="img" class="auo-img-one">
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-us-content-four">
                            <div class="section__title mb-15">
                                <h2 class="title" data-aos="fade-left">{{ $about->about_heading ?? 'About Us' }}</h2>
                            </div>
                            @if($about && !empty($about->about_description))
                                <div data-aos="fade-left" data-aos-delay="150">{!! $about->about_description !!}</div>
                            @endif
                        </div>
                        <div class="about-us-vision-commitment-veterinary-sec">
                            <div class="smooth-stream">
                                @php $infoItems = ($about && !empty($about->about_info_items)) ? $about->about_info_items : null; @endphp
                                @if($infoItems)
                                    @foreach($infoItems as $i => $it)
                                        <div class="stream-row" data-aos="fade-right" @if($i > 0) data-aos-delay="{{ $i * 150 }}" @endif>
                                            <div class="row-header">
                                                <span class="row-num">
                                                    @if(!empty($it['image']))
                                                        <img src="{{ asset('about_us/'.$it['image']) }}" alt="">
                                                    @endif
                                                </span>
                                                <h3 class="row-title mb-0">{{ $it['heading'] ?? '' }}</h3>
                                            </div>
                                            <div class="row-body-wrapper">
                                                <div class="row-body mb-0">{!! $it['description'] ?? '' !!}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-us-our-values-sec">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 about-us-our-values-col-first-sec">
                        <div class="why__we-are-content">
                            <div class="section__title">
                                <h2 class="title" data-aos="fade-right">{{ $about->values_heading ?? 'Our Values' }}</h2>
                            </div>
                            @if($about && !empty($about->values_description))
                                <div data-aos="fade-right" data-aos-delay="150">{!! $about->values_description !!}</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-us-our-values-img-col-sec">
                            @if($about && !empty($about->values_image))
                                <img src="{{ asset('about_us/'.$about->values_image) }}" alt="" data-aos="fade-left" data-aos-delay="150">
                            @else
                                <img src="{{ asset('frontend/assets/img/images/our-value.webp') }}" alt="" data-aos="fade-left" data-aos-delay="150">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="reflecting-our-commitment-custom-sec">
            <div class="container">

                <div class="row g-4 justify-content-center">

                    <div class="col-lg-12">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up" data-aos-delay="150">
                                {!! $about && !empty($about->commitment_heading)
                                    ? e($about->commitment_heading)
                                    : '12,000+ patients cared for - Reflecting our commitment<br>to animal health and well-being.' !!}
                            </h2>
                        </div>
                    </div>

                    @php $commitItems = ($about && !empty($about->commitment_items)) ? $about->commitment_items : null; @endphp
                    @if($commitItems)
                        @foreach($commitItems as $i => $it)
                            <div class="col-lg-2 col-md-4">
                                <div class="reflecting-commitment-card" data-aos="fade-up" data-aos-delay="{{ 150 + ($i * 50) }}">
                                    <div class="reflecting-icon">
                                        @if(!empty($it['image']))
                                            <img src="{{ asset('about_us/'.$it['image']) }}" alt="">
                                        @endif
                                    </div>
                                    <h3>{{ $it['count'] ?? '' }}</h3>
                                    <p>{{ $it['title'] ?? '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>

            </div>
        </section>



        <section class="about-us-book-an-appointment-footer-one-sec">
            <div class="container-fluid px-0">

                <div class="about-footer-wrapper">

                    <!-- Building Image -->
                    <div class="about-footer-building-img">
                        @if($about && !empty($about->contact_image))
                            <img src="{{ asset('about_us/'.$about->contact_image) }}" alt="Hospital Building">
                        @else
                            <img src="{{ asset('frontend/assets/img/banner/cont-bg.webp') }}" alt="Hospital Building">
                        @endif
                    </div>

                    <!-- Floating CTA Box -->
                    <div class="about-footer-cta-box">
                        @if($about && !empty($about->contact_description))
                            {!! $about->contact_description !!}
                        @endif
                        <a href="{{ route('frontend.user_login') }}" class="btn">Book An Appointment<img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt=""
                                class="injectable"></a>
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
