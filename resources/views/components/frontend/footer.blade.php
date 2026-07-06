    <section class="home-page-contact-us-footer-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="why__we-are-content">
                        <div class="section__title section_title_none mb-0">
                            <h2 class="title">Appointment & Emergency help is available 24x7</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="home-appointment-emergency-footer-btn-sec">
                        <a href="tel:02265383538" class="btn">Contact Us<img src="{{ asset('frontend/assets/img/icon/right_arrow.svg') }}"
                                alt="" class="injectable"></a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    
    
    <!-- footer-area -->
    <footer>
        <div class="footer__area">
            <div class="footer__top fix">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-lg-4 col-md-6">
                            <div class="footer__widget">
                                <div class="footer__logo">
                                    <a href="{{ route('frontend.index') }}"><img
                                            src="{{ asset('frontend/assets/img/logo/sahmumbai-logo.svg') }}" width="325" height="65" alt="Tata Trusts Small Animal Hospital Logo" loading="lazy"></a>
                                    <p><a href="https://maps.app.goo.gl/FYcr3wnZnz6PLKmm6" target="_blank">
                                        Tata Trusts Small Animal Hospital,<br>
                                        G.B. Sakpal Marg, Saat Rasta,<br>
                                        Mahalaxmi, Mumbai 400011</a></p>
                                    <div class="footer-btn-sah-custom-sec">
                                        <a href="https://maps.app.goo.gl/FYcr3wnZnz6PLKmm6" target="_blank" class="read-more-btn"><img src="{{ asset('frontend/assets/img/icon/map-icon-one.webp') }}" width="18" height="18" alt="View Map Icon" loading="lazy" class="view-map-img-one"> View Map</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
                            <div class="footer__widget">
                                <h4 class="footer__widget-title">Contact Us</h4>
                                <div class="footer__link">
                                    <ul class="address">
                                        <li>
                                            <div class="icon">
                                                <img src="{{ asset('frontend/assets/img/icon/telephone-icon.webp') }}" width="40" height="40" alt="Book An Appointment Icon" loading="lazy">
                                            </div>
                                            <div class="address-content-sec">
                                                <h4>Book An Appointment</h4>
                                                <p><a href="tel:02265383538">022-6538-3538</a></p>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="icon">
                                                <img src="{{ asset('frontend/assets/img/icon/email-icon.webp') }}" width="40" height="40" alt="Mail Us Icon" loading="lazy">
                                            </div>
                                            <div class="address-content-sec">
                                                <h4>Mail Us</h4>
                                                <p><a href="mailto:contactus@sahmumbai.com">contactus@sahmumbai.com</a>
                                                </p>
                                            </div>
                                        </li>
                                        
                                        <li>
                                            <div class="icon">
                                                <img src="{{ asset('frontend/assets/img/icon/donate-floating-icon.webp') }}" width="40" height="40" alt="Donate Icon" loading="lazy">
                                            </div>
                                            <div class="address-content-sec">
                                                <h4><a href="mailto:frontoffice@sahmumbai.com?subject=Interest%20in%20Donation%20/%20Enquiry"
                                                target="_blank">Donate</a></h4>
                                                <!--<p><a href="mailto:contactus@sahmumbai.com">contactus@sahmumbai.com</a></p>-->
                                            </div>
                                        </li>
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 quick-links-pad-sec">
                            <div class="footer__widget ms-0">
                                <h4 class="footer__widget-title">Quick Links</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="footer__link">
                                            <ul class="list-wrap">
                                                <li><a href="{{ route('frontend.index') }}">Home</a></li>
                                                <li><a href="about-us.html">About</a></li>
                                                <li><a href="specialities.html">Specialities</a></li>
                                                <li><a href="{{ route('frontend.our_facilities') }}">Facilities</a></li>
                                                <li><a href="{{ route('frontend.faqs') }}">FAQs</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="footer__link">
                                            <ul class="list-wrap">
                                                <li><a href="{{ route('frontend.our_team') }}">Team</a></li>
                                                <li><a href="join-us.html">Join Us</a></li>
                                                <li><a href="{{ route('frontend.gallery') }}">Gallery</a></li>
                                                <li><a href="blog.html">Blog</a></li>
                                                <li><a href="contact-us.html">Contact Us</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer__bottom">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="copyright-text">
                                <p>Copyright © 2026 Small Animal Hospital. All Rights Reserved. Designed By <a
                                        href="https://www.matrixbricks.com/" target="_blank">Matrix Bricks.</a></p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="footer__bottom-menu text-end">
                                <p><a href="{{ asset('frontend/assets/pdf/privacy-policy.pdf' )}}" target="_blank">Privacy Policy</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <div class="modal fade book-an-appointment-custom-popup-form-sec" id="appointmentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content custom-modal">
                <div class="modal-header custom-header">
                    <h5 class="modal-title">Book Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body custom-body">
                    <form class="appointment-form">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Your Name">
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" placeholder="Email Address">
                        </div>
                        <div class="form-group">
                            <input type="tel" class="form-control" placeholder="Phone Number">
                        </div>
                        <div class="form-group">
                            <textarea class="form-control" rows="3" placeholder="Your Message"></textarea>
                        </div>
                        <div class="bapcpop-btn-sec">
                            <button type="submit" class="btn submit-btn">Request a Call Back</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- footer-area-end -->


    <!--Start Sticky Icon-->
    <div class="sticky-icon">
        <a href="login.html"> Book An Appointment <img src="{{ asset('frontend/assets/img/icon/appointment-floating-icon.webp') }}" alt="Book An Appointment Icon"></a>
        <a href="mailto:frontoffice@sahmumbai.com?subject=Interest%20in%20Donation%20/%20Enquiry"> Donate <img src="{{ asset('frontend/assets/img/icon/donate-floating-icon.webp') }}" alt="Donate Icon"></a>
        <a href="contact-us.html"> Contact Us <img src="{{ asset('frontend/assets/img/icon/contact-us-floating-icon.webp') }}" alt="Contact Us Icon"></a>
    </div>
    <!--End Sticky Icon-->