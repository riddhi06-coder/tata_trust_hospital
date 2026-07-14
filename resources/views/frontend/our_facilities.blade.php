
<!DOCTYPE html>
<html lang="en">
  <head>

    @include('components.frontend.head')

  </head>
  <body>
   
    @include('components.frontend.header')

    
    <!-- main-area -->
    <main>


 <!-- Breadcrumb -->
        <section class="breadcrumb our-facilities-breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                @if($facility_settings && !empty($facility_settings->banner_image))
                    <img src="{{ asset('home/master-facilities/'.$facility_settings->banner_image) }}"
                        alt="{{ $facility_settings->banner_heading ?? 'Our Facilities' }}">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/facilities-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                @php
                    $facilityHeading = $facility_settings->banner_heading ?? 'Space That Comforts, Care That Heals';
                    // Break the heading onto a second line after the first comma.
                    $facilityHeadingHtml = preg_replace('/,\s*/', ',<br>', e($facilityHeading), 1);
                @endphp
                <h1 class="breadcrumb-title">{!! $facilityHeadingHtml !!}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Our Facilities</li>
                </ul>
            </div>
        </section>

        <section class="our-facilities-main-sec">
            <div class="container">
                <div class="our-facilities-title-sec">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section__title section_title-two text-center mb-40">
                                <h2 class="title" data-aos="fade-up">{{ $facility_settings->section_heading ?? 'Our Facilities' }}</h2>
                            </div>
                            @if($facility_settings && !empty($facility_settings->section_description))
                                <div class="para-mb text-center" data-aos="fade-up" data-aos-delay="150">
                                    {!! $facility_settings->section_description !!}
                                </div>
                            
                            @endif
                        </div>
                    </div>
                </div>

                @if($facilities->isNotEmpty())
                <div class="our-facilities-two-col-sec">
                    <div class="row">

                        <div class="col-lg-4">
                            <div class="our-facilities-left-sidebar">
                                <div class="our-facilities-left-catagery-list">
                                    <ul>
                                        @foreach($facilities as $item)
                                            <li><a href="#{{ \Illuminate\Support\Str::slug($item->name) }}">{{ $item->name }} <img
                                                        src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="Arrow Icon"
                                                        class="injectable"></a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="our-facilities-right-sidebar">

                                @foreach($facilities as $item)
                                    @php $contentFirst = $loop->odd; @endphp
                                    <div class="our-facilities-right-sidebar-row {{ $contentFirst ? 'our-fac-right-sidebar-row-even' : '' }}" id="{{ \Illuminate\Support\Str::slug($item->name) }}">
                                        <div class="row align-items-center">
                                            @if($contentFirst)
                                                <div class="col-lg-6">
                                                    <div class="our-fac-rig-sidebar-content-sec">
                                                        <h4 data-aos="fade-right">{{ $item->name }}</h4>
                                                        <div data-aos="fade-right">{!! $item->description !!}</div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="our-fac-rig-sidebar-img-sec">
                                                        <img src="{{ asset('home/master-facilities/'.$item->image) }}"
                                                            alt="{{ $item->name }}" data-aos="fade-left">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-lg-6">
                                                    <div class="our-fac-rig-sidebar-img-sec">
                                                        <img src="{{ asset('home/master-facilities/'.$item->image) }}"
                                                            alt="{{ $item->name }}" data-aos="fade-right">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="our-fac-rig-sidebar-content-sec">
                                                        <h4 data-aos="fade-left">{{ $item->name }}</h4>
                                                        <div data-aos="fade-left">{!! $item->description !!}</div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                            </div>


                        </div>

                    </div>
                </div>
                @endif

            </div>
        </section>
    
    </main>
    <!-- main-area-end -->


    
    @include('components.frontend.footer')
     
    @include('components.frontend.main-js')

    <script>
        (function () {
            // Smoothly scroll to a facility section, offsetting for the sticky header.
            function scrollToFacility(id, smooth) {
                var el = document.getElementById(id);
                if (!el) return;
                var header = document.getElementById('sticky-header');
                var offset = (header ? header.offsetHeight : 0) + 20;
                var y = el.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({ top: y, behavior: smooth ? 'smooth' : 'auto' });
            }

            // On load: if the URL has a #section (e.g. coming from the home page), jump to it.
            window.addEventListener('load', function () {
                if (!window.location.hash) return;
                var id = decodeURIComponent(window.location.hash.substring(1));
                // Delay lets images/AOS settle so the final position is accurate.
                setTimeout(function () { scrollToFacility(id, false); }, 350);
            });

            // In-page sidebar links: scroll with the same header offset.
            document.querySelectorAll('.our-facilities-left-catagery-list a[href^="#"]').forEach(function (link) {
                link.addEventListener('click', function (e) {
                    var id = decodeURIComponent(this.getAttribute('href').substring(1));
                    if (!document.getElementById(id)) return;
                    e.preventDefault();
                    history.replaceState(null, '', '#' + id);
                    scrollToFacility(id, true);
                });
            });
        })();
    </script>

  </body>
</html>