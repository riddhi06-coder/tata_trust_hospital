
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

                                <!-- LEFT CONTENT -->
                                <div class="container">
                                    <div class="row align-items-center">

                                        <div class="col-lg-5">
                                            <div class="pet-hero-content">

                                                @if(!empty($item->banner_heading))
                                                    <h1>{{ $item->banner_heading }}</h1>
                                                @endif

                                                @if(!empty($item->banner_title))
                                                    <p>{{ $item->banner_title }}</p>
                                                @endif

                                                <div class="pet-btn-group">
                                                    <a href="#"
                                                        class="pet-btn-outline"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#appointmentModal">
                                                        Book An Appointment
                                                    </a>
                                                </div>

                                            </div>
                                        </div>

                                        <!-- RIGHT MEDIA -->
                                        <div class="col-lg-7">
                                            <div class="pet-hero-image">

                                                {{-- IMAGE --}}
                                                @if($item->media_type == 'image')

                                                    <img src="{{ asset('home/bannerimagevideo/'.$item->banner_media) }}"
                                                        alt="Banner Image">

                                                {{-- VIDEO --}}
                                                @elseif($item->media_type == 'video')

                                                    <video autoplay muted loop playsinline width="100%">
                                                        <source src="{{ asset('home/bannerimagevideo/'.$item->banner_media) }}"
                                                            type="video/mp4">
                                                    </video>

                                                @endif

                                            </div>
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
            <div class="row align-items-center justify-content-center">

                <!-- LEFT IMAGE / VIDEO -->
                <div class="col-lg-6 col-md-8 col-sm-10">

                    <div class="world-class-care-unwaving-img-sec-wrapper">

                        <div class="world-class-care-unwaving-img-sec">

                            @if($short_intro && $short_intro->media_type == 'image')

                                <img src="{{ asset('home/shortintroduction/'.$short_intro->banner_media) }}"
                                    alt="About Us Image">

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
                                {{ $short_intro->banner_title ?? 'Care. Cure. Comfort.' }}
                            </span>
                        </div>

                    </div>

                </div>

                <!-- RIGHT CONTENT -->
                <div class="col-lg-6">
                    <div class="why__we-are-content">

                        <div class="section__title">

                            @if($short_intro && !empty($short_intro->banner_heading))
                                <h2 class="title">
                                    {{ $short_intro->banner_heading }}<br>
                                     {{ $short_intro->banner_title }}
                                </h2>
                            @endif

                        </div>

                        @if($short_intro && !empty($short_intro->introduction))
                            <div class="cke-editor">
                                {!! $short_intro->introduction !!}
                            </div>
                        @endif

                        <div class="home-about-world-class-care-sec">
                            <a href="{{ url('about-us') }}" class="btn">
                                Read More
                                <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                    alt="Read More"
                                    class="injectable">
                            </a>
                        </div>

                    </div>
                </div>

            </div>



            <div class="why-we-area-big-text">
                <h6 class="big-text">Care. Cure. Comfort.</h6>
            </div>


        </section>
        <!-- why-we-are-area-end -->


        <!-- services-area -->
        <section class="services__area our-specialities-section-one">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xl-12 col-lg-12">
                        <div class="section__title section_title-two text-center">
                            <!-- <span class="sub-title">Delivering world class home care
                                <strong class="shake">
                                    <img src="assets/img/icon/pet_icon02.svg" alt="" class="injectable">
                                </strong>
                            </span> -->
                            <h2 class="title">Our Specialities</h2>
                        </div>
                        <p class="text-center">From routine wellness visits to complex tertiary surgeries, SAHM brings
                            specialist
                            expertise, advanced diagnostics, and state-of-the-art infrastructure together under one
                            roof. Our experienced veterinary team works collaboratively, with personalised treatment
                            plans tailored to every patient’s unique needs because no two animals are the same.</p>
                        <p class="text-center">We believe in transparency, compassionate communication, and care that
                            goes beyond the
                            clinical. And when every minute counts, our Emergency and Critical Care department is
                            here 24X7X365, always ready, for whatever your pet needs.</p>
                    </div>
                </div>
                
                <div class="row justify-content-center">
                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/emergency-call-icon-one.png" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>24 Hour Emergency</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-two.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Consultation</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-three.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Surgery</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-four.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Inpatient Care</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-five.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Diagnostic Imaging</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-six.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Internal Medicine</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-seven.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Cardiology</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-ran.png" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Ortho Surgery</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-eight.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Dental Care & Surgery</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-nine.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Medical Oncology</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-ten.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Advanced Infection Control</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-eleven.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Anaesthesia</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-twleve.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Physio & Rehab</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-fourteen.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Ophthalmology</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-fifteen.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Pathology</h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 service-item">
                        <div class="our-specialities-services-card">
                            <div class="our-spec-ser-img">
                                <img src="assets/img/icon/our-specialities-img-sixteen.webp" alt="">
                            </div>
                            <div class="our-spec-ser-content-sec">
                                <h4>Blood Transfusion</h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                        <div class="home-our-specialities-btn">
                            <a href="javascript:void(0)" id="loadMoreBtn" class="btn">Load More<img
                                    src="assets/img/icon/right_arrow.svg" alt="" class="injectable"></a>
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
                    <div class="col-md-4">
                        <div class="why__we-are-content">
                            <div class="section__title">
                                <!-- <span class="sub-title">Why We are The Best
                                    <strong class="shake">
                                        <img src="assets/img/icon/pet_icon02.svg" alt="" class="injectable">
                                    </strong>
                                </span> -->
                                <h2 class="title">Our Facilities</h2>
                            </div>
                            <p>Spanning over 1,00,000 sq ft, our G+4 storey facility has been meticulously designed in
                                consultation with international veterinary hospital architects following global best
                                practice. This ensures that we adhere to the best practices in hospital design and
                                planning, guaranteeing the highest level of veterinary care for all small and companion
                                animals.</p>
                            <div class="home-about-world-class-care-sec">
                                <a href="our-facilities.html" class="btn">Read More<img src="assets/img/icon/right_arrow.svg" alt=""
                                        class="injectable"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="swiper product-active">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="product__item">
                                        <div class="product__thumb">
                                            <a href="#"><img src="assets/img/our-facilities/our-facilities-img-one.webp"
                                                    alt="img"></a>
                                            <div class="product__add-cart">
                                                <a href="our-facilities.html#purpose-driven-infrastructure" class="btn">Purpose-Driven Infrastructure</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="product__item">
                                        <div class="product__thumb">
                                            <a href="#"><img src="assets/img/our-facilities/our-facilities-img-two.webp"
                                                    alt="img"></a>
                                            <div class="product__add-cart">
                                                <a href="our-facilities.html#advanced-diagnostic-imaging-services" class="btn">Advanced Diagnostic Imaging Services</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="product__item">
                                        <div class="product__thumb">
                                            <a href="#"><img
                                                    src="assets/img/our-facilities/our-facilities-img-three.webp"
                                                    alt="img"></a>
                                            <div class="product__add-cart">
                                                <a href="our-facilities.html#separate-waiting-areas" class="btn">Separate Waiting Area</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="product__item">
                                        <div class="product__thumb">
                                            <a href="#"><img
                                                    src="assets/img/our-facilities/our-facilities-img-four.webp"
                                                    alt="img"></a>
                                            <div class="product__add-cart">
                                                <a href="our-facilities.html#world-class-emergency-room" class="btn">World Class Emergency Room</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="product__item">
                                        <div class="product__thumb">
                                            <a href="#"><img
                                                    src="assets/img/our-facilities/our-facilities-img-five.webp"
                                                    alt="img"></a>
                                            <div class="product__add-cart">
                                                <a href="our-facilities.html#advanced-surgical-suites" class="btn">Advanced Surgical Suites</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="product__item">
                                        <div class="product__thumb">
                                            <a href="#"><img src="assets/img/our-facilities/our-facilities-img-six.webp"
                                                    alt="img"></a>
                                            <div class="product__add-cart">
                                                <a href="our-facilities.html#specialized-critical-care-units" class="btn">Specialized Critical Care Unit</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="swiper-slide">
                                    <div class="product__item">
                                        <div class="product__thumb">
                                            <a href="#"><img
                                                    src="assets/img/our-facilities/our-facilities-img-three.webp"
                                                    alt="img"></a>
                                            <div class="product__add-cart">
                                                <a href="our-facilities.html#comprehensive-in-house-pathology-lab" class="btn">Comprehensive In-house Pathology Lab</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="product-nav">
                            <div class="swiper-button-next product-button-next"></div>
                            <div class="swiper-button-prev product-button-prev"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bottom Left Image -->
            <img src="assets/img/bg/our-fac-bg-img-one.png" class="facility-bottom-img" alt="">
        </section>
        <!-- product-area-end -->


        <!-- team-area -->
        <section class="team__area">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section__title section_title-two text-center mb-40">
                            <!-- <span class="sub-title">WE CHANGE YOUR LIFE & WORLD
                                <strong class="shake">
                                    <img src="assets/img/icon/pet_icon02.svg" alt="" class="injectable">
                                </strong>
                            </span> -->
                            <h2 class="title">OUR TEAM</h2>
                        </div>
                        <p class="text-center">At SAHM, exceptional care begins with exceptional people. Our team of
                            specialists spans
                            veterinary surgery, emergency and critical care, diagnostic imaging, and internal
                            medicine, each deeply skilled, each driven by the same commitment to your pet’s
                            well-being. Whatever the situation, you can trust the right expertise is always in the
                            room.</p>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                        <div class="team__item">
                            <div class="team__item-img">
                                <div class="mask-img-wrap">
                                    <img src="assets/img/team/dr-hamid-shah.webp" alt="img">
                                </div>
                                <div class="team__item-img-shape">
                                    <div class="shape-one">
                                        <img src="assets/img/team/team_img_shape01.svg" alt="" class="injectable">
                                    </div>
                                    <div class="shape-two">
                                        <img src="assets/img/team/team_img_shape02.svg" alt="" class="injectable">
                                    </div>
                                </div>
                            </div>
                            <div class="team__item-content">
                                <h4 class="title">Dr. Hamid Shah</h4>
                                <p>M.V.Sc., Ph.D.</p>
                                <p>Sr. Consulting Vet</p>
                                <p>Imaging (USG, ECHO)</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                        <div class="team__item">
                            <div class="team__item-img">
                                <div class="mask-img-wrap">
                                    <img src="assets/img/team/dr-abhilash-jadhao.webp" alt="img">
                                </div>
                                <div class="team__item-img-shape">
                                    <div class="shape-one">
                                        <img src="assets/img/team/team_img_shape01.svg" alt="" class="injectable">
                                    </div>
                                    <div class="shape-two">
                                        <img src="assets/img/team/team_img_shape02.svg" alt="" class="injectable">
                                    </div>
                                </div>
                            </div>
                            <div class="team__item-content">
                                <h4 class="title">Dr. Abhilash Jadhao</h4>
                                <p>M.V.Sc., Ph.D. (Pathology)</p>
                                <p>Pathology & Cytological</p>
                                <p>& Biopsy examination</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                        <div class="team__item">
                            <div class="team__item-img">
                                <div class="mask-img-wrap">
                                    <img src="assets/img/team/dr-aishwarya-r.webp" alt="img">
                                </div>
                                <div class="team__item-img-shape">
                                    <div class="shape-one">
                                        <img src="assets/img/team/team_img_shape01.svg" alt="" class="injectable">
                                    </div>
                                    <div class="shape-two">
                                        <img src="assets/img/team/team_img_shape02.svg" alt="" class="injectable">
                                    </div>
                                </div>
                            </div>
                            <div class="team__item-content">
                                <h4 class="title">Dr. Aishwarya R.</h4>
                                <p>Consulting Vet, ECC</p>
                                <p>(Emergency Critical Care),</p>
                                <p>Feline Medicine</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8">
                        <div class="team__item">
                            <div class="team__item-img">
                                <div class="mask-img-wrap">
                                    <img src="assets/img/team/dr-surbhi-gupta.webp" alt="img">
                                </div>
                                <div class="team__item-img-shape">
                                    <div class="shape-one">
                                        <img src="assets/img/team/team_img_shape01.svg" alt="" class="injectable">
                                    </div>
                                    <div class="shape-two">
                                        <img src="assets/img/team/team_img_shape02.svg" alt="" class="injectable">
                                    </div>
                                </div>
                            </div>
                            <div class="team__item-content">
                                <h4 class="title">Dr. Surbhi Gupta</h4>
                                <p>M.V.Sc., Ph.D. (Medicine)</p>
                                <p>Clinical medicine, Dermatology,</p>
                                <p>Intensive Care</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="team__bottom-content">
                    <a href="our-team.html" class="btn">View More <img src="assets/img/icon/right_arrow.svg" alt=""
                            class="injectable"></a>
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
                            <h2 class="title">Testimonials</h2>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-8 order-0 order-lg-2">
                        <div class="testimonial__img">
                            <div class="mask-img testimonial__img-mask">
                                <img src="assets/img/images/sah-testimonial-img.jpg" alt="img">
                            </div>

                            <div class="testimonial__img-shape">
                                <div class="shape-one">
                                    <img src="assets/img/images/testimonial_img_shape.svg" alt="" class="injectable">
                                </div>
                                <div class="shape-two">
                                    <img src="assets/img/images/testimonial_shape03.png" alt="img"
                                        class="alltuchtopdown">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="testimonial__item-wrap">
                            <div class="swiper testimonial-active">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>I want to sincerely thank the entire team at Tata Small Animal
                                                    Hospital for the incredible care they gave our beloved 15-year-old
                                                    dog, Brisco. He suffered a life-threatening spinal injury that left
                                                    him paralyzed in his hind legs, and during this terrifying time,
                                                    every single person—from the doctors to the medical staff, security
                                                    and housekeeping —showed us nothing but compassion, dedication, and
                                                    skill. Their tireless and selfless efforts ensured that Brisco could
                                                    return home to us, and today, he’s at home on the road to recovery.
                                                </p>
                                                <p>Tata Small Animal Hospital has been our go-to place for veterinary
                                                    care. It is a state-of-the-art facility—spotlessly clean, extremely
                                                    well-equipped, and home to some of the most skilled and
                                                    compassionate veterinary professionals in the country. The warmth
                                                    and love they extend to both animals and their humans is something
                                                    we will never forget.</p>
                                                <h2 class="title">Rishabh Sharma</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>I had a truly wonderful experience bringing my pet, Tyson, to Tata
                                                    Trust Small Animal Hospital. The entire team were exceptional from
                                                    start to finish. They treated Tyson with so much care and compassion
                                                    - it really put my mind at ease. The doctors were not only
                                                    professional and knowledgeable, but also incredibly patient and
                                                    kind. She took the time to explain Tyson's condition and treatment
                                                    options in a way that was easy to understand. She genuinely cares
                                                    about her patients and loves what she does. The staff was friendly,
                                                    the hospital was clean and well-organized, and everything ran
                                                    smoothly. I also appreciated how transparent they were with
                                                    pricing—there were no surprises, just honest service and top-quality
                                                    care.</p>
                                                <!-- <div class="testimonial__author">
                                                    <div class="testimonial__author-thumb">
                                                        <img src="assets/img/images/testi_author01.png" alt="">
                                                    </div>
                                                    <div class="testimonial__author-content">
                                                        <h4 class="title">Uraney Jacke</h4>
                                                        <span>Business Study</span>
                                                    </div>
                                                </div> -->
                                                <h2 class="title">Jyoti Bhatt</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>We are beyond grateful to Tata Small Animal Hospital for saving our
                                                    beloved puppy, Ted, from Parvo. The care, dedication, and
                                                    professionalism shown by the entire team were truly remarkable. Ted
                                                    is healthy and happy again because of you! Tata Small Animal
                                                    Hospital is truly a one-stop solution for all pet care needs. The
                                                    facilities are clean, the staff is courteous, and the medical
                                                    attention is top-notch. We highly recommend this hospital to all pet
                                                    parents.</p>
                                                <p>Thank you once again for everything!</p>
                                                <h2 class="title">Nishikant Khandle</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>From the level of care, to the facilities, to the hygiene standards -
                                                    this place was so good that I'd want to be treated there. Never
                                                    before seen in our country. The care, patience, and understanding of
                                                    every one of the staff - from the security guards at the gate, to
                                                    the reception, the handlers, nursing staff, and the veterinarian
                                                    doctors themselves - were all well above simply doing a job. They
                                                    truly care, both for the animals and their parents.10/10 would
                                                    definitely recommend.
                                                </p>
                                                <h2 class="title">Tarun K.</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>I have taken the online consultation for my pet it helped me a lot
                                                    with all the problems that my pet has been dealing with. Had a great
                                                    experience in both clinical as well as non-clinical studies. Your
                                                    documentation is always thorough and timely ensuring continuity of
                                                    care. I appreciate your calm demeanor and ability to effectively
                                                    communicate with patients, especially during stressful situations.
                                                    The doctors always listens to you very patiently and allows you to
                                                    communicate effectively and afterwards you will feel comfortable
                                                    with the situation. The pharmacy department also helped me his way
                                                    best to provide medication.
                                                </p>
                                                <h2 class="title">Shivam K</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>My pet dog had a severe seizure last night. We brought him down from
                                                    Singapore this year and he has been in Mumbai for the past 6 months.
                                                    We called up the hospital and they responded immediately, while we
                                                    were on the way to the hospital with our pet, they called to check
                                                    our whereabouts and get the basic information about him, upon
                                                    arrival there was a full team waiting at the entrance with a
                                                    stretcher, ready to take our dog in. The hospital was clean, the
                                                    staff was extremely friendly and helpful and overall one of the best
                                                    experiences we have had In Mumbai. Thank you to the doctors and
                                                    staff that helped us, and assured us during tough times.
                                                </p>
                                                <h2 class="title">Vedika Sharma</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>Our feline pet Nico is a Persian Cat male who was recently suffering
                                                    from urinary tract blockages . We drove down from Pune and were
                                                    warmly welcomed by the reception staff. They immediately made Nico
                                                    comfortable and arranged for him to be taken to Triage room . The
                                                    doc handled Nico in a very gentle manner. Nico was very calm and
                                                    comfortable. Immediately after this Nico was consulted by a doctor
                                                    who explained us about Nico’s condition thoroughly and answered all
                                                    our questions patiently.
                                                    Nico was prescribed few medicines which were readily available at
                                                    the 1mg Pharmacy in the same premises.</p>
                                                <p>The facility is well equipped with state of the art equipment and
                                                    trained doctors. We found that we were not rushed into any
                                                    unnecessary tests as they
                                                    went through the tests done previously. As a pet parent I am
                                                    grateful to all the staff for such positive experience.
                                                </p>
                                                <h2 class="title">Ashwini Ganapule</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>I recently visited the Small Animal Hospital of Tata Trust with my
                                                    15-year-old dog for an FNAC procedure. I must say, this hospital is
                                                    an excellent facility, especially for cats and dogs. The doctors and
                                                    staff are highly cooperative, professional, and caring, making the
                                                    entire process smooth and comfortable. The hospital maintains
                                                    extremely high standards of cleanliness, ensuring a hygienic and
                                                    pleasant environment for both pets and their owners. Another great
                                                    advantage is that all investigation facilities are available
                                                    in-house, making it a one-stop solution for pet healthcare needs.
                                                </p>
                                                <h2 class="title">Dr. Indranil Bhattacharaya</h2>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="swiper-slide">
                                        <div class="testimonial__item">
                                            <div class="testimonial__icon">
                                                <img src="assets/img/icon/quote.svg" alt="" class="injectable">
                                            </div>
                                            <div class="testimonial__content">
                                                <p>My dachshund came to you with pyometra. Doctors operated successfully
                                                    and she was discharged. She is well. The reception staff was prompt,
                                                    polite and extremely patient with me (I was hyper)! The Drs
                                                    explained everything very clearly and again were kind enough to
                                                    answer all my questions. After the surgery too Titli got great care
                                                    and I felt confident to leave her for the night at the hospital. All
                                                    in all AMAZING hospital and great staff.
                                                </p>
                                                <h2 class="title">Dia Bagve</h2>
                                            </div>
                                        </div>
                                    </div>

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
                    <div class="col-lg-6 text-center position-relative">
                        <div class="image-stack">
                            <img src="assets/img/images/Board1.png" class="img-fluid main-img" alt="Ratan Tata">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="glass-card p-4 p-md-5">
                            <h2 class="main-title">
                                Our Board
                            </h2>
                            <p class="desc mb-4">The Advanced Veterinary Care Foundation (AVCF) oversees the management
                                of the Small Animal Hospital Mumbai, with support from Tata Trusts. The AVCF Board of
                                Directors include:</p>
                            <div class="home-about-world-class-care-sec">
                                <a href="our-team.html#our-team-our-board-sec" class="btn">Read More<img src="assets/img/icon/right_arrow.svg" alt=""
                                        class="injectable"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        
        <section class="pet-instagram-section">
            <div class="container">
                <div class="row align-items-center justify-content-center">

                    <div class="col-lg-4">
                        <div class="why__we-are-content">
                            <div class="section__title">
                                <h2 class="title">Follow Us</h2>
                            </div>
                            <p>
                                Stay connected with us on Instagram to receive updates, pet care tips,
                                and information about our services. Join our community of pet lovers.
                            </p>

                            <div class="home-about-world-class-care-sec">
                                <div class="home-about-world-class-care-sec">
                                    <a href="https://www.instagram.com/sahmumbai" target="_blank" class="btn">Click To
                                        Join<img src="assets/img/icon/right_arrow.svg" alt="" class="injectable"></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="insta-stack-wrapper">
                            <img src="assets/img/images/followus.jpg" class="follow-us-oa-custom-img" alt="Cute Dog">
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
                            <a href="tel:02265383538" class="btn">Contact Us<img src="assets/img/icon/right_arrow.svg"
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