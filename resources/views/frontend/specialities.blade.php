
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
        <section class="breadcrumb our-specialities-breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                @if($speciality_settings && !empty($speciality_settings->banner_image))
                    <img src="{{ asset('home/specialities/'.$speciality_settings->banner_image) }}"
                        alt="{{ $speciality_settings->banner_heading ?? 'Specialities' }}">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/specialities-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                @php
                    $bannerHeading = $speciality_settings->banner_heading ?? 'Veterinary Specialities For Every Step Of Your Pet\'s Care';
                    $headingHtml   = preg_replace('/\b(for)\b/i', '$1<br>', e($bannerHeading), 1);
                @endphp
                <h1 class="breadcrumb-title">{!! $headingHtml !!}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Specialities</li>
                </ul>
            </div>
        </section>

        <section class="specialities-sec">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up">
                                {{ $speciality_settings->title ?? 'Our Services' }}
                            </h2>
                        </div>
                        @if($speciality_settings && !empty($speciality_settings->service_description))
                            <div class="text-center specialities-services-description" data-aos="fade-up" data-aos-delay="150">
                                {!! $speciality_settings->service_description !!}
                            </div>
                        @endif
                    </div>
                </div>


                <div class="col-lg-12">
                    <!-- Nav tabs -->
                    <ul class="specialities-nav nav nav-tabs" id="specialitiesTabs" role="tablist">
                        @if($home_services && !empty($home_services->specialities))
                            @foreach($home_services->specialities as $index => $service)
                                @php
                                    $delays  = [0, 150, 250, 300, 250, 200];
                                    $aosDelay = $delays[$index % 6];
                                    $paneId   = 'pane-service-'.$index;
                                @endphp
                                <li class="nav-item" role="presentation">
                                    <div class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                        data-bs-toggle="tab"
                                        data-bs-target="#{{ $paneId }}"
                                        data-aos="fade-up"
                                        @if($aosDelay > 0) data-aos-delay="{{ $aosDelay }}" @endif>
                                        <img src="{{ asset('home/specialities/'.$service['icon']) }}"
                                            alt="{{ $service['name'] }}">{{ $service['name'] }}
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    <!-- /nav -->
                </div>
            </div>
        </section>


        @if($speciality_items->count() > 0)
        <section class="our-speci-custom-ten-sec">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up">
                                SPECIALITIES
                            </h2>
                        </div>
                    </div>
                </div>

                @foreach($speciality_items->chunk(5) as $chunkIndex => $chunk)
                    @if($chunkIndex > 0)<br>@endif

                    <div class="row justify-content-center g-4">
                        @foreach($chunk->values() as $index => $item)
                            @php $aosDelay = $index * 150; @endphp
                            <div class="col-lg-2"
                                data-aos="fade-up"
                                @if($aosDelay > 0) data-aos-delay="{{ $aosDelay }}" @endif>
                                <a href="{{ url('specialities/'.$item->slug) }}" class="spec-custom-ten-card-sec">
                                    <div class="spec-custom-ten-card-content-sec">
                                        <img src="{{ asset('home/specialities/'.$item->image) }}" alt="{{ $item->speciality }}">
                                        <h4>{{ $item->speciality }}</h4>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
        </section>
        @endif

    

    </main>
    <!-- main-area-end -->


    @include('components.frontend.footer')

    @include('components.frontend.main-js')


  </body>
</html>
