    @php
        $headerContact = \App\Models\ContactDetails::whereNull('deleted_by')
            ->with([
                'ribbonItems' => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
                'socialLinks' => fn ($q) => $q->whereNull('deleted_by')->orderBy('sort_order')->orderBy('id'),
            ])
            ->first();

        $headerSocials = $headerContact ? $headerContact->socialLinks : collect();
        $headerRibbons = $headerContact ? $headerContact->ribbonItems : collect();

        // Split ribbon items evenly between the two top-strip columns.
        $ribbonHalf    = (int) ceil($headerRibbons->count() / 2);
        $leftRibbons   = $headerRibbons->slice(0, $ribbonHalf);
        $rightRibbons  = $headerRibbons->slice($ribbonHalf);

        // Phone display + tel:link helpers used in the mobile menu / offcanvas.
        $phoneDisplay = $headerContact->emergency_no ?? '';
        $phoneTel     = $phoneDisplay ? preg_replace('/[^\d+]/', '', $phoneDisplay) : '';

        // Header/menu blocks want short plain-text address (rich-text stripped).
        $addressPlain = $headerContact && $headerContact->address
            ? trim(preg_replace('/\s+/', ' ', strip_tags($headerContact->address)))
            : '';

        // Smart-link helper for ribbon values: URL, email, phone, or plain text.
        $ribbonHref = function ($value) {
            $v = trim((string) $value);
            if ($v === '') { return null; }
            if (str_starts_with($v, 'http')) { return $v; }
            if (str_contains($v, '@'))       { return 'mailto:'.$v; }
            if (preg_match('/^\+?[\d\s\-()]+$/', $v)) {
                return 'tel:'.preg_replace('/[^\d+]/', '', $v);
            }
            return null;
        };
    @endphp

     <!-- PRELOADER -->
    <div id="preloader">
        <div class="preloader-icon-wrap">
            <div class="preloader-icon-stack">
                <lottie-player class="lottie-preloader-player-sec" src="{{ asset('frontend/assets/preloader-logo.json') }}"
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
    
    <div class="floating-social-menu">
        <button class="social-toggle-btn">
            <i class="fas fa-headset"></i>
        </button>
        <div class="social-icons">
            @forelse($headerSocials as $link)
                <a target="_blank" href="{{ $link->url }}" class="social-icon {{ $link->platform }}" title="{{ $link->platform_label }}">
                    <i class="{{ $link->icon_class }}"></i>
                </a>
            @empty
                <a target="_blank" href="https://www.instagram.com/sahmumbai/" class="social-icon instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a target="_blank" href="https://wa.me/917021850400" class="social-icon whatsapp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a target="_blank" href="#" class="social-icon linkedin">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            @endforelse
        </div>
    </div>
 
 
 
 <!-- header-area -->
    <header>
        <div id="header-fixed-height"></div>

        @if($headerRibbons->count())
            <div class="tg-header__top">
                <div class="container custom-container">
                    <div class="row">
                        <div class="col-xl-6 col-lg-8 col-md-6">
                            <ul class="tg-header__top-info left-side list-wrap">
                                @foreach($leftRibbons as $r)
                                    @php $href = $ribbonHref($r->value); @endphp
                                    <li>
                                        @if($r->icon)
                                            <img src="{{ asset('home/contact/ribbon/'.$r->icon) }}" alt="{{ $r->title }}">
                                        @endif
                                        @if($href)
                                            <a href="{{ $href }}">{{ $r->title }}@if($r->value)  {{ $r->value }}@endif</a>
                                        @else
                                            {{ $r->title }}@if($r->value)  {{ $r->value }}@endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-xl-6 col-lg-4 col-md-6">
                            <ul class="tg-header__top-right list-wrap">
                                @foreach($rightRibbons as $r)
                                    @php $href = $ribbonHref($r->value); @endphp
                                    <li>
                                        @if($r->icon)
                                            <img src="{{ asset('home/contact/ribbon/'.$r->icon) }}" alt="{{ $r->title }}">
                                        @endif
                                        @if($href)
                                            <a href="{{ $href }}">{{ $r->title }}@if($r->value)  {{ $r->value }}@endif</a>
                                        @else
                                            {{ $r->title }}@if($r->value)  {{ $r->value }}@endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <div id="sticky-header" class="tg-header__area">
            <div class="container custom-container">
            <!--<div class="container">-->
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
                                        <li><a href="{{ route('frontend.specialities') }}">Specialities</a></li>
                                        <li><a href="{{ route('frontend.our_facilities') }}">Facilities</a></li>
                                        <li><a href="{{ route('frontend.about_us') }}">About</a></li>
                                        <li><a href="{{ route('frontend.our_team') }}">Team</a></li>
                                        <li><a href="{{ route('frontend.events') }}">Events</a></li>
                                        <li><a href="{{ route('frontend.blogs') }}">Blog</a></li> 
                                        <li><a href="{{ route('frontend.coming_soon') }}">Media</a></li>
                                        <li><a href="{{ route('frontend.contact_us') }}">Contact</a></li>
                                       
                                    </ul>
                                </div>
                                <div class="tgmenu__action d-none d-md-flex">
                                    <div class="emergency-menu-button-custom-sec">
                                        <a href="{{ $phoneTel ? 'tel:'.$phoneTel : '#' }}"
                                           @if($phoneDisplay) title="Emergency: {{ $phoneDisplay }}" aria-label="Call Emergency: {{ $phoneDisplay }}" @endif>
                                            <img src="{{ asset('frontend/assets/img/icon/call-icon-one.webp') }}" alt="">
                                        </a>
                                    </div>
                                    <ul class="list-wrap">
                                        <li class="offCanvas-menu">
                                            <a href="javascript:void(0)" class="menu-tigger">
                                                <svg id="Layer_1" enable-background="new 0 0 24 24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g><path d="m20 17.8h-16c-.4 0-.8-.3-.8-.8s.3-.8.8-.8h16c.4 0 .8.3.8.8s-.4.8-.8.8zm0-5h-16c-.4 0-.8-.3-.8-.8s.3-.8.8-.8h16c.4 0 .8.3.8.8s-.4.8-.8.8zm0-5h-16c-.4 0-.7-.4-.7-.8s.3-.7.7-.7h16c.4 0 .8.3.8.8s-.4.7-.8.7z"/></g></svg>
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
                                            src="{{ asset('frontend/assets/img/logo/tata-trust-logo.webp') }}"
                                            alt="Tata Trusts Small Animal Hospital Logo"></a>
                                </div>
                                <div class="tgmobile__menu-outer">
                                </div>

                                <div class="tg-mobile-custom-book-appoint-sec">
                                    @foreach($headerRibbons as $r)
                                        @php $href = $ribbonHref($r->value); @endphp
                                        <div class="address-item">
                                            @if($r->icon)
                                                <div class="icon">
                                                    <img src="{{ asset('home/contact/ribbon/'.$r->icon) }}" alt="{{ $r->title }}">
                                                </div>
                                            @endif
                                            <div class="address-content-sec">
                                                <h4>{{ $r->title }}</h4>
                                                @if($r->value)
                                                    <p>
                                                        @if($href)
                                                            <a href="{{ $href }}">{{ $r->value }}</a>
                                                        @else
                                                            {{ $r->value }}
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="social-links">
                                    <ul class="list-wrap">
                                        @forelse($headerSocials as $link)
                                            <li><a href="{{ $link->url }}" target="_blank" title="{{ $link->platform_label }}"><i class="{{ $link->icon_class }}"></i></a></li>
                                        @empty
                                            <li><a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                                            <li><a href="#" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
                                            <li><a href="#" target="_blank"><i class="fab fa-instagram"></i></a></li>
                                        @endforelse
                                    </ul>
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
                <a href="{{ route('frontend.index') }}"><img src="{{ asset('frontend/assets/img/logo/tata-trust-logo.webp') }}"
                        alt="Tata Trusts Small Animal Hospital Logo"></a>
            </div>
            <div class="offCanvas__side-info mb-30">
                @if($addressPlain)
                    <div class="contact-list d-flex align-items-start mb-30">
                        <img src="{{ asset('frontend/assets/img/icon/side-menu-address.webp') }}" alt="Address icon" class="contact-icon">
                        <div>
                            <h4>Address</h4>
                            <p>
                                @if($headerContact->map_url)
                                    <a href="{{ $headerContact->map_url }}" target="_blank">{{ $addressPlain }}</a>
                                @else
                                    {{ $addressPlain }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endif

                @if($phoneDisplay)
                    <div class="contact-list d-flex align-items-start mb-30">
                        <img src="{{ asset('frontend/assets/img/icon/side-menu-phone.webp') }}" alt=" Phone Number icon" class="contact-icon">
                        <div>
                            <h4>Phone Number</h4>
                            <p><a href="tel:{{ $phoneTel }}">{{ $phoneDisplay }}</a></p>
                        </div>
                    </div>
                @endif

                @if($headerContact && $headerContact->email)
                    <div class="contact-list d-flex align-items-start mb-30">
                        <img src="{{ asset('frontend/assets/img/icon/side-menu-email.webp') }}" alt="Email icon" class="contact-icon">
                        <div>
                            <h4>Email Address</h4>
                            <p><a href="mailto:{{ $headerContact->email }}">{{ $headerContact->email }}</a></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="offCanvas__overly"></div>
        <!-- offCanvas-menu-end -->

    </header>
    <!-- header-area-end -->
