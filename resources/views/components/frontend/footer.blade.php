@php
  $footer = \App\Models\FooterDetail::wherenull('deleted_by')->first();
  $social_links = $footer && $footer->social_links ? json_decode($footer->social_links, true) : [];

@endphp


    <footer>
      <div class="container">
        <div class="row main-footer" id="exclude">
          <div class="col-md-6">
            <div class="footer-about">

                {{-- Logo --}}
                @if(!empty($footer?->logo))
                    <img src="{{ asset('home/footer/' . $footer->logo) }}" class="img-responsive">
                @endif

                <hr>

                {{-- Contact Details --}}
                <div class="footer-contact">
                  <ul>

                      {{-- Address --}}
                      @if(!empty($footer?->address))
                          <li>
                              <i class="fa fa-map-marker"></i>
                              {{ $footer->address }}
                          </li>
                      @endif

      

                        @if(!empty($footer?->map_iframe))
                            <li>
                                {!! $footer->map_iframe !!}
                            </li>
                        @endif

         
                        <li class="footer_call">
                          <i class="fa fa-phone"></i>

                          @if(!empty($footer?->enquiry_number))
                              <b>24x7 Enquiry:</b>
                              <a href="tel:{{ $footer->enquiry_number }}">{{ $footer->enquiry_number }}</a><br>
                          @endif

                          @if(!empty($footer?->emergency_contact))
                              <b>Emergency Contact:</b>
                              <a href="tel:{{ $footer->emergency_contact }}">{{ $footer->emergency_contact }}</a><br>
                          @endif

                          @if(!empty($footer?->opd_appointment))
                              <b>Book OPD Appointment:</b>
                              <a href="tel:{{ $footer->opd_appointment }}">{{ $footer->opd_appointment }}</a><br>
                          @endif

                          @if(!empty($footer?->wellness_appointment))
                              <b>Wellness Appointment:</b>
                              <a href="tel:{{ $footer->wellness_appointment }}">{{ $footer->wellness_appointment }}</a>
                          @endif
                      </li>

                        {{-- Phone / Contact HTML --}}
                        @if(!empty($footer?->contact_html))
                            <li class="footer_call">
                                {!! $footer->contact_html !!}
                            </li>
                        @endif

                  </ul>
                </div>

                @if(!empty($social_links))
                    <ul class="footer-social-links">
                        @foreach($social_links as $link)
                            @php
                                $icon = '';
                                switch ((int)$link['platform']) {
                                    case 1: $icon = 'fa-facebook'; break;
                                    case 2: $icon = 'fa-twitter'; break;
                                    case 3: $icon = 'fa-instagram'; break;
                                    case 4: $icon = 'fa-linkedin'; break;
                                    case 5: $icon = 'fa-youtube'; break;
                                    case 6: $icon = 'fa-pinterest'; break;
                                }
                            @endphp

                            @if($icon && !empty($link['link']))
                                <li>
                                    <a href="{{ $link['link'] }}" target="_blank" rel="noopener">
                                        <i class="fa {{ $icon }}"></i>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
             

            </div>

              </div>
                <div class="col-md-4">
                  <div class="footer-links">
                    <h5>Quick Links</h5>
                    <ul>
                      <li><a href="{{ route('frontend.specialties') }}">Specialties</a></li>
                      <li><a href="{{ route('frontend.billing_process') }}">Billing Process</a></li>
                      <li><a href="{{ route('frontend.insurance_and_tpa') }}">Insurance & TPA</a></li>
                      <li><a href="{{ route('frontend.biomedical_waste') }}">Biomedical Waste</a></li>

                      <li><a href="{{ route('frontend.management_team') }}">Management Team</a></li>
                      <li><a href="{{ route('frontend.accreditations') }}">Accreditations</a></li>
                      <li><a href="{{ route('frontend.awards_accolades') }}">Awards &amp; Accolades</a></li>
                      <li><a href="{{ route('frontend.contact_us') }}">Contact Us</a></li>
                      <li><a href="{{ route('frontend.blogs') }}">Blogs</a></li>
                      <!-- <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Disclaimer</a></li>
                        <li><a href="#">Terms of use</a></li> -->
                    </ul>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="footer-links">
                    <h5>Wellness Centre</h5>
                    <ul>
                      <li><a href="{{ route('frontend.health_packages') }}">Health Packages</a></li>
                      <li><a href="{{ route('frontend.ayurveda') }}">Ayurveda</a></li>
                      <li>
                        <a href="{{ route('frontend.alternative_therapies') }}">
                          <!-- Other --> Alternative Therapies
                        </a>
                      </li>
                    </ul>
                    <h5>News &amp; Events</h5>
                    <ul>
                      <!-- <li><a href="#">News &amp; Events</a></li> -->
                      <li><a href="#">Announcements</a></li>
                      <li><a href="{{ route('frontend.gallery_listing') }}">Gallery</a></li>
                      <li><a href="{{ route('frontend.media_coverage') }}">Media Coverage</a></li>
                      <li><a href="career.html">Career</a></li>
                    </ul>
                    <!-- <h5><a href="#">Virtual Tour</a></h5> -->
                  </div>
                </div>
                
              </div>
              
              <hr>

              <div class="row">
                <div class="col-md-6">
                  <div class="copyright-text">
                    <p>Copyright © {{ date('Y') }} K J Somaiya Hospital. All Rights Reserved. | Crafted by <a href="https://www.matrixbricks.com/" target="_blank">Matrix Bricks</a></p>
                  </div>
                </div>
                <div class="col-md-6">
                  <ul class="privacy_links">
                    <li><a href="{{ route('frontend.privacy') }}">Privacy</a></li>
                    <li><a href="{{ route('frontend.disclaimer') }}">Disclaimers</a></li>
                    <li><a href="{{ route('frontend.terms_conditions') }}">Terms and Conditions</a></li>
                  </ul>
                </div>
              </div>

              <div id="videoModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-body">
                      <iframe src="" allowfullscreen></iframe>
                    </div>
                  </div>
                </div>
              </div>
              
              <a href="#" class="menu_contact_icon float float-search"><i class="fa fa-search"></i></a> 

              <input type="checkbox" id="menuToggle" class="menu-toggle">

              <label for="menuToggle" class="open-menu-btn float"><i class="fa fa-phone"></i></label>
              <div class="side-menu">
                <label for="menuToggle" class="closebtn">&times;</label>
                <ul class="sidemenu_numbers">
                  <li>24x7 Enquiry: <br><a href="tel:02261124800">022-6112 4800</a></li>
                  <li>Emergency Contact: <br><a href="tel:02250954723">022-50954723</a></li>
                  <li>Book OPD Appointment: <br><a href="tel:02250954700">022-50954700</a> / <a href="tel:9324960673">9324960673</a></li>
                  <li>Wellness Appointment: <br><a href="tel:918090155888">+91-8090155888</a></li>
                </ul>
              </div>

              <a id="button"></a>

              <!-- ✅ Bootstrap 3 Modal -->
              <div id="imageModal" class="modal fade">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                      <!-- <h4 class="modal-title">Full Image</h4> -->
                    </div>
                    <div class="modal-body text-center">
                      <img id="fullImage">
                    </div>
                  </div>
                </div>
              </div>

         </div>
    </footer>