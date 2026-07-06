
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
        <section class="breadcrumb our-team-breadcrumb">
            <div class="breadcrumb-img-custom-sec">
                @if($team_settings && !empty($team_settings->banner_image))
                    <img src="{{ asset('our-team/'.$team_settings->banner_image) }}" alt="{{ $team_settings->banner_heading ?? 'Our Team' }}">
                @else
                    <img src="{{ asset('frontend/assets/img/banner/our-team-new-custom-banner-img.webp') }}" alt="">
                @endif
            </div>
            <div class="container">
                @php
                    $teamHeading = $team_settings->banner_heading ?? 'Meet the People Behind The Care';
                    // Break the heading onto a second line after the third word.
                    $teamHeadingHtml = preg_replace('/^((?:\S+\s+){2}\S+)\s+/u', '$1<br>', e($teamHeading), 1);
                @endphp
                <h1 class="breadcrumb-title">{!! $teamHeadingHtml !!}</h1>
                <ul class="breadcrumb-nav">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><span class="separator"><i class="fas fa-angle-double-right"></i></span></li>
                    <li>Our Team</li>
                </ul>
            </div>
        </section>


        <!-- Team members -->
        <section class="team__area our-team-area-bg">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section__title section_title-two text-center mb-40">
                            <h2 class="title" data-aos="fade-up">{{ $team_settings->section_heading ?? 'OUR TEAM' }}</h2>
                        </div>
                        @if($team_settings && !empty($team_settings->section_description))
                            <div class="text-center" data-aos="fade-up" data-aos-delay="150">{!! $team_settings->section_description !!}</div>
                        @endif
                    </div>
                </div>
                <div class="row justify-content-center">
                    @forelse($team_members as $member)
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-8" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 4) * 150 }}">
                            <div class="team__item">
                                <div class="team__item-img">
                                    <div class="mask-img-wrap">
                                        <img src="{{ asset('our-team/'.$member->image) }}" alt="{{ $member->name }}">
                                    </div>
                                    <div class="team__item-img-shape">
                                    </div>
                                </div>
                                <div class="team__item-content">
                                    <h4 class="title">{{ $member->name }}</h4>
                                    @if(!empty($member->education))<p>{{ $member->education }}</p>@endif
                                    @if(!empty($member->designation))<p>{{ $member->designation }}</p>@endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center"><p>No team members added yet.</p></div>
                    @endforelse
                </div>
            </div>
        </section>


        <!-- Motto / group image -->
        <section class="our-team-all-img-sec">
            <div class="team-image">

                @if($team_settings && !empty($team_settings->motto_image))
                    <img src="{{ asset('our-team/'.$team_settings->motto_image) }}" alt="Our Team">
                @else
                    <img src="{{ asset('frontend/assets/img/bg/group.jpg') }}" alt="Our Team">
                @endif

                <div class="team-content-our-team-all-img">
                    <span class="team-tagline" data-aos="fade-up" data-aos-delay="150">{{ $team_settings->motto ?? 'Care. Cure. Comfort.' }}</span>

                    @if($team_settings && !empty($team_settings->motto_description))
                        <p data-aos="fade-up" data-aos-delay="200">{!! nl2br(e($team_settings->motto_description)) !!}</p>
                    @endif
                </div>

            </div>
        </section>


        <!-- Our Board -->
        <section class="our-team-our-board-section position-relative overflow-hidden" id="our-team-our-board-sec">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 text-center position-relative">
                        <div class="our-team-our-board-img-stack">
                            @if($team_settings && !empty($team_settings->board_image))
                                <img src="{{ asset('our-team/'.$team_settings->board_image) }}" class="img-fluid our-team-our-board-main-img"
                                    alt="{{ $team_settings->board_name ?? 'Board' }}" data-aos="fade-right" data-aos-delay="150">
                            @else
                                <img src="{{ asset('frontend/assets/img/images/sir-ratan-tata-img.webp') }}" class="img-fluid our-team-our-board-main-img"
                                    alt="Ratan Tata" data-aos="fade-right" data-aos-delay="150">
                            @endif
                            <div class="image-stack-content-sec">
                                <h4>{{ $team_settings->board_name ?? 'Late Mr Ratan N. Tata' }}</h4>
                                <p>{{ $team_settings->board_designation ?? 'Founder and Chairman Emeritus' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="section__title">
                            <h2 class="title" data-aos="fade-left" data-aos-delay="150">{{ $team_settings->board_heading ?? 'Our Board' }}</h2>
                            @if($team_settings && !empty($team_settings->board_small_desc))
                                <p data-aos="fade-left" data-aos-delay="150">{!! nl2br(e($team_settings->board_small_desc)) !!}</p>
                            @endif
                        </div>

                        <div class="our-team-our-board-custom-card">
                            <div class="row g-3">
                                @php
                                    $boardMembers = ($team_settings && !empty($team_settings->board_members))
                                        ? $team_settings->board_members
                                        : ['Mr. Siddharth Sharma', 'Ms. Leah Tata1', 'Mr. Mehernosh Kapadia', 'Mr. Sanjay Ubale', 'Dr. Sheila Mukundan'];
                                @endphp
                                @foreach($boardMembers as $i => $bm)
                                    <div class="col-lg-6 col-md-6">
                                        <div class="ou-tou-bc-us-card" data-aos="fade-left" data-aos-delay="{{ 150 + ($i * 50) }}">
                                            <div class="ou-tou-bc-us-img-sec">
                                                <img src="{{ asset('frontend/assets/img/icon/board-of-directors-icon.webp') }}" alt="">
                                            </div>
                                            <div class="ou-tou-bc-us-content-sec">
                                                <p>{{ $bm }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
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
