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
                    $authUser = auth()->user();
                    // Convenience checker used across every sidebar tab below.
                    $can = fn (string $permission) => (bool) $authUser?->hasPermission($permission);

                    $canRoles       = $can('roles.view');
                    $canUsers       = $can('users.view');
                    $canPermissions = $can('permissions.view');
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

                <li class="sidebar-list {{ request()->routeIs('manage-flyer.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-flyer.index') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-editors') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-editors') }}"></use>
                    </svg>
                    <span>Flyer</span>
                  </a>
                </li>

                <!-- Form Enquiries -->
                @if($can('contact-enquiries.view') || $can('job-applications.view') || $can('appointment-enquiries.view'))
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
                    @if($can('contact-enquiries.view'))
                        <li><a href="{{ route('manage-contact-enquiries.index') }}" class="{{ request()->routeIs('manage-contact-enquiries.*') ? 'active' : '' }}">Contact Enquiries</a></li>
                    @endif
                    @if($can('job-applications.view'))
                        <li><a href="{{ route('manage-job-applications.index') }}" class="{{ request()->routeIs('manage-job-applications.*') ? 'active' : '' }}">Job Applications</a></li>
                    @endif
                    @if($can('appointment-enquiries.view'))
                        <li><a href="{{ route('manage-appointment-enquiries.index') }}" class="{{ request()->routeIs('manage-appointment-enquiries.*') ? 'active' : '' }}">Appointment Enquiries</a></li>
                    @endif
                  </ul>
                </li>
                @endif

                <!-- Appointments -->
                @if($can('appointment-users.view') || $can('appointments.view') || $can('appointment-statuses.view'))
                <li class="sidebar-list {{ request()->routeIs('manage-appointment-users.*', 'manage-appointments.*', 'manage-appointment-statuses.*') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-calendar') }}"></use>
                    </svg>
                    <span>Appointments</span>
                  </a>
                  <ul class="sidebar-submenu">
                    @if($can('appointment-users.view'))
                        <li><a href="{{ route('manage-appointment-users.index') }}" class="{{ request()->routeIs('manage-appointment-users.*') ? 'active' : '' }}">Appointment Users</a></li>
                    @endif
                    @if($can('appointments.view'))
                        <li><a href="{{ route('manage-appointments.index') }}" class="{{ request()->routeIs('manage-appointments.*') ? 'active' : '' }}">Appointments</a></li>
                    @endif
                    @if($can('appointment-statuses.view'))
                        <li><a href="{{ route('manage-appointment-statuses.index') }}" class="{{ request()->routeIs('manage-appointment-statuses.*') ? 'active' : '' }}">Manage Statuses</a></li>
                    @endif
                  </ul>
                </li>
                @endif

                <!-- Reports -->
                @if($can('reports.view'))
                <li class="sidebar-list {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link sidebar-title" href="#">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-charts') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#fill-charts') }}"></use>
                    </svg>
                    <span>Reports</span>
                  </a>
                  <ul class="sidebar-submenu">
                    <li><a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">Overview</a></li>
                    <li><a href="{{ route('admin.reports.appointments') }}" class="{{ request()->routeIs('admin.reports.appointments') ? 'active' : '' }}">Appointments</a></li>
                    <li><a href="{{ route('admin.reports.operational') }}" class="{{ request()->routeIs('admin.reports.operational') ? 'active' : '' }}">Operational</a></li>
                    <li><a href="{{ route('admin.reports.clients') }}" class="{{ request()->routeIs('admin.reports.clients') ? 'active' : '' }}">Clients</a></li>
                    @if(auth()->user()?->isSuperAdmin())
                        <li><a href="{{ route('admin.reports.communication') }}" class="{{ request()->routeIs('admin.reports.communication') ? 'active' : '' }}">Communication</a></li>
                    @endif
                  </ul>
                </li>
                @endif

                <!-- Home slider banner Details -->
                @php
                    $canHome = $can('home-banners.view') || $can('home-short-intro.view') || $can('home-specialities.view')
                        || $can('home-facilities.view') || $can('home-our-team.view') || $can('home-testimonials.view')
                        || $can('home-board.view') || $can('home-follow-us.view');
                @endphp
                @if($canHome)
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
                    @if($can('home-banners.view'))      <li><a href="{{ route('banner-details.index') }}">Banner Details </a></li> @endif
                    @if($can('home-short-intro.view'))  <li><a href="{{ route('short-introduction.index') }}"> Short Introduction </a></li> @endif
                    @if($can('home-specialities.view')) <li><a href="{{ route('home-services.index') }}"> Services </a></li> @endif
                    @if($can('home-facilities.view'))   <li><a href="{{ route('manage-facilities.index') }}"> Facilities </a></li> @endif
                    @if($can('home-our-team.view'))     <li><a href="{{ route('home-team.index') }}"> Our Team </a></li> @endif
                    @if($can('home-testimonials.view')) <li><a href="{{ route('manage-testimonials.index') }}"> Testimonials </a></li> @endif
                    @if($can('home-board.view'))        <li><a href="{{ route('manage-board.index') }}"> Our Board </a></li> @endif
                    @if($can('home-follow-us.view'))    <li><a href="{{ route('manage-follow-us.index') }}"> Follow Us </a></li> @endif
                  </ul>
                </li>
                @endif
                
                @if($can('our-team.view'))
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
                @endif


                
                <!-- Specialities -->
                @if($can('specialities.view') || $can('speciality-details.view'))
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
                    @if($can('specialities.view'))        <li><a href="{{ route('manage-specialities.index') }}"> Listing </a></li> @endif
                    @if($can('speciality-details.view'))  <li><a href="{{ route('speciality-details.index') }}"> Details </a></li> @endif
                  </ul>
                </li>
                @endif


                @if($can('master-facilities.view'))
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
                @endif



                @if($can('about-us.view'))
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
                @endif

                

                @if($can('testimonials.view'))
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
                @endif


                @if($can('gallery.view'))
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
                @endif


                @if($can('events.view'))
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
                @endif
                

                <!-- Join Us -->
                @if($can('join-page.view') || $can('job-roles.view'))
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
                    @if($can('join-page.view')) <li><a href="{{ route('manage-join-page.index') }}"> Page Details </a></li> @endif
                    @if($can('job-roles.view')) <li><a href="{{ route('manage-job-role.index') }}"> Job Postings </a></li> @endif
                  </ul>
                </li>
                @endif



                @if($can('faqs.view'))
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
                @endif

                <!-- Blog-->
                @if($can('blog-categories.view') || $can('blog-listings.view') || $can('blog-details.view') || $can('blog-comments.view'))
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
                    @if($can('blog-categories.view')) <li><a href="{{ route('manage-blog-category.index') }}"> Category </a></li> @endif
                    @if($can('blog-listings.view'))   <li><a href="{{ route('manage-blogs-listing.index') }}"> Listing </a></li> @endif
                    @if($can('blog-details.view'))    <li><a href="{{ route('manage-blog-details.index') }}"> Details </a></li> @endif
                    @if($can('blog-comments.view'))   <li><a href="{{ route('manage-blog-comments.index') }}"> Comments </a></li> @endif
                  </ul>
                </li>
                @endif


                @if($can('contact-details.view'))
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
                @endif


                @if($can('privacy-policy.view'))
                <li class="sidebar-list {{ request()->routeIs('manage-privacy-policy.index') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('manage-privacy-policy.index') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-knowledgebase') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-knowledgebase') }}"></use>
                    </svg>
                    <span>Policy</span>
                  </a>
                </li>
                @endif
                
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

                <li class="sidebar-list {{ request()->routeIs('admin.communication-logs.*') ? 'active' : '' }}">
                  <i class="fa fa-thumb-tack"></i>
                  <a class="sidebar-link" href="{{ route('admin.communication-logs.index') }}">
                    <svg class="stroke-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-email') }}"></use>
                    </svg>
                    <svg class="fill-icon">
                      <use href="{{ asset('admin/assets/svg/icon-sprite.svg#stroke-email') }}"></use>
                    </svg>
                    <span>Communication Logs</span>
                  </a>
                </li>
                @endif

              
              </ul>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
          </nav>
        </div>


        