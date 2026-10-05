    <!-- loader starts-->
    <div class="loader-wrapper">
      <div class="loader"> 
        <div class="loader4"></div>
      </div>
    </div>
    <!-- loader ends-->
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
      <!-- Page Header Start-->
      <div class="page-header">
        <div class="header-wrapper row m-0">
          <form class="form-inline search-full col" action="#" method="get">
            <div class="form-group w-100">
              <div class="Typeahead Typeahead--twitterUsers">
                <div class="u-posRelative"> 
                  <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search Riho .." name="q" title="" autofocus>
                  <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading... </span></div><i class="close-search" data-feather="x"></i>
                </div>
                <div class="Typeahead-menu"> </div>
              </div>
            </div>
          </form>
          <div class="header-logo-wrapper col-auto p-0">  
            <div class="logo-wrapper"> <a href="index.html"><img class="img-fluid for-light" src="{{ asset('admin/assets/images/logo/logo_dark.png') }}" alt="logo-light"><img class="img-fluid for-dark" src="{{ asset('admin/assets/images/logo/logo_dark.png') }}" alt="logo-dark"></a></div>
            <div class="toggle-sidebar"> <i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
          </div>
          <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <div> <a class="toggle-sidebar" href="#"> <i class="iconly-Category icli"> </i></a>
              <div class="d-flex align-items-center gap-2 ">
              <h4 class="f-w-600">Welcome, {{ Auth::user()->name }}</h4><img class="mt-0" src="{{ asset('admin/assets/images/hand.gif') }}" alt="hand-gif">
              </div>
            </div>
            <div class="welcome-content d-xl-block d-none"><span class="text-truncate col-12">Here’s what’s happening with your store today. </span></div>
          </div>
          <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
            <ul class="nav-menus">

              {{-- Activity notifications bell --}}
              <li class="an-notif-wrap">
                <a href="javascript:void(0)" class="an-bell" id="anBellToggle" title="Notifications" aria-label="Notifications">
                  <i data-feather="bell"></i>
                  <span class="an-badge" id="anBadge" style="display:none;">0</span>
                </a>
                <div class="an-dropdown" id="anDropdown">
                  <div class="an-dd-head">
                    <span>Notifications</span>
                    <a href="javascript:void(0)" id="anMarkAll" class="an-markall">Mark all read</a>
                  </div>
                  <div class="an-dd-list" id="anList">
                    <div class="an-empty">No notifications yet.</div>
                  </div>
                  <a href="{{ route('admin.notifications.index') }}" class="an-dd-foot">View all notifications</a>
                </div>
              </li>

              <li>
                <div class="mode"><i class="moon" data-feather="moon"> </i></div>
              </li>
           
              <li class="profile-nav onhover-dropdown"> 
                <div class="media profile-media"><img class="b-r-10" src="{{ asset('admin/assets/images/logo/favicon.png') }}" alt="">
                  <div class="media-body d-xxl-block d-none box-col-none">
                    <div class="d-flex align-items-center gap-2"> <span>{{ Auth::user()->name }} </span><i class="middle fa fa-angle-down"> </i></div>
                    <!-- <p class="mb-0 font-roboto">{{ Auth::user()->name }}</p> -->
                  </div>
                </div>
                <ul class="profile-dropdown onhover-show-div">
                  <!-- <li><a href="user-profile.html"><i data-feather="user"></i><span>My Profile</span></a></li> -->
                  <!-- <li><a href="letter-box.html"><i data-feather="mail"></i><span>Inbox</span></a></li>
                  <li> <a href="edit-profile.html"> <i data-feather="settings"></i><span>Settings</span></a></li> -->
                  <li><a class="btn btn-pill btn-outline-primary btn-sm" href="{{ route('admin.logout') }}">Log Out</a></li>
                </ul>
              </li>
            </ul>
          </div>
          <script class="result-template" type="text/x-handlebars-template">
            <div class="ProfileCard u-cf">                        
            <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg') }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
            <div class="ProfileCard-details"> 
            <div class="ProfileCard-realName"></div>
            </div> 
            </div>
          </script>
          <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
        </div>
      </div>

      <style>
        .an-notif-wrap{position:relative;display:flex;align-items:center;}
        .an-bell{position:relative;display:flex;align-items:center;justify-content:center;width:40px;height:40px;color:#52526c;}
        .an-bell svg{width:20px;height:20px;}
        .an-bell.has-unread svg{animation:anSwing 1.6s ease infinite;transform-origin:top center;}
        @keyframes anSwing{0%,60%,100%{transform:rotate(0)}10%{transform:rotate(16deg)}20%{transform:rotate(-14deg)}30%{transform:rotate(10deg)}40%{transform:rotate(-6deg)}50%{transform:rotate(3deg)}}
        .an-badge{position:absolute;top:2px;right:2px;min-width:17px;height:17px;padding:0 4px;border-radius:9px;background:#dc3545;color:#fff;font-size:10px;font-weight:700;line-height:17px;text-align:center;box-shadow:0 0 0 2px #fff;}
        .an-dropdown{position:absolute;top:48px;right:0;width:340px;max-width:92vw;background:#fff;border:1px solid #efefef;border-radius:12px;box-shadow:0 12px 40px rgba(0,0,0,.14);z-index:1050;display:none;overflow:hidden;}
        .an-dropdown.show{display:block;}
        .an-dd-head{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f1f1f1;font-weight:600;font-size:14px;color:#2b2b2b;}
        .an-markall{font-size:12px;color:#7366ff;font-weight:600;}
        .an-dd-list{max-height:360px;overflow-y:auto;}
        .an-item{display:flex;gap:10px;padding:11px 16px;border-bottom:1px solid #f6f6f6;text-decoration:none;color:#2b2b2b;transition:background .15s ease;}
        .an-item:hover{background:#f7f6ff;}
        .an-item.unread{background:#f3f1ff;}
        .an-ic{flex:none;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#eef;color:#7366ff;}
        .an-ic svg{width:17px;height:17px;}
        .an-ic.success{background:#e7f7ee;color:#2fab66;} .an-ic.info{background:#e6f4ff;color:#2a8ff0;}
        .an-ic.warning{background:#fff4e3;color:#e8920b;} .an-ic.whatsapp{background:#e7f7ee;color:#25D366;}
        .an-it-body{min-width:0;}
        .an-it-title{font-size:13px;font-weight:600;line-height:1.3;}
        .an-it-sub{font-size:12px;color:#8a8a9a;line-height:1.35;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .an-it-ago{font-size:11px;color:#b3b3c2;margin-top:2px;}
        .an-empty{padding:28px 16px;text-align:center;color:#9a9aad;font-size:13px;}
        .an-dd-foot{display:block;text-align:center;padding:11px;font-size:13px;font-weight:600;color:#7366ff;border-top:1px solid #f1f1f1;}

        /* Professional toast popups */
        .an-toast-wrap{position:fixed;top:22px;right:22px;z-index:2000;display:flex;flex-direction:column;gap:12px;width:360px;max-width:92vw;pointer-events:none;}
        .an-toast{pointer-events:auto;display:flex;align-items:flex-start;gap:12px;background:#fff;border:1px solid #edeef2;border-left:4px solid #7366ff;border-radius:12px;box-shadow:0 14px 36px rgba(17,24,39,.16);padding:14px 14px 15px 16px;cursor:pointer;overflow:hidden;position:relative;transform:translateX(130%);opacity:0;transition:transform .4s cubic-bezier(.2,.8,.2,1),opacity .4s;}
        .an-toast.in{transform:translateX(0);opacity:1;}
        .an-toast.out{transform:translateX(130%);opacity:0;}
        .an-toast--success{border-left-color:#22c55e;} .an-toast--warning{border-left-color:#f59e0b;} .an-toast--info{border-left-color:#3b82f6;}
        .an-toast__ic{flex:none;width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#eef0ff;color:#7366ff;}
        .an-toast--success .an-toast__ic{background:#e7f8ee;color:#1fa75a;}
        .an-toast--warning .an-toast__ic{background:#fef3e2;color:#e0890b;}
        .an-toast--info .an-toast__ic{background:#e8f1fe;color:#2f7bea;}
        .an-toast__ic svg{width:19px;height:19px;}
        .an-toast__body{min-width:0;flex:1;padding-top:1px;}
        .an-toast__title{font-size:13.5px;font-weight:600;color:#1f2430;line-height:1.35;}
        .an-toast__title b{font-weight:700;}
        .an-toast__sub{font-size:12.5px;color:#8a90a2;line-height:1.4;margin-top:2px;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;}
        .an-toast__close{flex:none;color:#c2c6d2;font-size:15px;line-height:1;padding:3px;margin:-3px -3px 0 0;background:none;border:0;cursor:pointer;}
        .an-toast__close:hover{color:#6b7280;}
        .an-toast__bar{position:absolute;left:0;bottom:0;height:3px;width:100%;background:#7366ff;opacity:.45;transform-origin:left;}
        .an-toast--success .an-toast__bar{background:#22c55e;} .an-toast--warning .an-toast__bar{background:#f59e0b;} .an-toast--info .an-toast__bar{background:#3b82f6;}
        @keyframes anBar{from{transform:scaleX(1)}to{transform:scaleX(0)}}
      </style>
      <!-- Page Header Ends 