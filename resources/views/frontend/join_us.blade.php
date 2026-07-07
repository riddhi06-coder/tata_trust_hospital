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
        <section class="breadcrumb join-us-breadcrumb-bg">
            <div class="breadcrumb-img-custom-sec">
                @if($join_page && $join_page->banner_image)
                    <img src="{{ asset('home/join-us/banner/'.$join_page->banner_image) }}" alt="">
                @else
                    <img src="{{ asset('assets/img/banner/about-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">
                    @if($join_page && $join_page->banner_heading)
                        {!! nl2br($join_page->banner_heading) !!}
                    @else
                        Be A Part Of <br>Our Team!
                    @endif
                </h1>
                <ul class="breadcrumb-nav">
                    <li><a href="./">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Join Us</li>
                </ul>
            </div>
        </section>

        <section class="join-us-one-sec">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-12">
                        <div class="section__title section_title-two text-center">
                            <h2 class="title" data-aos="fade-up" data-aos-delay="150">{{ $join_page->section_heading ?? 'Join Us' }}</h2>
                        </div>
                        <div class="para-mb text-center" data-aos="fade-up" data-aos-delay="150">
                            {!! $join_page->section_description ?? '' !!}
                        </div>
                    </div>

                    @if($join_page && $join_page->infos->count())
                        @foreach($join_page->infos as $i => $info)
                            <div class="col-lg-6">
                                <div class="join-us-why-choose-what-we-look-sec" data-aos="fade-up" data-aos-delay="{{ 150 + ($i * 100) }}">
                                    <div class="feature-one__single-inner">
                                        @if($info->image)
                                            <div class="juwcwwl-img-sec">
                                                <img src="{{ asset('home/join-us/info/'.$info->image) }}" alt="{{ $info->title }}">
                                            </div>
                                        @endif
                                        <h3 class="feature-one__title">{{ $info->title }}</h3>
                                        <div class="feature-one__text">{!! $info->description !!}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        </section>



        <section class="current-job-openings-section">
            <div class="container">
                <div class="current-job-opening-title-sec">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section__title section_title-two text-center">
                                <h2 class="title" data-aos="fade-up" data-aos-delay="100">{{ $join_page->current_job_title ?? 'Current Job Openings' }}</h2>
                            </div>
                            <div class="para-mb text-center" data-aos="fade-up" data-aos-delay="150">
                                {!! $join_page->current_job_description ?? '' !!}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Job role cards — kept static (not driven by CMS) --}}
                <div class="current-job-opening-job-card-sec">
                    <div class="job-card" data-aos="fade-up" data-aos-delay="100">
                        <div>
                            <h4>Hospital Operations Manager</h4>
                            <div class="job-meta">
                                <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-1.webp') }}" alt=""> Mumbai,
                                    Maharashtra</p>
                                <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-2.webp') }}" alt=""> <i
                                        class="bi bi-clock"></i>Full-time</p>
                                <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-3.webp') }}" alt=""> On-site</p>
                            </div>
                        </div>
                        <div class="job-actions">
                            <a href="javascript:void(0)" class="btn apply-now-btn" data-bs-toggle="modal"
                               data-bs-target="#appointmentModal" data-job-title="Hospital Operations Manager">Apply Now<img
                                    src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable"></a>
                            <a href="{{ asset('frontend/assets/pdf/hospital-operations-manager-pdf.pdf') }}" target="_blank"
                                class="btn btn-job-sec" title="View Job Description"><img
                                    src="{{ asset('frontend/assets/img/icon/pdf-download-icon.webp') }}" alt=""></a>
                        </div>
                    </div>

                    <div class="job-card" data-aos="fade-up" data-aos-delay="150">
                        <div>
                            <h4>Client Care Associate</h4>
                            <div class="job-meta">
                                <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-1.webp') }}" alt=""> Mumbai,
                                    Maharashtra</p>
                                <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-2.webp') }}" alt=""> <i
                                        class="bi bi-clock"></i>Full-time</p>
                                <p><img src="{{ asset('frontend/assets/img/icon/current-job-opening-icon-3.webp') }}" alt=""> On-site</p>
                            </div>
                        </div>
                        <div class="job-actions">
                            <a href="javascript:void(0)" class="btn apply-now-btn" data-bs-toggle="modal"
                               data-bs-target="#appointmentModal" data-job-title="Client Care Associate">Apply Now<img
                                    src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable"></a>
                            <a href="{{ asset('frontend/assets/pdf/client-care-associate-icon-pdf.pdf') }}" target="_blank"
                                class="btn btn-job-sec" title="View Job Description"><img
                                    src="{{ asset('frontend/assets/img/icon/pdf-download-icon.webp') }}" alt=""></a>
                        </div>
                    </div>

                    <div class="job-card" data-aos="fade-up" data-aos-delay="200">
                        <div>
                            <h4>Nursing Manager</h4>
                            <div class="job-meta">
                                <p><img src="assets/img/icon/current-job-opening-icon-1.webp" alt=""> Mumbai,
                                    Maharashtra</p>
                                <p><img src="assets/img/icon/current-job-opening-icon-2.webp" alt=""> <i
                                        class="bi bi-clock"></i>Full-time</p>
                                <p><img src="assets/img/icon/current-job-opening-icon-3.webp" alt=""> On-site</p>
                            </div>
                        </div>
                        <div class="job-actions">
                            <a href="javascript:void(0)" class="btn apply-now-btn" data-bs-toggle="modal"
                               data-bs-target="#appointmentModal" data-job-title="Nursing Manager">Apply Now<img
                                    src="assets/img/icon/right_arrow.svg" alt="" class="injectable"></a>
                            <a href="assets/pdf/nursing-manager-pdf.pdf" target="_blank" class="btn btn-job-sec"
                                title="View Job Description"><img src="assets/img/icon/pdf-download-icon.webp"
                                    alt=""></a>
                        </div>
                    </div>
                </div>
                
            </div>
        </section>

        <section class="career-section">
            <div class="container career-inner">

                <div class="row align-items-center g-5">

                    <!-- ── LEFT ── -->
                    <div class="col-lg-5">
                        <div class="career-eyebrow" data-aos="fade-right" data-aos-delay="100">
                            <div class="career-eyebrow-dot">
                                <i class="far fa-eye"></i>
                            </div>
                            <span>{{ $join_page->common_heading ?? "Can't find a suitable role?" }}</span>
                        </div>

                        <div class="section__title">
                            <h2 class="title" data-aos="fade-right" data-aos-delay="150">{{ $join_page->common_title ?? "Don't worry, connect with our team." }}</h2>
                        </div>
                        <div data-aos="fade-right" data-aos-delay="200">
                            {!! $join_page->common_description ?? '' !!}
                        </div>

                        <a href="mailto:careers@sahmumbai.com" target="_blank" class="btn" data-aos="fade-right" data-aos-delay="200">Submit Now<img
                                src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable"></a>

                    </div>

                    <!-- ── RIGHT ── -->
                    <div class="col-lg-7">
                        <div class="app-box">

                            <!-- Subject Cards -->
                            <div class="subject-cards">

                                @if($join_page && $join_page->commonRows->count())
                                    @foreach($join_page->commonRows as $i => $row)
                                        <div class="scard" data-aos="fade-left" data-aos-delay="{{ 100 + ($i * 50) }}">
                                            <div class="scard-num">
                                                <img src="{{ asset('frontend/assets/img/icon/job-foo-icon.webp') }}" alt="">
                                            </div>
                                            <div class="scard-body">
                                                <div class="scard-role">{{ $row->job_title }}</div>
                                                <div class="scard-tag">
                                                    <span>{{ $row->subject }}/span>
                                                </div>
                                                @if($row->description)
                                                    <div class="scard-desc">{!! $row->description !!}</div>
                                                @endif
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

        <section class="join-us-footer-sec"
                 @if($join_page && $join_page->extra_background_image)
                    style="background-image: url('{{ asset('home/join-us/extra/'.$join_page->extra_background_image) }}');"
                 @endif>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="join-us-footer-content-sec" data-aos="fade-right" data-aos-delay="150">
                            {!! $join_page->extra_description ?? '' !!}
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
