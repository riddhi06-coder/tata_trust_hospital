
      <!-- Floating Buttons -->
      <div class="floating-icons floating-icons-inner">
          <a type="button" data-toggle="modal" data-target="#bookappointment-services">
          <img src="{{ asset('frontend/assets/img/icon/book-appointment-new.svg') }}">
          <span class="tooltip-text">Book Appointment </span>
          </a>
          <a href="#find-doctor">
          <img src="{{ asset('frontend/assets/img/icon/find-doctor-new.svg') }}">
          <span class="tooltip-text">Find A Doctor </span>
          </a>
          <a type="button" data-toggle="modal" data-target="#health-checkup">
          <img src="{{ asset('frontend/assets/img/icon/book-health-check-new.svg') }}">
          <span class="tooltip-text">Book Heath Check </span>
          </a>
      </div>


      <!-- header start -->
      <div class="full_header inner-page-header" id="header-sticky">
        <header class="header">
          <div class="container-fluid">

            <div class="row v-center main-header">
              <div class="header-item item-left">
               
                <div class="logo logo-custom-flex-sec">
                  <a href="{{ route('frontend.index') }}">
                    <img src="{{ asset('frontend/assets/img/logo/kj-somaiya-logo.png')}}" class="img-responsive"><!-- <img src="img/logo/somaiya-trust-logo.png" class="img-responsive lcfsec-custom-sec"> -->
                  </a>
                </div>
              </div>

              <!-- menu start here -->
              <div class="header-item item-center">
                <div class="menu-overlay"></div>
                <nav class="menu">
                  <div class="mobile-menu-head">
                    <div class="go-back"><i class="fa fa-angle-left"></i></div>
                    <div class="current-menu-title"></div>
                    <div class="mobile-menu-close">&times;</div>
                  </div>
                  <ul class="menu-main">
                    <li class="menu-item-has-children">
                      <a href="#">About Us <i class="fa fa-angle-down"></i></a>
                      <div class="sub-menu single-column-menu">
                        <ul>
                          <li><a href="{{ route('frontend.introduction') }}">Introduction</a></li>
                          <li><a href="{{ route('frontend.vision_and_mision') }}">Vision & Mission</a></li>
                          <li><a href="{{ route('frontend.chairmans_message') }}">Chairman’s Message</a></li>
                          <li><a href="{{ route('frontend.associations') }}">Associations</a></li>
                          <!-- <li><a href="#">Our Journey</a></li> -->
                          <li><a href="{{ route('frontend.somaiya_prayer') }}">Somaiya Prayer</a></li>
                          <!-- 
                            <li><a href="#">Associations</a></li> -->
                          <li><a href="{{ route('frontend.management_team') }}">Management Team</a></li>
                          <li><a href="{{ route('frontend.csr_sustainability') }}">CSR & Sustainability</a></li>
                          <li><a href="{{ route('frontend.accreditations') }}">Accreditations</a></li>
                          <li><a href="{{ route('frontend.community_outreach') }}">Community Outreach</a></li>
                          <li><a href="{{ route('frontend.awards_accolades') }}">Awards & Accolades</a></li>
                        </ul>
                      </div>
                    </li>


                    <li class="menu-item-has-children">
                      <a href="#">Medical Services <i class="fa fa-angle-down"></i></a>
                      <div class="sub-menu mega-menu mega-menu-column-4">
                      
                        <div class="list-item">
                            @php
                                use Illuminate\Support\Facades\DB;

                                // Fetch all master categories with subcategories and services
                                $masters = DB::table('medical_service_master_categories as m')
                                    ->select(
                                        'm.id as master_id',
                                        'm.category_name as master_name',
                                        'm.slug as master_slug',
                                        's.id as sub_id',
                                        's.subcategory_name as sub_name',
                                        's.slug as sub_slug',
                                        'c.id as service_id',
                                        'c.service_name as service_name',
                                        'c.slug as service_slug'
                                    )
                                    ->leftJoin('medical_service_sub_categories as s', 's.category_id', '=', 'm.id')
                                    ->leftJoin('medical_service_categorie as c', function($join){
                                        $join->on('c.category_id', '=', 's.category_id')
                                            ->on('c.subcategory_id', '=', 's.id');
                                    })
                                    ->orderBy('m.id')
                                    ->orderBy('s.id')
                                    ->orderBy('c.id')
                                    ->get()
                                    ->groupBy('master_id'); // Group by master category
                            @endphp

                            <div class="row mega-menu-container">

                                <!-- LEFT VERTICAL TABS (DESKTOP) -->
                                <div class="col-sm-4 hidden-xs">
                                    <ul class="nav nav-pills nav-stacked mega-vertical-tabs">
                                        @foreach($masters as $masterId => $masterItems)
                                            <li class="{{ $loop->first ? 'active' : '' }}">
                                                <a href="#v{{ $loop->index + 1 }}" data-toggle="tab">
                                                    {{ $masterItems->first()->master_name }} <i class="fa fa-angle-right"></i>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <!-- RIGHT CONTENT (DESKTOP) -->
                                <div class="col-sm-8 hidden-xs">
                                    <div class="tab-content">
                                        @foreach($masters as $masterId => $masterItems)
                                            <div class="tab-pane fade {{ $loop->first ? 'in active' : '' }}" id="v{{ $loop->index + 1 }}">
                                                <div class="tab-box">
                                                    <div class="row">
                                                        @if($loop->first)

                                                          @php
                                                              $subs = $masterItems->groupBy('sub_id')->values();
                                                              $half = ceil($subs->count() / 2);
                                                          @endphp

                                                          <div class="col-md-6">
                                                              <ul class="menu_tab_list">
                                                                  @foreach($subs->slice(0,$half) as $subItems)
                                                                      <li>
                                                                          <a href="{{ route('frontend.service_details', $subItems->first()->sub_slug) }}">
                                                                              {{ $subItems->first()->sub_name }}
                                                                          </a>
                                                                      </li>
                                                                  @endforeach
                                                              </ul>
                                                          </div>

                                                          <div class="col-md-6">
                                                              <ul class="menu_tab_list">
                                                                  @foreach($subs->slice($half) as $subItems)
                                                                      <li>
                                                                          <a href="{{ route('frontend.service_details', $subItems->first()->sub_slug) }}">
                                                                              {{ $subItems->first()->sub_name }}
                                                                          </a>
                                                                      </li>
                                                                  @endforeach
                                                              </ul>
                                                          </div>

                                                          @else

                                               
                                                            <!-- Other master categories: keep subcategory headings -->
                                                            @foreach($masterItems->groupBy('sub_id') as $subId => $subItems)
                                                                <div class="col-md-6">
                                                                    <ul class="menu_tab_list">
                                                                        @if($subItems->first()->sub_name)
                                                                            <li class="heading_bullet">
                                                                                <a href="#">
                                                                                    <h5>{{ $subItems->first()->sub_name }}</h5>
                                                                                </a>
                                                                            </li>
                                                                        @endif
                                                                        @foreach($subItems as $service)
                                                                            @if($service->service_id)
                                                                                <li>
                                                                                    <a href="{{ route('frontend.diagnostic_details', $service->service_slug) }}">
                                                                                        {{ $service->service_name }}
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- MOBILE ACCORDION -->
                                <div class="visible-xs">
                                    <div class="panel-group" id="mobileAccordion">
                                        @foreach($masters as $masterId => $masterItems)
                                            <div class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <a data-toggle="collapse" data-parent="#mobileAccordion" href="#m{{ $loop->index + 1 }}">
                                                            {{ $masterItems->first()->master_name }} <i class="fa fa-chevron-down"></i>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="m{{ $loop->index + 1 }}" class="panel-collapse collapse {{ $loop->first ? 'in' : '' }}">
                                                    <div class="panel-body">
                                                        <div class="row">
                                                            @if($loop->first)
                                                                <div class="col-md-6">
                                                                    <ul class="menu_tab_list">
                                                                        @foreach($masterItems->groupBy('sub_id') as $subId => $subItems)
                                                                            <li>
                                                                                <a href="{{ route('frontend.service_details', $subItems->first()->sub_slug) }}">
                                                                                    {{ $subItems->first()->sub_name }}
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @else
                                                                @foreach($masterItems->groupBy('sub_id') as $subId => $subItems)
                                                                    <div class="col-md-6">
                                                                        <ul class="menu_tab_list">
                                                                            @if($subItems->first()->sub_name)
                                                                                <li class="heading_bullet">
                                                                                    <a href="#">
                                                                                        <h5>{{ $subItems->first()->sub_name }}</h5>
                                                                                    </a>
                                                                                </li>
                                                                            @endif
                                                                            @foreach($subItems as $service)
                                                                                @if($service->service_id)
                                                                                    <li>
                                                                                        <a href="{{ route('frontend.diagnostic_details', $service->service_slug) }}">
                                                                                            {{ $service->service_name }}
                                                                                        </a>
                                                                                    </li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>

                      </div>
                    </li>



                    <li class="menu-item-has-children">
                      <a href="#">Patient Services <i class="fa fa-angle-down"></i></a>
                      <div class="sub-menu single-column-menu">
                        <ul>
                          <li><a href="{{ route('frontend.inpatient_services') }}">Inpatient Services</a></li>
                          <li><a href="{{ route('frontend.visitor_guide') }}">Visitor Guide</a></li>
                          <li><a href="{{ route('frontend.rights_and_responsibilities') }}">Rights & Responsibilities</a></li>
                          <li><a href="{{ route('frontend.convenience_and_facilities') }}">Convenience & Facilities</a></li>
                          <!--<li><a href="#">Maps & Directions</a></li>-->
                          <!-- <li><a href="#">Nearby Lodging Facilities</a></li> -->
                          <!--<li><a href="#">Contact Directory</a></li>-->
                          <!-- <li><a href="#">Payment Method</a></li> -->
                          <li><a href="{{ route('frontend.insurance_and_tpa') }}">Insurance & TPA</a></li>
                          <li><a href="{{ route('frontend.government_schemes') }}">Government Schemes</a></li>
                          <li><a href="{{ route('frontend.billing_process') }}">Billing Process</a></li>
                          <!-- <li><a href="#">Patients Safety</a></li> -->
                          <!-- <li><a href="#">Infection Control</a></li> -->
                          <!-- <li><a href="#">Biomedical Waste</a></li> -->
                          <!-- <li><a href="#">FAQ’s</a></li> -->
                        </ul>
                      </div>
                    </li>
                    <li class="menu-item-has-children">
                      <a href="#"> Wellness Centre <i class="fa fa-angle-down"></i></a>
                      <div class="sub-menu single-column-menu">
                        <ul>
                          <li><a href="{{ route('frontend.health_packages') }}">Health Packages</a></li>
                          <li><a href="{{ route('frontend.ayurveda') }}">Ayurveda</a></li>
                          <!-- <li><a href="#">Acupressure and Acupuncture</a></li> -->
                          <!-- <li><a href="#">Yoga</a></li> -->
                          <!-- <li><a href="#">Physiotherapy</a></li> -->
                          <li>
                            <a href="{{ route('frontend.alternative_therapies') }}">
                              <!-- Other --> Alternative Therapies
                            </a>
                          </li>
                        </ul>
                      </div>
                    </li>
                
                  </ul>
                </nav>
              </div>
              <!-- menu end here -->

              <div class="header-item item-right">
                <ul class="somaiya_sidelogo">
                  <li><img src="{{ asset('frontend/assets/img/logo/NABH-logo.png')}}"></li>
                  <li><img src="{{ asset('frontend/assets/img/logo/nabl.png')}}"></li>
                  <li><img src="{{ asset('frontend/assets/img/logo/somaiya-trust-logo.png')}}"></li>
                </ul>
                
                <!-- Hidden default Google widget -->
                <div id="google_translate_element"></div>
                <!-- Custom dropdown (Indian languages first, full list) -->
                <select id="customTranslate" aria-label="Select language">
                  <option value="">EN</option>
                  <!-- Indian languages first -->
                  <option value="en">English</option>
                  <option value="hi">Hindi</option>
                  <option value="mr">Marathi</option>
                  <option value="gu">Gujarati</option>
                  <option value="bn">Bengali</option>
                  <option value="te">Telugu</option>
                  <option value="ta">Tamil</option>
                  <option value="ur">Urdu</option>
                  <option value="kn">Kannada</option>
                  <option value="ml">Malayalam</option>
                  <option value="pa">Punjabi</option>
                  <option value="or">Odia</option>
                  <option value="as">Assamese</option>
                  <option value="sd">Sindhi</option>
                  <option value="ne">Nepali</option>
                  <!-- All other languages (from your list) -->
                  <option value="af">Afrikaans</option>
                  <option value="sq">Albanian</option>
                  <option value="am">Amharic</option>
                  <option value="ar">Arabic</option>
                  <option value="hy">Armenian</option>
                  <option value="az">Azerbaijani</option>
                  <option value="eu">Basque</option>
                  <option value="be">Belarusian</option>
                  <option value="bg">Bulgarian</option>
                  <option value="bs">Bosnian</option>
                  <option value="ca">Catalan</option>
                  <option value="ceb">Cebuano</option>
                  <option value="zh-CN">Chinese (Simplified)</option>
                  <option value="zh-TW">Chinese (Traditional)</option>
                  <option value="co">Corsican</option>
                  <option value="hr">Croatian</option>
                  <option value="cs">Czech</option>
                  <option value="da">Danish</option>
                  <option value="nl">Dutch</option>
                  <option value="eo">Esperanto</option>
                  <option value="et">Estonian</option>
                  <option value="fi">Finnish</option>
                  <option value="fr">French</option>
                  <option value="fy">Frisian</option>
                  <option value="gl">Galician</option>
                  <option value="ka">Georgian</option>
                  <option value="de">German</option>
                  <option value="el">Greek</option>
                  <option value="ht">Haitian Creole</option>
                  <option value="ha">Hausa</option>
                  <option value="haw">Hawaiian</option>
                  <option value="iw">Hebrew</option>
                  <option value="hmn">Hmong</option>
                  <option value="hu">Hungarian</option>
                  <option value="is">Icelandic</option>
                  <option value="ig">Igbo</option>
                  <option value="id">Indonesian</option>
                  <option value="ga">Irish</option>
                  <option value="it">Italian</option>
                  <option value="ja">Japanese</option>
                  <option value="jw">Javanese</option>
                  <option value="kk">Kazakh</option>
                  <option value="km">Khmer</option>
                  <option value="rw">Kinyarwanda</option>
                  <option value="ko">Korean</option>
                  <option value="ku">Kurdish</option>
                  <option value="ky">Kyrgyz</option>
                  <option value="lo">Lao</option>
                  <option value="la">Latin</option>
                  <option value="lv">Latvian</option>
                  <option value="lt">Lithuanian</option>
                  <option value="lb">Luxembourgish</option>
                  <option value="mk">Macedonian</option>
                  <option value="mg">Malagasy</option>
                  <option value="ms">Malay</option>
                  <option value="mt">Maltese</option>
                  <option value="mi">Maori</option>
                  <option value="mn">Mongolian</option>
                  <option value="my">Myanmar (Burmese)</option>
                  <option value="no">Norwegian</option>
                  <option value="ny">Nyanja</option>
                  <option value="ps">Pashto</option>
                  <option value="fa">Persian</option>
                  <option value="pl">Polish</option>
                  <option value="pt">Portuguese</option>
                  <option value="ro">Romanian</option>
                  <option value="ru">Russian</option>
                  <option value="sm">Samoan</option>
                  <option value="gd">Scots Gaelic</option>
                  <option value="sr">Serbian</option>
                  <option value="st">Sesotho</option>
                  <option value="si">Sinhala</option>
                  <option value="sk">Slovak</option>
                  <option value="sl">Slovenian</option>
                  <option value="so">Somali</option>
                  <option value="es">Spanish</option>
                  <option value="su">Sundanese</option>
                  <option value="sw">Swahili</option>
                  <option value="sv">Swedish</option>
                  <option value="tl">Tagalog</option>
                  <option value="tg">Tajik</option>
                  <option value="tt">Tatar</option>
                  <option value="te">Telugu</option>
                  <!-- duplicate safe -->
                  <option value="th">Thai</option>
                  <option value="tr">Turkish</option>
                  <option value="tk">Turkmen</option>
                  <option value="uk">Ukrainian</option>
                  <option value="ur">Urdu</option>
                  <option value="ug">Uyghur</option>
                  <option value="uz">Uzbek</option>
                  <option value="vi">Vietnamese</option>
                  <option value="cy">Welsh</option>
                  <option value="xh">Xhosa</option>
                  <option value="yi">Yiddish</option>
                  <option value="yo">Yoruba</option>
                  <option value="zu">Zulu</option>
                </select>
                
                <div class="mobile-menu-trigger">
                  <span></span>
                </div>
                
              </div>
            </div>
          </div>
        </header>
      </div>
      <!-- header end -->


      <!-- Modal -->
      <div id="health-checkup" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content -->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Book Health Check</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <h6 class="form-title">please fill out all required fields meaning</h6>
                <form class="book-appoint-form">
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Package" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Date of Birth:</label>
                    <input type="date" class="form-control" placeholder="" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Date of Appointment :</label>
                    <input type="date" class="form-control" placeholder="" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email ID" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Mobile Number" required>
                  </div>
                </div>
                
                <div class="col-md-12">
                  <div class="button-box">
                    <a class="twenty" href="#"><span>Submit</span></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    
      <div id="bookappointment-services" class="modal fade" role="dialog">
        <div class="modal-dialog">
          <!-- Modal content -->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Book Appointment</h4>
            </div>
            <div class="modal-body">
              <div class="row">
                <h6 class="form-title">please fill out all required fields meaning</h6>
                <form class="book-appoint-form">
                <div class="col-md-8">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Enter Patient Name" required>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <select class="form-control">
                      <option>--Select Gender--</option>
                      <option>Male</option>
                      <option>Female</option>
                      <option>Other</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Mobile Number" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Email Address" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <input type="text" class="form-control" placeholder="Pincode" required>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select class="form-control">
                      <option>--Select Country--</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select class="form-control">
                      <option>--Select State--</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <select class="form-control">
                      <option>--Select City--</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <select class="form-control">
                      <option>--Select Speciality--</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label></label>
                    <input type="text" class="form-control" placeholder="Doctor Name" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Appointment Date:</label>
                    <input type="date" class="form-control" placeholder="Appointment Date" required>
                  </div>
                </div>
                <!-- <div class="col-md-12">
                  <div class="form-group">
                  <textarea class="form-control" rows="5" placeholder="Message" required></textarea>
                  </div>
                  </div> -->
                <div class="col-md-12">
                  <div class="button-box">
                    <a class="twenty" href="#"><span>Submit</span></a>
                  </div>
                </div>
              </div>
            </div>
            <!-- <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">
              Close
              </button>
              </div> -->
          </div>
        </div>
      </div>
    <!-- Modal -->
    