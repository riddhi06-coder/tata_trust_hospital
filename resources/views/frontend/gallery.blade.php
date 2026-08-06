
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
                @if($gallery_settings && !empty($gallery_settings->banner_media) && $gallery_settings->media_type === 'image')
                    <img src="{{ asset('home/gallery/'.$gallery_settings->banner_media) }}"
                        alt="{{ $gallery_settings->banner_heading ?? 'Gallery' }}">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/about-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                @php
                    $headingText  = $gallery_settings->banner_heading ?? 'GALLERY';
                    $headingWords = preg_split('/\s+/', trim($headingText));
                    $firstPart    = e(implode(' ', array_slice($headingWords, 0, 3)));
                    $restPart     = e(implode(' ', array_slice($headingWords, 3)));
                    $headingHtml  = $restPart !== '' ? $firstPart.'<br>'.$restPart : $firstPart;
                @endphp
                <h1 class="breadcrumb-title">{!! $headingHtml !!}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Gallery</li>
                </ul>
            </div>
        </section>

        <section class="gallery-main-sec">
            <div class="container">
                <div class="gallery-title-sec">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section__title section_title-two text-center mb-40">
                                <h2 class="title" data-aos="fade-up" data-aos-delay="150">
                                    {{ $gallery_settings->section_heading ?? 'GALLERY' }}
                                </h2>
                            </div>
                            <div class="text-center gallery-section-description" data-aos="fade-up" data-aos-delay="200">
                                {!! $gallery_settings->section_description ?? 'From advanced infrastructure to compassionate treatment, these snapshots capture the best of veterinary care under one roof.' !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box"></div>

                <div class="gallery__area">
                    <div class="row g-4 justify-content-center">

                        @forelse($gallery_images as $index => $img)
                            @php
                                $delays  = [150, 250, 350];
                                $aosDelay = $delays[$index % 3];
                                $imgUrl   = asset('home/gallery/'.$img->image);
                                $altText  = $gallery_settings->banner_heading ?? 'Gallery image';
                            @endphp

                            <div class="col-lg-4 col-md-6">
                                <div class="gallery__item" data-aos="fade-up" data-aos-delay="{{ $aosDelay }}">
                                    <div class="gallery__img">
                                        <img src="{{ $imgUrl }}" alt="{{ $altText }}">
                                    </div>
                                    <a href="{{ $imgUrl }}" class="popup-image">
                                        <img src="{{ asset('frontend/assets/img/icon/gallery-icon.webp') }}" alt="" class="injectable">
                                    </a>

                                    <div class="gallery-animal-hospital-content">
                                        <h3 class="gallery-animal-hospital-title">Clients consulting with the doctor</h3>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted py-5">No gallery images available yet.</p>
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
