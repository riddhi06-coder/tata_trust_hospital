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


                
                <!-- Home slider banner Details -->
                <li class="sidebar-list {{ request()->routeIs('banner-details.index','short-introduction.index','home-specialities.index') ? 'active' : '' }}">
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
                    <li><a href="{{ route('home-specialities.index') }}"> Specialities </a></li>
                  </ul>
                </li>
                
                
              </ul>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </div>
          </nav>
        </div>


        