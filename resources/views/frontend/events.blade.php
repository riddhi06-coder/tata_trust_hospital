
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
                <img src="{{ asset('frontend/assets/img/banner/about-new-banner.webp') }}" alt="">
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">{{ $event_settings->section_heading1 ?? 'Events' }}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ route('frontend.index') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Events</li>
                </ul>
            </div>
        </section>

        <section class="events-listing-main-sec">
            <div class="container">
                <div class="row g-4">

                    @forelse($events as $event)
                        @php
                            $thumbUrl = !empty($event->thumbnail) ? asset('home/events/'.$event->thumbnail) : '';
                            $fullUrl  = !empty($event->image) ? asset('home/events/'.$event->image) : $thumbUrl;
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-card">
                                <div class="gallery-card__img-wrap">
                                    <img src="{{ $thumbUrl }}" alt="{{ $event->title }}"
                                        class="gallery-card__img">
                                    @if(!empty($event->period_label))
                                        <div class="gallery-card__date">
                                            <span>{{ $event->period_label }}</span>
                                        </div>
                                    @endif
                                    <div class="gallery-card__overlay">
                                        <a href="{{ $fullUrl }}" class="gallery-card__view-btn popup-image">View</a>
                                    </div>
                                    <a href="{{ $fullUrl }}" class="gallery-card__arrow-btn popup-image" aria-label="View {{ $event->title }}">
                                        <img src="{{ asset('frontend/assets/img/icon/right-up.webp') }}" alt="">
                                    </a>
                                </div>
                                <div class="gallery-card__info">
                                    <h3 class="gallery-card__title">{{ $event->title }}</h3>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted py-5">No events available yet.</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </section>


    </main>
    <!-- main-area-end -->



    @include('components.frontend.footer')

    @include('components.frontend.main-js')

  </body>
</html>
