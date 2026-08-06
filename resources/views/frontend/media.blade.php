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
                <img src="{{ !empty($media_settings->banner_image) ? asset('home/media/'.$media_settings->banner_image) : asset('frontend/assets/img/banner/about-new-banner.webp') }}" alt="">
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">{{ $media_settings->heading ?? 'Media' }}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Media</li>
                </ul>
            </div>
        </section>


        <section class="media-custom-listing-main-sec">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up">{{ $media_settings->section_heading ?? 'Media' }}</h2>
                        </div>
                    </div>
                </div>

                <div class="media-custom-listing-main-card-sec">
                    <div class="row g-4 justify-content-center">

                        @forelse($media_items as $item)
                            <div class="col-lg-3">
                                <div class="item">
                                    <div class="img">
                                        <div class="br-sh">
                                            <img src="{{ asset('home/media/'.$item->image) }}" class="img-fluid" alt="{{ $item->title }}">
                                        </div>
                                    </div>
                                    <div class="cont">
                                        <h3>{{ $item->title }}</h3>
                                        <a href="{{ $item->article_link }}" target="_blank" rel="noopener noreferrer" class="media-custom-btn">Read More</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted py-5">No media available yet.</p>
                            </div>
                        @endforelse

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
