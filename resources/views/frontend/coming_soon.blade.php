
<!DOCTYPE html>
<html lang="en">
  <head>

    @include('components.frontend.head')

  </head>
  <body>

    @include('components.frontend.header')


    <!-- main-area -->
    <main class="fix">


        <section class="coming-soon-custom-sec">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="coming-soon-main-sec-custom">
                            <img src="{{ (' frontend/assets/img/images/coming-soon-img-1.webp') }}" alt="">
                            <div class="section__title section_title-two text-center mb-40">
                                <h2 class="title">Coming Soon</h2>
                            </div>
                            <p class="text-center" data-aos="fade-up" data-aos-delay="150">Stay tuned for updates. We will get back to you soon!</p>
                            <a href="{{ route('frontend.coming_soon') }}" class="btn" data-aos="fade-up" data-aos-delay="200">Back to Home<span class="visually-hidden">Home Page</span><img src="{{ (' frontend/assets/img/icon/right_arrow.svg' ) }}" alt="Read More" class="injectable"></a>
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