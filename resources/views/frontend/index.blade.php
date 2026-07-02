
<!DOCTYPE html>
<html lang="en">
  <head>

    @include('components.frontend.head')

  </head>
  <body>
   
    @include('components.frontend.header')

    
    <!-- main-area -->
    <main class="fix">


        <section class="pet-hero-section">
            <div class="swiper petHeroSwiper">
                <div class="swiper-wrapper">

                    @foreach($banner as $item)

                        <div class="swiper-slide">
                            <div class="pet-hero-slide">

                                <!-- MEDIA -->
                                <div class="pet-hero-image">

                                    {{-- IMAGE --}}
                                    @if($item->media_type == 'image')

                                        <img src="{{ asset('home/bannerimagevideo/'.$item->banner_media) }}"
                                            alt="{{ $item->banner_heading ?? 'Pet Care Image' }}"
                                            class="d-none d-lg-block">
                                        <img src="{{ asset('home/bannerimagevideo/'.$item->banner_media) }}"
                                            alt="{{ $item->banner_heading ?? 'Pet Care Image' }}"
                                            class="d-block d-lg-none">

                                    {{-- VIDEO --}}
                                    @elseif($item->media_type == 'video')

                                        <video autoplay muted loop playsinline width="100%">
                                            <source src="{{ asset('home/bannerimagevideo/'.$item->banner_media) }}"
                                                type="video/mp4">
                                        </video>

                                    @endif

                                </div>

                                <!-- CONTENT -->
                                <div class="pet-hero-content-wrapper">
                                    <div class="pet-hero-content">

                                        @if(!empty($item->banner_heading))
                                            <h1>{!! $item->banner_heading !!}</h1>
                                        @endif

                                        @if(!empty($item->banner_title))
                                            <p>{{ $item->banner_title }}</p>
                                        @endif

                                        <div class="pet-btn-group">
                                            <a href="#"
                                                class="btn"
                                                data-bs-toggle="modal"
                                                data-bs-target="#appointmentModal">
                                                Book An Appointment
                                                <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                                    alt="Read More" class="injectable">
                                            </a>
                                        </div>

                                    </div>
                                </div>

                                <!-- Paw Overlay -->
                                <div class="pet-paw-overlay"></div>

                            </div>
                        </div>

                    @endforeach

                </div>

                <!-- Navigation -->
                <div class="pet-swiper-next"></div>
                <div class="pet-swiper-prev"></div>

            </div>
        </section>


        <!-- why-we-are-area -->
        <section class="why__we-are-area">
            <div class="container">
                <div class="row align-items-center justify-content-center">

                    <!-- LEFT IMAGE / VIDEO -->
                    <div class="col-lg-6 col-md-12 col-sm-10">

                        <div class="world-class-care-unwaving-img-sec-wrapper">

                            <div class="world-class-care-unwaving-img-sec">

                                @if($short_intro && $short_intro->media_type == 'image')

                                    <img src="{{ asset('home/shortintroduction/'.$short_intro->banner_media) }}"
                                        alt="{{ $short_intro->banner_heading ?? 'About Us Image' }}">

                                @elseif($short_intro && $short_intro->media_type == 'video')

                                    <video autoplay muted loop playsinline width="100%">
                                        <source src="{{ asset('home/shortintroduction/'.$short_intro->banner_media) }}"
                                            type="video/mp4">
                                    </video>

                                @endif

                            </div>

                            <!-- Overlay Badge -->
                            <div class="img-overlay-badge">
                                <span>
                                    {!! $short_intro->banner_title ?? 'Care.<br>Cure..<br>Comfort...' !!}
                                </span>
                            </div>

                        </div>

                    </div>

                    <!-- RIGHT CONTENT -->
                    <div class="col-lg-6">
                        <div class="why__we-are-content">

                            <div class="section__title">

                                @if($short_intro && !empty($short_intro->banner_heading))
                                    <h2 class="title" data-aos="fade-left">
                                        {!! $short_intro->banner_heading !!}<br>
                                        {!! $short_intro->banner_title !!}
                                    </h2>
                                @endif

                            </div>

                            @if($short_intro && !empty($short_intro->introduction))
                                <div class="cke-editor" data-aos="fade-left" data-aos-delay="150">
                                    {!! $short_intro->introduction !!}
                                </div>
                            @endif

                            <div class="home-about-world-class-care-sec" data-aos="fade-left" data-aos-delay="250">
                                <a href="{{ url('about-us') }}" class="btn">
                                    Read More
                                    <span class="visually-hidden">About Us</span>
                                    <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                        alt="Read More"
                                        class="injectable">
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="why-we-area-big-text">
                <h6 class="big-text">{{ $specialities->our_motto ?? 'Care. Cure. Comfort.' }}</h6>
            </div>

        </section>
        <!-- why-we-are-area-end -->


        <!-- services-area -->
        <section class="services__area our-specialities-section-one">
            <div class="container">

                <!-- SECTION TITLE -->
                <div class="row align-items-center">
                    <div class="col-xl-12 col-lg-12">

                        <div class="section__title">
                            @if($specialities && !empty($specialities->title))
                                <h2 class="title" data-aos="fade-right">
                                    {!! $specialities->title !!}
                                </h2>
                            @endif
                        </div>

                        @if($specialities && !empty($specialities->description))
                            <div data-aos="fade-right" data-aos-delay="150">
                                {!! $specialities->description !!}
                            </div>
                        @endif

                    </div>
                </div>

                <!-- SPECIALITIES -->
                <div class="our-spec-listing-home-custom-new-sec">
                    <div class="row justify-content-center">

                        @if($specialities && !empty($specialities->specialities))

                            @foreach($specialities->specialities as $index => $item)

                                @php
                                    $aosDelay = ($index % 3) * 150;
                                @endphp

                                <div class="col-lg-4 col-md-4 col-sm-6 service-item"
                                    data-aos="fade-up"
                                    @if($aosDelay > 0) data-aos-delay="{{ $aosDelay }}" @endif>

                                    <div class="serviceBox">

                                        <div class="service-icon">
                                            <span>
                                                <img src="{{ asset('home/specialities/'.$item['icon']) }}"
                                                    width="65" height="65"
                                                    alt="{{ $item['name'] }}">
                                            </span>
                                        </div>

                                        <h3 class="title">{{ $item['name'] }}</h3>

                                    </div>

                                </div>

                            @endforeach

                        @endif

                        </div>

                    </div>
                </div>

            </div>
        </section>
        <!-- services-area-end -->



        <!-- product-area -->
        <section class="product__area our-facilities-one-bg-custom-sp">

            <div class="container">

                <div class="row align-items-center">

                    <!-- LEFT CONTENT -->
                    <div class="col-lg-4 col-md-12">

                        <div class="why__we-are-content">

                            <div class="section__title">

                                @if($facilities && !empty($facilities->title))
                                    <h2 class="title" data-aos="fade-right">
                                        {!! $facilities->title !!}
                                    </h2>
                                @endif

                            </div>

                            @if($facilities && !empty($facilities->description))
                                <div class="cke-editor mar-new-spcus" data-aos="fade-right" data-aos-delay="150">
                                    {!! $facilities->description !!}
                                </div>
                            @endif

                            <div class="home-about-world-class-care-sec" data-aos="fade-right" data-aos-delay="200">

                                <a href="{{ url('our-facilities') }}" class="btn">
                                    Read More
                                    <span class="visually-hidden">Our Facilities</span>
                                    <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                        alt=""
                                        class="injectable">
                                </a>

                            </div>

                        </div>

                    </div>

                    <!-- RIGHT SLIDER -->
                    <div class="col-lg-8 col-md-12">

                        <div class="swiper product-active">

                            <div class="swiper-wrapper">

                                @if($facilities && !empty($facilities->facilities))

                                    @foreach($facilities->facilities as $item)

                                        <div class="swiper-slide">

                                            <div class="product__item">

                                                <div class="product__thumb">

                                                    <a href="{{ url('our-facilities') }}">
                                                        <img src="{{ asset('home/facilities/'.$item['icon']) }}"
                                                            alt="{{ $item['name'] }}">
                                                    </a>

                                                    <div class="product__add-cart">
                                                        <a href="{{ url('our-facilities') }}" class="btn">
                                                            {{ $item['name'] }}
                                                        </a>
                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    @endforeach

                                @endif

                            </div>

                        </div>

                        <!-- NAVIGATION -->
                        <div class="product-nav">
                            <div class="swiper-button-next product-button-next"></div>
                            <div class="swiper-button-prev product-button-prev"></div>
                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- product-area-end -->


        <!-- team-area -->
        <section class="team__area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up">{!! $our_team->title !!}</h2>
                        </div>

                        @if(!empty($our_team->description))
                            <div class="text-center" data-aos="fade-up" data-aos-delay="150">
                                {!! $our_team->description !!}
                            </div>
                        @endif
                    </div>
                </div>


                <div class="row justify-content-center">

                    @if($team_members->count() > 0)

                        @foreach($team_members as $index => $member)

                            @php
                                $aosDelay = ($index % 4) * 150;
                            @endphp

                            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8"
                                data-aos="fade-up"
                                @if($aosDelay > 0) data-aos-delay="{{ $aosDelay }}" @endif>

                                <div class="team__item">

                                    <div class="team__item-img">

                                        <div class="mask-img-wrap">
                                            <img src="{{ asset('our-team/'.$member->image) }}"
                                                alt="{{ $member->name }}">
                                        </div>

                                        <div class="team__item-img-shape"></div>

                                    </div>

                                    <div class="team__item-content">

                                        <h4 class="title">
                                            {{ $member->name }}
                                        </h4>

                                        @if(!empty($member->education))
                                            <p>{{ $member->education }}</p>
                                        @endif

                                        @if(!empty($member->designation))

                                            @foreach(explode(',', $member->designation) as $designation)
                                                <p>{{ trim($designation) }}</p>
                                            @endforeach

                                        @endif

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    @endif

                </div>

                <div class="team__bottom-content">

                    <a href="{{ url('our-team') }}" class="btn">
                        View More
                        <span class="visually-hidden">Our team</span>
                        <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                            alt=""
                            class="injectable">
                    </a>

                </div>

            </div>
        </section>
        <!-- team-area-end -->


        <!-- testimonial-area -->
        <section class="testimonial__area sah-testimonials-custom-sec">
            <div class="container">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-12">
                        <div class="section__title section_title-two text-center">
                            <h2 class="title" data-aos="fade-up">
                                {!! $testimonial_details->title ?? 'Testimonials' !!}
                            </h2>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-8 order-0 order-lg-2">
                        <div class="testi-img-custom-sp-sec">
                            @if($testimonial_details && !empty($testimonial_details->image))
                                <img src="{{ asset('home/testimonials/'.$testimonial_details->image) }}"
                                    alt="{{ $testimonial_details->title ?? 'Testimonial' }}"
                                    data-aos="fade-left"
                                    data-aos-delay="150">
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="testimonial__item-wrap">

                            <div class="swiper testimonial-active">

                                <div class="swiper-wrapper">

                                    @if($testimonials->count() > 0)

                                        @foreach($testimonials as $testimonial)

                                            <div class="swiper-slide">

                                                <div class="testimonial__item">

                                                    <div class="testimonial__icon">
                                                        <img src="{{ asset('frontend/assets/img/icon/quote.svg') }}"
                                                            alt=""
                                                            class="injectable">
                                                    </div>

                                                    <div class="testimonial__content">

                                                        {!! $testimonial->testimony !!}

                                                        <h2 class="title">
                                                            {{ $testimonial->name }}
                                                        </h2>

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    @endif

                                </div>

                            </div>

                            <!-- Navigation buttons -->
                            <div class="testimonial-nav">
                                <div class="swiper-button-prev custom-prev"></div>
                                <div class="swiper-button-next custom-next"></div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>
        <!-- testimonial-area-end -->

        

        <section class="board-creative-section position-relative overflow-hidden">
            <div class="container">
                <div class="row align-items-center">

                    <!-- IMAGE SIDE -->
                    <div class="col-lg-6 col-md-6 text-center position-relative">
                        <div class="image-stack">

                            @if($our_board && !empty($our_board->image))
                                <img src="{{ asset('home/board/' . $our_board->image) }}"
                                    class="img-fluid main-img"
                                    alt="{{ $our_board->image_caption ?? $our_board->title }}"
                                    data-aos="fade-right"
                                    data-aos-delay="150">
                            @endif

                            <div class="image-stack-content-sec">
                                <h4>
                                    {{ $our_board->image_caption ?? 'Late Mr Ratan N. Tata' }}
                                </h4>
                                <p>
                                    {{ $our_board->image_subtitle ?? 'Founder and Chairman Emeritus' }}
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- CONTENT SIDE -->
                    <div class="col-lg-6 col-md-6">
                        <div class="glass-card p-4 p-md-5">

                            <h2 class="main-title" data-aos="fade-left">
                                {{ $our_board->title ?? 'Our Board' }}
                            </h2>

                            @if($our_board && !empty($our_board->description))
                                <p class="desc" data-aos="fade-left" data-aos-delay="150">
                                    {!! strip_tags($our_board->description, '<br><strong><b><em><i>') !!}
                                </p>
                            @else
                                <p class="desc" data-aos="fade-left" data-aos-delay="150">
                                    The Advanced Veterinary Care Foundation (AVCF) oversees the management
                                    of the Small Animal Hospital Mumbai, with support from Tata Trusts.
                                </p>
                            @endif

                            <div class="home-about-world-class-care-sec"
                                data-aos="fade-left"
                                data-aos-delay="200">

                                <a href="{{ url('our-team#our-team-our-board-sec') }}" class="btn">
                                    Read More
                                    <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                        alt=""
                                        class="injectable">
                                </a>

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>
        

        <section class="pet-instagram-section">

            <div class="container">

                <div class="row align-items-center justify-content-center">

                    <!-- CONTENT -->
                    <div class="col-lg-4">

                        <div class="why__we-are-content">

                            <div class="section__title">

                                <h2 class="title">
                                    {{ $follow_us->title ?? 'Follow Us' }}
                                </h2>

                            </div>

                            @if($follow_us && !empty($follow_us->description))

                                <div class="cke-editor">

                                    {!! $follow_us->description !!}

                                </div>

                            @endif

                            <div class="home-about-world-class-care-sec">

                                <div class="home-about-world-class-care-sec">

                                    @if($follow_us && !empty($follow_us->social_media_link))

                                        <a href="{{ $follow_us->social_media_link }}"
                                            target="_blank"
                                            class="btn">

                                            Click To Join

                                            <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                                alt=""
                                                class="injectable">

                                        </a>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- IMAGE -->
                    <div class="col-lg-5">

                        <div class="insta-stack-wrapper">

                            @if($follow_us && !empty($follow_us->image))

                                <img src="{{ asset('home/follow_us/'.$follow_us->image) }}"
                                    class="follow-us-oa-custom-img"
                                    alt="{{ $follow_us->title }}">

                            @endif

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
                                <h2 class="title">Appointment & Emergency help is available</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="home-appointment-emergency-footer-btn-sec">
                            <a href="tel:02265383538" class="btn">Contact Us<img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
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