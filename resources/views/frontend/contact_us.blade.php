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
        <section class="breadcrumb contact-us-breadcrumb-bg">
            <div class="breadcrumb-img-custom-sec">
                @if($contact && $contact->banner_image)
                    <img src="{{ asset('home/contact/banner/'.$contact->banner_image) }}" alt="">
                @else
                    <img src="{{ asset('assets/img/banner/contact-new-banner.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                <h1 class="breadcrumb-title">
                    @if($contact && $contact->banner_heading)
                        @php
                            $heading = nl2br($contact->banner_heading);
                            // If the admin didn't add a break themselves, auto-break after the first comma.
                            if (!str_contains($heading, '<br')) {
                                $heading = preg_replace('/,\s*/', ',<br>', $heading, 1);
                            }
                        @endphp
                        {!! $heading !!}
                    @else
                        Reach Out, <br> We Are Ready To Help.
                    @endif
                </h1>
                <ul class="breadcrumb-nav">
                    <li><a href="./">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Contact Us</li>
                </ul>
            </div>
        </section>

        <section class="contact-us-main-sec">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12"></div>
                </div>
            </div>
        </section>

        <section class="dm-contact-page">
            <div class="container">
                <div class="row g-4 align-items-stretch">
                    <div class="col-xl-12 col-lg-12">
                        <div class="section__title section_title-two text-center">
                            <h2 class="title" data-aos="fade-up">Contact Us</h2>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="contact-card contact-info-card">

                            @if($contact && $contact->address)
                                <div class="contact-detail">
                                    <div class="contact-icon-contact-us-page">
                                        <img src="{{ asset('frontend/assets/img/icon/contact-us-icon-1.webp') }}" alt="" data-aos="fade-right" data-aos-delay="150">
                                    </div>
                                    <div>
                                        <h4 data-aos="fade-left" data-aos-delay="150">Address</h4>
                                        <div data-aos="fade-left" data-aos-delay="150" class="contact-address-rich">
                                            @if($contact->map_url)
                                                <a href="{{ $contact->map_url }}" target="_blank">{!! $contact->address !!}</a>
                                            @else
                                                {!! $contact->address !!}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($contact && $contact->email)
                                <div class="contact-detail">
                                    <div class="contact-icon-contact-us-page">
                                        <img src="{{ asset('frontend/assets/img/icon/contact-us-icon-2.webp') }}" alt="" data-aos="fade-right" data-aos-delay="200">
                                    </div>
                                    <div>
                                        <h4 data-aos="fade-left" data-aos-delay="200">E-mail</h4>
                                        <a href="mailto:{{ $contact->email }}" data-aos="fade-left" data-aos-delay="200">{{ $contact->email }}</a>
                                    </div>
                                </div>
                            @endif

                            @if($contact && $contact->emergency_no)
                                <div class="contact-detail">
                                    <div class="contact-icon-contact-us-page">
                                        <img src="{{ asset('frontend/assets/img/icon/contact-us-icon-3.webp') }}" alt="" data-aos="fade-right" data-aos-delay="250">
                                    </div>
                                    <div>
                                        <h4 data-aos="fade-left" data-aos-delay="250">I Have An Emergency</h4>
                                        <p data-aos="fade-left" data-aos-delay="250">
                                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $contact->emergency_no) }}">{{ $contact->emergency_no }}</a>
                                        </p>
                                    </div>
                                </div>
                            @endif

                            @if($contact && $contact->join_team_email)
                                <div class="contact-detail border-0 pb-0 mb-0">
                                    <div class="contact-icon-contact-us-page">
                                        <img src="{{ asset('frontend/assets/img/icon/contact-us-icon-4.webp') }}" alt="" data-aos="fade-right" data-aos-delay="300">
                                    </div>
                                    <div>
                                        <h4 data-aos="fade-left" data-aos-delay="300">Join Our Team</h4>
                                        <p data-aos="fade-left" data-aos-delay="300"><a href="mailto:{{ $contact->join_team_email }}">{{ $contact->join_team_email }}</a></p>
                                    </div>
                                </div>
                            @endif


                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="contact-card contact-form-card">


                            <form id="contactForm" action="{{ route('frontend.contact_enquiry.store') }}" method="POST" novalidate>
                                @csrf
                                <div class="row g-4">

                                    <div class="col-md-12">
                                        <input type="text" name="full_name" id="full_name" class="form-control"
                                               placeholder="Your name*" maxlength="50">
                                        <small class="error-msg" id="err_full_name"></small>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="email" name="email" id="email" class="form-control"
                                               placeholder="Email Address*">
                                        <small class="error-msg" id="err_email"></small>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="tel" name="phone" id="phone" class="form-control"
                                               placeholder="Phone Number*" maxlength="12"
                                               inputmode="numeric" pattern="[0-9]{10,12}"
                                               oninput="this.value=this.value.replace(/\D/g,'').slice(0,12);">
                                        <small class="error-msg" id="err_phone"></small>
                                    </div>

                                    <div class="col-md-12">
                                        <input type="text" name="subject" id="subject" class="form-control"
                                               placeholder="Subject*">
                                        <small class="error-msg" id="err_subject"></small>
                                    </div>

                                    <div class="col-12">
                                        <textarea name="message" id="message" class="form-control" rows="6"
                                                  placeholder="Write a message (optional)"></textarea>
                                    </div>

                                    <!-- reCAPTCHA widget (disabled for now)
                                    <div class="col-12">
                                        <div class="g-recaptcha" data-sitekey="6LfIdQotAAAAAL68OYHeR6YqH18h7VHTr5jRg0OV"></div>
                                        <small class="error-msg" id="err_captcha"></small>
                                    </div>
                                    -->

                                    <div class="col-12">
                                        <p class="form-status" id="formStatus"></p>
                                        <button type="submit" class="btn" id="submitBtn">
                                            Submit Message
                                            <img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}" alt="" class="injectable">
                                        </button>
                                    </div>
                                </div>
                            </form>


                        </div>
                    </div>

                </div>
            </div>
        </section>

        @if($contact && $contact->iframe_url)
            <div class="contact-map">
                @if(str_starts_with(trim($contact->iframe_url), '<iframe'))
                    {!! $contact->iframe_url !!}
                @else
                    <iframe
                      src="{{ $contact->iframe_url }}"
                      width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                      referrerpolicy="no-referrer-when-downgrade" title="Location Map">
                    </iframe>
                @endif
            </div>
        @endif


    </main>
    <!-- main-area-end -->

    @include('components.frontend.footer')

    @include('components.frontend.main-js')

    <script>
    (function () {
        var form = document.getElementById('contactForm');
        if (!form) return;

        var submitBtn = document.getElementById('submitBtn');
        var originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

        function showError(id, msg) {
            var el = document.getElementById('err_' + id);
            var input = form.querySelector('[name="' + id + '"]');
            if (el)    el.textContent = msg;
            if (input) input.classList.add('is-invalid');
        }

        function clearError(id) {
            var el = document.getElementById('err_' + id);
            var input = form.querySelector('[name="' + id + '"]');
            if (el)    el.textContent = '';
            if (input) input.classList.remove('is-invalid');
        }

        // Live-clear on input.
        ['full_name', 'email', 'phone', 'subject', 'message'].forEach(function (id) {
            var input = form.querySelector('[name="' + id + '"]');
            if (!input) return;
            input.addEventListener('input', function () { clearError(id); });
        });

        form.addEventListener('submit', function (e) {
            // Reset first
            ['full_name', 'email', 'phone', 'subject', 'message'].forEach(clearError);

            var v = function (id) {
                var el = form.querySelector('[name="' + id + '"]');
                return el ? (el.value || '').trim() : '';
            };

            var errors = 0;

            // Name — required, no digits/special chars.
            var name = v('full_name');
            if (name === '') {
                showError('full_name', 'Please enter your name.'); errors++;
            } else if (!/^[A-Za-z\s.'\-]+$/.test(name)) {
                showError('full_name', 'Name cannot contain numbers or special characters.'); errors++;
            }

            // Email — required + format.
            var email = v('email');
            if (email === '') {
                showError('email', 'Please enter your email address.'); errors++;
            } else if (!/^[^\s@]+@[^\s@]+\.[A-Za-z]{2,}$/.test(email)) {
                showError('email', 'Please enter a valid email address.'); errors++;
            }

            // Phone — digits only, 10-12 characters (cap enforced by oninput).
            var phone = v('phone');
            if (phone === '') {
                showError('phone', 'Please enter your phone number.'); errors++;
            } else if (!/^\d{10,12}$/.test(phone)) {
                showError('phone', 'Phone number must be 10 to 12 digits.'); errors++;
            }

            // Subject — required.
            var subject = v('subject');
            if (subject === '') { showError('subject', 'Please enter a subject.'); errors++; }

            // Message is optional — no validation.

            if (errors > 0) {
                e.preventDefault();
                // Scroll to first error.
                var firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }

            // Passed — disable to prevent double-submit.
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Submitting…';
            }
            // Fallback: re-enable after 30s if the browser stalls somehow.
            setTimeout(function () {
                if (submitBtn && submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                }
            }, 30000);
        });
    })();
    </script>

  </body>
</html>
