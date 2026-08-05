
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
        <section class="breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                @if(!empty($detail->banner_image))
                    <img src="{{ asset('home/speciality-details/'.$detail->banner_image) }}"
                        alt="{{ $speciality->speciality }}">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/blood-transfusion-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">{{ $speciality->speciality }}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li><a href="{{ route('frontend.specialities') }}">Specialities</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>{{ $speciality->speciality }}</li>
                </ul>
            </div>
        </section>

        <section class="facilities-details-main-sec">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="our-fac-details-sidebar-img-sec">
                            @if(!empty($detail->section_image))
                                <img src="{{ asset('home/speciality-details/'.$detail->section_image) }}"
                                    alt="{{ $detail->section_heading }}">
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="our-fac-details-sidebar-content-sec">
                            <h4>{{ $detail->section_heading }}</h4>
                            {!! $detail->section_description !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if(is_array($detail->services) && count($detail->services) > 0)
        <section class="specilities-new-custom-seven-sec">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="our-fac-details-sidebar-content-sec">
                            <h4>{{ $detail->service_heading }}</h4>
                        </div>
                    </div>
                </div>
                @php
                    // Distribute items column-first across 3 columns so vertical reading
                    // order matches the order the admin entered them.
                    $servicesList = array_values($detail->services);
                    $numColumns   = 3;
                    $totalItems   = count($servicesList);
                    $rowsPerCol   = (int) ceil($totalItems / $numColumns);
                    $serviceCols  = [];
                    foreach ($servicesList as $i => $svc) {
                        $col = intdiv($i, $rowsPerCol);
                        $serviceCols[$col][] = $svc;
                    }
                @endphp

                <div class="row align-items-start g-3">
                    @foreach($serviceCols as $columnItems)
                        <div class="col-lg-4 d-flex flex-column gap-3">
                            @foreach($columnItems as $service)
                                <div class="our-fac-det-eyebrow">
                                    <div class="our-fac-det-dot">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <p>{{ $service }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
                @if(!empty($detail->short_info))
                    <div class="row">
                        <div class="col-lg-12">
                            <p class="spec-details-custom-para">{{ $detail->short_info }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </section>
        @endif


        @if($detail->doctors->count() > 0)
            <section class="doctor-profile-section">
                <div class="container">
                    @foreach($detail->doctors as $doctor)
                        @php
                            // Use per-speciality override if set, otherwise fall back to the master bio
                            $doctorBio = trim((string) $doctor->pivot->bio_override) !== ''
                                ? $doctor->pivot->bio_override
                                : $doctor->bio;
                        @endphp
                        <div class="doctor-card {{ ! $loop->last ? 'mb-4' : '' }}">
                            <div class="row">
                                <div class="col-md-3 no-gap">
                                    <div class="doctor-image gau-doc-image-sec">
                                        @if(!empty($doctor->image))
                                            <img src="{{ asset('our-team/'.$doctor->image) }}"
                                                alt="{{ $doctor->name }}" class="img-responsive">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-9 no-gap">
                                    <div class="doctor-content gau-doc-content-sec">
                                        <h2>{{ $doctor->name }}</h2>
                                        @if(!empty($doctor->designation))
                                            <h4>{{ strip_tags($doctor->designation) }}</h4>
                                        @endif
                                        @if(!empty($doctorBio))
                                            {!! nl2br(e($doctorBio)) !!}
                                        @endif
                                    </div>
                                </div>
                            </div>
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
