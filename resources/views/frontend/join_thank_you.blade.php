
<!doctype html>
<html lang="en">

    <head>
        @include('components.frontend.head')

        <style>
            .form-thank-you-sec{padding:80px 0}.form-thank-you-content-main-sec{text-align:center}.form-thank-you-content-main-sec img{width:200px;margin-bottom:30px}.form-thank-you-content-main-sec p{margin-bottom:0}
        </style>
    </head>

    <body>
   
        @include('components.frontend.header')

        <!-- main-area -->
        <main>

            <section class="form-thank-you-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-thank-you-content-main-sec">
                                <img src="{{ (' frontend/assets/img/icon/open-mail-icon-one.webp') }}" alt="Thank You Icon">
                                <div class="section__title section_title-two text-center mb-40">
                                    <h1 class="title" data-aos="fade-up">Thank You</h1>
                                </div>
                                <p class="text-center" data-aos="fade-up" data-aos-delay="150">We have received your request successfully, and our team will get back to you as soon as possible.<br>
                                    We appreciate your interest and look forward to assisting you.</p>
                                <a href="https://sahmumbai.com/" class="btn mt-40" data-aos="fade-up" data-aos-delay="200">Back to Home<span class="visually-hidden">Home Page</span><img src="{{ (' frontend/assets/img/icon/right_arrow.svg') }}" alt="Read More" class="injectable"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>


        @include('components.frontend.footer')
        
        @include('components.frontend.main-js')

    </body>

    
</html>