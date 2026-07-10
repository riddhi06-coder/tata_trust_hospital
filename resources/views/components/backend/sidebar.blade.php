<!-- Page Body Start-->
 <div class="page-body-wrapper">
        <!-- Page Sidebar Start-->
        <div class="sidebar-wrapper" data-layout="stroke-svg">
          <div class="logo-wrapper"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid" src="{{ asset('') }}" alt="" style="max-width: 35% !important;"></a>
		  	<a href="{{ route('admin.dashboard') }}">
				<img class="img-fluid" src="{{ asset('admin/assets/images/logo/tata-trust-logo.webp') }}" alt="" style="max-width: 88% !important;">
			</a>  
		  <div class="back-btn"><i class="fa fa-angle-left"> </i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
          </div>
          <div class="logo-icon-wrapper"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid" src="{{ asset('admin/assets/images/logo/favicon.png') }}" alt="" ></a></div>
          <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
              <ul class="sidebar-links" id="simple-bar">
                <li class="back-btn"><a href="{{ route('admin.dashboard') }}"><img class="img-fluid" src="{{ asset('admin/assets/images/logo/favicon.png') }}" alt=""></a>
                  <div class="mobile-back text-end"> <span>Back </span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                </li>
             
                <li class="sidebar-list {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"> </i>
                  <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.dashboard') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#fill-home') }}"></use>
                    </svg>
                    <span class="lan-3">Dashboard</span>
                  </a>
                </li>


                @php
                    $canRoles       = auth()->user()?->hasPermission('roles.view');
                    $canUsers       = auth()->user()?->hasPermission('users.view');
                    $canPermissions = auth()->user()?->hasPermission('permissions.view');
                @endphp

                @if($canRoles || $canUsers || $canPermissions)
                <li class="sidebar-list {{ request()->routeIs('admin.roles.*', 'admin.users.*', 'admin.permissions.*') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-user') }}"></use>
                    </svg>
                    <span>User</span>
                  </a>
                  <ul class="sidebar-submenu">
                      @if($canRoles)
                          <li><a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Roles</a></li>
                      @endif
                      @if($canUsers)
                          <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">Users</a></li>
                      @endif
                      @if($canPermissions)
                          <li><a href="{{ route('admin.permissions.index') }}" class="{{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">Permissions</a></li>
                      @endif
                  </ul>
                </li>
                @endif

                <!-- Form Enquiries -->
                <li class="sidebar-list {{ request()->routeIs('manage-contact-enquiries.*', 'manage-job-applications.*', 'manage-appointment-enquiries.*') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-email') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-email') }}"></use>
                    </svg>
                    <span>Form Enquiries</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('manage-contact-enquiries.index') }}" class="{{ request()->routeIs('manage-contact-enquiries.*') ? 'active' : '' }}">Contact Enquiries</a></li>
                    <li><a href="{{ route('manage-job-applications.index') }}" class="{{ request()->routeIs('manage-job-applications.*') ? 'active' : '' }}">Job Applications</a></li>
                    <li><a href="{{ route('manage-appointment-enquiries.index') }}" class="{{ request()->routeIs('manage-appointment-enquiries.*') ? 'active' : '' }}">Appointment Enquiries</a></li>
                  </ul>
                </li>

                <!-- Home slider banner Details -->
                <li class="sidebar-list {{ request()->routeIs('banner-details.index','short-introduction.index','home-services.index','manage-facilities.index','home-team.index','manage-testimonials.index','manage-board.index','manage-follow-us.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-icons') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-icons') }}"></use>
                    </svg>
                    <span>Home</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('banner-details.index') }}">Banner Details </a></li>
                    <li><a href="{{ route('short-introduction.index') }}"> Short Introduction </a></li>
                    <li><a href="{{ route('home-services.index') }}"> Services </a></li>
                    <li><a href="{{ route('manage-facilities.index') }}"> Facilities </a></li>
                    <li><a href="{{ route('home-team.index') }}"> Our Team </a></li>
                    <li><a href="{{ route('manage-testimonials.index') }}"> Testimonials </a></li>
                    <li><a href="{{ route('manage-board.index') }}"> Our Board </a></li>
                    <li><a href="{{ route('manage-follow-us.index') }}"> Follow Us </a></li>
                  </ul>
                </li>
                
                <li class="sidebar-list {{ request()->routeIs('manage-our-team.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-our-team.index') }}">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                    </svg>
                    <span>Our Team</span>
                  </a>
                </li>


                
                <!-- Home slider banner Details -->
                <li class="sidebar-list {{ request()->routeIs('manage-specialities.index','speciality-details.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-learning') }}"></use>
                    </svg>
                    <span>Specialities</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('manage-specialities.index') }}"> Listing </a></li>
                    <li><a href="{{ route('speciality-details.index') }}"> Details </a></li>
                  </ul>
                </li>


                <li class="sidebar-list {{ request()->routeIs('manage-master-facilities.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-master-facilities.index') }}">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-widget') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-widget') }}"></use>
                    </svg>
                    <span>Our Facilities</span>
                  </a>
                </li>



                <li class="sidebar-list {{ request()->routeIs('manage-about-us.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-about-us.index') }}">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-layout') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-layout') }}"></use>
                    </svg>
                    <span>About Us</span>
                  </a>
                </li>

                

                <li class="sidebar-list {{ request()->routeIs('manage-master-testimonials.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-master-testimonials.index') }}">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                    </svg>
                    <span>Testimonials</span>
                  </a>
                </li>


                <li class="sidebar-list {{ request()->routeIs('manage-gallery.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-gallery.index') }}">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-gallery') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-gallery') }}"></use>
                    </svg>
                    <span>Gallery</span>
                  </a>
                </li>


                <li class="sidebar-list {{ request()->routeIs('manage-events.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-events.index') }}">
                    <svg class="stroke-icon"> 
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-starter-kit') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-starter-kit') }}"></use>
                    </svg>
                    <span>Events</span>
                  </a>
                </li>
                

                <!-- Join Us -->
                <li class="sidebar-list {{ request()->routeIs('manage-join-page.index','manage-job-role.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-maps') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-maps') }}"></use>
                    </svg>
                    <span>Join Us</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('manage-join-page.index') }}"> Page Details </a></li>
                    <li><a href="{{ route('manage-job-role.index') }}"> Job Postings </a></li>
                  </ul>
                </li>



                <li class="sidebar-list {{ request()->routeIs('manage-faqs.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-faqs.index') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-faq') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-faq') }}"></use>
                    </svg>
                    <span>FAQ's</span>
                  </a>
                </li>



                @if(auth()->user()?->isSuperAdmin())
                <li class="sidebar-list {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('admin.activity-logs.index') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-file') }}"></use>
                    </svg>
                    <span>Activity Log</span>
                  </a>
                </li>
                @endif


                <!-- Blog-->
                <li class="sidebar-list {{ request()->routeIs('manage-blogs-listing.*', 'manage-blog-details.*', 'manage-blog-category.*', 'manage-blog-comments.*') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-blog') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-blog') }}"></use>
                    </svg>
                    <span>Blog</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('manage-blog-category.index') }}"> Category </a></li>
                    <li><a href="{{ route('manage-blogs-listing.index') }}"> Listing </a></li>
                    <li><a href="{{ route('manage-blog-details.index') }}"> Details </a></li>
                    <li><a href="{{ route('manage-blog-comments.index') }}"> Comments </a></li>
                  </ul>
                </li>


                <li class="sidebar-list {{ request()->routeIs('manage-contact-details.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-contact-details.index') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-editors') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-editors') }}"></use>
                    </svg>
                    <span>Contact Details</span>
                  </a>
                </li>


                <li class="sidebar-list {{ request()->routeIs('manage-privacy-policy.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-privacy-policy.index') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-knowledgebase') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-knowledgebase') }}"></use>
                    </svg>
                    <span>Privacy Policy</span>
                  </a>
                </li>

              
              </ul>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
          </nav>
        </div>


        