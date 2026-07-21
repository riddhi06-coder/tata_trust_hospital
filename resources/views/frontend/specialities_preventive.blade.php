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
        <section class="breadcrumb preventive-care-breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                <img src="{{ asset('home/speciality-details/'.$detail->banner_image) }}" alt="{{ $speciality->speciality }}">
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">{{ $speciality->speciality }}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>{{ $speciality->speciality }}</li>
                </ul>
            </div>
        </section>

        <section class="preventive-care-main-sec">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="preventive-care-sidebar-img-sec">
                            <img src="{{ asset('home/speciality-details/'.$detail->section_image) }}" alt="{{ $detail->preventive_section_heading }}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="preventive-care-sidebar-content-sec">
                            <div class="section__title">
                                <h2 class="title" data-aos="fade-right">{{ $detail->section_heading }}</h2>
                            </div>
                            <div class="cke-editor" data-aos="fade-up" data-aos-delay="150">
                                {!! $detail->section_description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @php $services = $detail->preventive_services ?? []; @endphp
        @if(count($services))
        <section class="preventive-care-custom-seven-sec">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up">{{ $detail->preventive_section_heading }}</h2>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center g-2 justify-content-center">
                    @foreach($services as $service)
                        <div class="col-lg-2" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                            <div class="per-care-custom-sev-eyebrow">
                                <div class="pre-care-custom-det-dot">
                                    <img src="{{ asset('home/speciality-details/'.$service['image']) }}" alt="{{ $service['name'] }}">
                                </div>
                                <p>{{ $service['name'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(!empty($detail->preventive_section_description))
                <div class="row">
                    <div class="col-lg-12">
                        <div class="per-care-custom-sev-custom-para cke-editor" data-aos="fade-up">
                            {!! $detail->preventive_section_description !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>
        @endif

        @php $planGroups = $detail->preventive_plans ?? []; @endphp
        <section class="life-stage-packages-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up">{{ $detail->preventive_plans_heading }}</h2>
                        </div>
                        <div class="cke-editor text-center" data-aos="fade-up" data-aos-delay="150">
                            {!! $detail->preventive_plans_description !!}
                        </div>
                    </div>
                </div>

                @if(count($planGroups))
                <div class="life-stage-packages-dogs-cats-sec">
                    <div class="row">
                        @foreach($planGroups as $group)
                            <div class="col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                                <div class="{{ $loop->index % 2 === 0 ? 'life-stage-packages-dogs-custom-sec' : 'life-stage-packages-cats-custom-sec' }}">
                                    <span class="most-loved">{{ $group['category'] ?? '' }}</span>
                                    <div class="row g-5">
                                        @foreach(($group['plans'] ?? []) as $plan)
                                            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ ($loop->index + 1) * 150 }}">
                                                <div class="badge-card">
                                                    <div class="badge-circle">
                                                        <img src="{{ asset('home/speciality-details/'.$plan['image']) }}" alt="{{ $plan['name'] ?? '' }}">
                                                    </div>
                                                    <div class="plan-name">{{ $plan['name'] ?? '' }}</div>
                                                    <div class="plan-age">{{ $plan['age_range'] ?? '' }}</div>
                                                    <div class="plan-price">{{ $plan['cost'] ?? '' }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif


                @if(!empty($detail->preventive_disclaimer))
                <div class="fine-print" data-aos="fade-up">
                    <div class="paw-seal">
                        <img src="{{ asset('frontend/assets/img/icon/extra-paw-icon.webp') }}" alt="">
                    </div>
                    <div class="fine-print-custom-para">
                        {!! $detail->preventive_disclaimer !!}
                    </div>
                </div>
                @endif

            </div>
        </section>


    </main>
    <!-- main-area-end -->


    @include('components.frontend.footer')

    @include('components.frontend.main-js')


  </body>
</html>
