        <!-- PRELOADER -->
    <div id="preloader">
        <div class="preloader-icon-wrap">
            <div class="preloader-icon-stack">
                <lottie-player class="lottie-preloader-player-sec" src="{{ asset('frontend/assets/preloader-logo.json' ) }}"
                    background="transparent" speed="1" loop autoplay>
                </lottie-player>
            </div>
        </div>
        <div class="preloader-counter">
            <span id="counter-current">0</span>
            <span class="separator">/</span>
            <span>100</span>
        </div>
    </div>

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <!-- Scroll-top-end-->
    
    <div class="floating-social-menu">
        <button class="social-toggle-btn">
            <i class="fas fa-headset"></i>
        </button>
        <div class="social-icons">
            <a target="_blank" href="https://www.instagram.com/sahmumbai/" class="social-icon instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a target="_blank" href="#" class="social-icon whatsapp">
                <i class="fab fa-whatsapp"></i>
            </a>
            <a target="_blank" href="#" class="social-icon linkedin">
                <i class="fab fa-linkedin-in"></i>
            </a>
        </div>
    </div>
    
    
    <!-- header-area -->
    <header>
        <div id="header-fixed-height"></div>
        <div class="sah-header-top-new-ask">
            <div class="marquee-text">
                <span>
                    <img src="{{ asset('frontend/assets/img/icon/appointment.png') }}" alt="Book An Appointment Icon">Book An Appointment: <a
                        href="tel:02265383538">022-6538-3538</a> &nbsp;&nbsp;&nbsp; |
                    &nbsp;&nbsp;&nbsp; <img src="{{ asset('frontend/assets/img/icon/timing-icon.webp') }}" alt="Timing Icon">
                    Timing 24 x 7
                </span>
            </div>
        </div>

        <div id="sticky-header" class="tg-header__area">
            <!--<div class="container custom-container">-->
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="tgmenu__wrap">
                            <nav class="tgmenu__nav">
                                <div class="logo">
                                    <a href="{{ route('frontend.index') }}"><img
                                            src="{{ asset('frontend/assets/img/logo/tata-trust-logo.webp') }}" alt="Tata Trusts Small Animal Hospital Logo"></a>
                                </div>
                                <div class="tgmenu__navbar-wrap tgmenu__main-menu d-none d-lg-flex">
                                    <ul class="navigation">
                                        <!--<li class="active"><a href="#">Home</a></li>-->
                                        <li><a href="about-us.html">About Us</a></li>
                                        <li><a href="our-specialities.html">Specialities</a></li>
                                        <li><a href="our-facilities.html">Facilities</a></li>
                                        <!-- <li><a href="#">Faqs</a></li> -->
                                         <li><a href="our-team.html">Team</a></li> 
                                        <!-- <li><a href="#">Join Us</a></li> -->
                                         <li><a href="#">Blogs</a></li> 
                                        <li><a href="contact-us.html">Contact</a></li>
                                    </ul>
                                </div>
                                <div class="tgmenu__action d-none d-md-block">
                                    <ul class="list-wrap">
                                        <li class="offCanvas-menu">
                                            <a href="javascript:void(0)" class="menu-tigger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="16"
                                                    viewBox="0 0 26 16" fill="none">
                                                    <rect width="9" height="2" rx="1" fill="currentcolor" />
                                                    <rect x="11" width="15" height="2" rx="1" fill="currentcolor" />
                                                    <rect y="14" width="26" height="2" rx="1" fill="currentcolor" />
                                                    <rect y="7" width="16" height="2" rx="1" fill="currentcolor" />
                                                    <rect x="17" y="7" width="9" height="2" rx="1"
                                                        fill="currentcolor" />
                                                </svg>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="mobile-nav-toggler">
                                    <i class="flaticon-layout"></i>
                                </div>
                            </nav>
                        </div>

                        <!-- Mobile Menu  -->
                        <div class="tgmobile__menu">
                            <nav class="tgmobile__menu-box">
                                <div class="close-btn"><i class="fas fa-times"></i></div>
                                <div class="nav-logo">
                                    <a href="{{ route('frontend.index') }}"><img
                                            src="{{ asset('frontend/assets/img/logo/tata-trust-logo.webp') }}" alt="Tata Trusts Small Animal Hospital Logo"></a>
                                </div>
                                <div class="tgmobile__menu-outer">
                                </div>
                            </nav>
                        </div>
                        <div class="tgmobile__menu-backdrop"></div>
                        <!-- End Mobile Menu -->

                    </div>
                </div>
            </div>
        </div>

        <!-- header-search -->
        <div class="search__popup">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="search__wrapper">
                            <div class="search__close">
                                <button type="button" class="search-close-btn">
                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M17 1L1 17" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path d="M1 1L17 17" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="search__form">
                                <form action="#">
                                    <div class="search__input">
                                        <input class="search-input-field" type="text" placeholder="Type keywords here">
                                        <span class="search-focus-border"></span>
                                        <button>
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M9.55 18.1C14.272 18.1 18.1 14.272 18.1 9.55C18.1 4.82797 14.272 1 9.55 1C4.82797 1 1 4.82797 1 9.55C1 14.272 4.82797 18.1 9.55 18.1Z"
                                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path d="M19.0002 19.0002L17.2002 17.2002" stroke="currentcolor"
                                                    stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="search-popup-overlay"></div>
        <!-- header-search-end -->

        <!-- offCanvas-menu -->
        <div class="offCanvas__info">
            <div class="offCanvas__close-icon menu-close">
                <button><i class="far fa-window-close"></i></button>
            </div>
            <div class="offCanvas__logo mb-20">
                <a href="{{ route('frontend.index') }}"><img src="{{ asset('frontend/assets/assets/img/logo/tata-trust-logo.webp' ) }}"
                        alt="Tata Trusts Small Animal Hospital Logo"></a>
            </div>
            <div class="offCanvas__side-info mb-30">
                <div class="contact-list d-flex align-items-start mb-30">
                    <img src="{{ asset('frontend/assets/img/icon/side-menu-address.webp') }}" alt="Address icon" class="contact-icon">
                    <div>
                        <h4>Address</h4>
                        <p><a href="https://maps.app.goo.gl/FYcr3wnZnz6PLKmm6" target="_blank">Tata Trusts Small Animal
                                Hospital, G.Babu Sakpal Marg, Saat Rasta, Mahalaxmi, Mumbai 400011,
                                Landmark: Opposite Omkar Realty, Behind Dhobi Ghat.</a></p>
                    </div>
                </div>

                <div class="contact-list d-flex align-items-start mb-30">
                    <img src="{{ asset('frontend/assets/img/icon/side-menu-phone.webp' ) }}" alt=" Phone Number icon" class="contact-icon">
                    <div>
                        <h4>Phone Number</h4>
                        <p><a href="tel:02265383538">022-6538-3538</a></p>
                    </div>
                </div>

                <div class="contact-list d-flex align-items-start mb-30">
                    <img src="{{ asset('frontend/assets/img/icon/side-menu-email.webp') }}" alt="Email icon" class="contact-icon">
                    <div>
                        <h4>Email Address</h4>
                        <p><a href="mailto:contactus@sahmumbai.com">contactus@sahmumbai.com</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="offCanvas__overly"></div>
        <!-- offCanvas-menu-end -->

    </header>
    <!-- header-area-end -->