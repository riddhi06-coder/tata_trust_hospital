<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\PreventBackHistoryMiddleware;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\HomeBannerController;
use App\Http\Controllers\Backend\ShortIntroductionController;
use App\Http\Controllers\Backend\HomeServicesController;
use App\Http\Controllers\Backend\HomeFacilitiesController;
use App\Http\Controllers\Backend\HomeTeamController;
use App\Http\Controllers\Backend\HomeTestimonialsController;
use App\Http\Controllers\Backend\HomeBoardController;
use App\Http\Controllers\Backend\HomeFollowUsController;
use App\Http\Controllers\Backend\MasterOurTeamController;
use App\Http\Controllers\Backend\MasterTestimonialsController;
use App\Http\Controllers\Backend\GalleryController;
use App\Http\Controllers\Backend\EventsController;
use App\Http\Controllers\Backend\SpecialitiesController;
use App\Http\Controllers\Backend\SpecialitiesDetailsController;
use App\Http\Controllers\Backend\FAQController;
use App\Http\Controllers\Backend\MasterFacilitiesController;
use App\Http\Controllers\Backend\AboutUsController;
use App\Http\Controllers\Backend\ActivityLogController;
use App\Http\Controllers\Backend\CommunicationLogController;
use App\Http\Controllers\Backend\JoinPageController;
use App\Http\Controllers\Backend\JobRoleController;
use App\Http\Controllers\Backend\ContactDetailsController;
use App\Http\Controllers\Backend\PrivacyPolicyController;
use App\Http\Controllers\Backend\BlogListingController;
use App\Http\Controllers\Backend\BlogDetailsController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\AppointmentEnquiryController;
use App\Http\Controllers\Backend\AppointmentUserController;
use App\Http\Controllers\Backend\AppointmentController;
use App\Http\Controllers\Backend\AppointmentStatusController;
use App\Http\Controllers\Backend\ReportController;
use App\Http\Controllers\Backend\ContactEnquiryController;
use App\Http\Controllers\Backend\JobApplicationController;
use App\Http\Controllers\Backend\BlogCommentController;
use App\Http\Controllers\Backend\FlyerController;

//frontend controller
use App\Http\Controllers\Frontend\HomeController;



    // ----------------------
    // Guest-only auth routes (login / register / forgot password)
    // ----------------------
    Route::middleware('guest')->group(function () {
        // Login
        Route::get('/login',  [LoginController::class, 'login'])->name('admin.login');
        Route::post('/login', [LoginController::class, 'authenticate'])->name('admin.authenticate');

        // Register
        Route::get('/register',  [LoginController::class, 'register'])->name('admin.register');
        Route::post('/register', [LoginController::class, 'authenticate_register'])->name('admin.register.authenticate');

        // Forgot password — request a reset link
        Route::get('/forgot-password',  [LoginController::class, 'showForgotPasswordForm'])->name('admin.password.request');
        Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('admin.password.email');

        // Reset password — clicked from email
        Route::get('/reset-password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('password.reset');
        Route::post('/reset-password',         [LoginController::class, 'resetPassword'])->name('admin.password.update');

        // Backward-compat alias
        Route::get('/change-password', [LoginController::class, 'showForgotPasswordForm'])->name('admin.changepassword');
    });


    // ----------------------
    // Authenticated routes
    // ----------------------
    Route::middleware('auth')->group(function () {
        Route::get('/',          [LoginController::class, 'dashboard'])->name('admin.home');
        Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('admin.dashboard');

        Route::post('/update-password', [LoginController::class, 'updatePassword'])->name('admin.updatepassword');

        Route::match(['get', 'post'], '/logout', [LoginController::class, 'logout'])->name('admin.logout');

        // ---- Roles ----
        Route::get('roles',                [RoleController::class, 'index'])->middleware('permission:roles.view')->name('admin.roles.index');
        Route::get('roles/create',         [RoleController::class, 'create'])->middleware('permission:roles.create')->name('admin.roles.create');
        Route::post('roles',               [RoleController::class, 'store'])->middleware('permission:roles.create')->name('admin.roles.store');
        Route::get('roles/{role}/edit',    [RoleController::class, 'edit'])->middleware('permission:roles.edit')->name('admin.roles.edit');
        Route::put('roles/{role}',         [RoleController::class, 'update'])->middleware('permission:roles.edit')->name('admin.roles.update');
        Route::delete('roles/{role}',      [RoleController::class, 'destroy'])->middleware('permission:roles.delete')->name('admin.roles.destroy');

        // ---- Users ----
        Route::get('users',                [UserController::class, 'index'])->middleware('permission:users.view')->name('admin.users.index');
        Route::get('users/create',         [UserController::class, 'create'])->middleware('permission:users.create')->name('admin.users.create');
        Route::post('users',               [UserController::class, 'store'])->middleware('permission:users.create')->name('admin.users.store');
        Route::get('users/{user}/edit',    [UserController::class, 'edit'])->middleware('permission:users.edit')->name('admin.users.edit');
        Route::put('users/{user}',         [UserController::class, 'update'])->middleware('permission:users.edit')->name('admin.users.update');
        Route::delete('users/{user}',      [UserController::class, 'destroy'])->middleware('permission:users.delete')->name('admin.users.destroy');

        // ---- Permissions (per-role matrix) ----
        Route::get('permissions',                  [PermissionController::class, 'index'])->middleware('permission:permissions.view')->name('admin.permissions.index');
        Route::get('permissions/{role}/edit',      [PermissionController::class, 'edit'])->middleware('permission:permissions.assign')->name('admin.permissions.edit');
        Route::put('permissions/{role}',           [PermissionController::class, 'update'])->middleware('permission:permissions.assign')->name('admin.permissions.update');

        // ---- Permission catalog (add new permissions when new tabs appear) ----
        Route::get('permissions-catalog',                          [PermissionController::class, 'manage'])->middleware('permission:permissions.assign')->name('admin.permissions.manage');
        Route::get('permissions-catalog/create',                   [PermissionController::class, 'createPermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.create');
        Route::post('permissions-catalog',                         [PermissionController::class, 'storePermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.store');
        Route::get('permissions-catalog/{permission}/edit',        [PermissionController::class, 'editPermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.edit');
        Route::put('permissions-catalog/{permission}',             [PermissionController::class, 'updatePermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.update');
        Route::delete('permissions-catalog/{permission}',          [PermissionController::class, 'destroyPermission'])->middleware('permission:permissions.assign')->name('admin.permissions.manage.destroy');

        // ---- Activity Log (Super Admin only; gated inside the controller) ----
        Route::get('activity-logs',      [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
        Route::get('activity-logs/{id}', [ActivityLogController::class, 'show'])->whereNumber('id')->name('admin.activity-logs.show');

        // ---- Communication Log (mail/SMS delivery record; Super Admin only) ----
        Route::get('communication-logs',        [CommunicationLogController::class, 'index'])->name('admin.communication-logs.index');
        Route::post('communication-logs/filter', [CommunicationLogController::class, 'filter'])->name('admin.communication-logs.filter');
        Route::get('communication-logs/{id}',   [CommunicationLogController::class, 'show'])->whereNumber('id')->name('admin.communication-logs.show');
    });


    // ----------------------
    // 🔹 Backend (Admin Panel) Routes
    // ----------------------


    Route::prefix('')
        ->middleware(['auth:web', PreventBackHistoryMiddleware::class])
        ->group(function () {


            // ----------------------------------------------------------------
            // Permission-gated resource helper.
            // Splits a REST resource into per-action permission checks:
            //   {prefix}.view   -> index / show
            //   {prefix}.create -> create / store
            //   {prefix}.edit   -> edit / update
            //   {prefix}.delete -> destroy
            // create/store are registered before show so /{uri}/create isn't
            // captured by the /{uri}/{id} (show) route.
            // ----------------------------------------------------------------
            $crud = function (string $uri, string $controller, string $perm): void {
                Route::resource($uri, $controller)->only(['create', 'store'])->middleware("permission:{$perm}.create");
                Route::resource($uri, $controller)->only(['index'])->middleware("permission:{$perm}.view");
                Route::resource($uri, $controller)->only(['edit', 'update'])->middleware("permission:{$perm}.edit");
                Route::resource($uri, $controller)->only(['show'])->middleware("permission:{$perm}.view");
                Route::resource($uri, $controller)->only(['destroy'])->middleware("permission:{$perm}.delete");
            };

            // Home page sections
            $crud('banner-details',      HomeBannerController::class,        'home-banners');
            $crud('short-introduction',  ShortIntroductionController::class, 'home-short-intro');
            $crud('home-services',       HomeServicesController::class,      'home-specialities');
            $crud('manage-facilities',   HomeFacilitiesController::class,    'home-facilities');
            $crud('home-team',           HomeTeamController::class,          'home-our-team');
            $crud('manage-testimonials', HomeTestimonialsController::class,  'home-testimonials');
            $crud('manage-board',        HomeBoardController::class,         'home-board');
            $crud('manage-follow-us',    HomeFollowUsController::class,      'home-follow-us');

            //Our Team Master
            Route::post('manage-our-team/{id}/toggle-home', [MasterOurTeamController::class, 'toggleHome'])->middleware('permission:our-team.edit')->name('manage-our-team.toggle-home');
            $crud('manage-our-team', MasterOurTeamController::class, 'our-team');

            // Gallleryyyyy
            Route::post('manage-gallery/{id}/toggle-home', [GalleryController::class, 'toggleHome'])->middleware('permission:gallery.edit')->name('manage-gallery.toggle-home');
            $crud('manage-gallery', GalleryController::class, 'gallery');

            // Eventssss
            Route::post('manage-events/{id}/toggle-home', [EventsController::class, 'toggleHome'])->middleware('permission:events.edit')->name('manage-events.toggle-home');
            $crud('manage-events', EventsController::class, 'events');

            // Specialities
            $crud('manage-specialities', SpecialitiesController::class,        'specialities');
            $crud('speciality-details',  SpecialitiesDetailsController::class, 'speciality-details');

            //Testimonials Master
            $crud('manage-master-testimonials', MasterTestimonialsController::class, 'testimonials');

            //Our Facilities
            $crud('manage-master-facilities', MasterFacilitiesController::class, 'master-facilities');

            // About Us
            $crud('manage-about-us', AboutUsController::class, 'about-us');

            // Join Us
            $crud('manage-join-page', JoinPageController::class, 'join-page');
            $crud('manage-job-role',  JobRoleController::class,  'job-roles');

            // FAQ's
            $crud('manage-faqs', FAQController::class, 'faqs');

            // Blog
            $crud('manage-blog-category', BlogCategoryController::class, 'blog-categories');
            $crud('manage-blogs-listing', BlogListingController::class,  'blog-listings');
            $crud('manage-blog-details',  BlogDetailsController::class,   'blog-details');

            // Blog comments — read-only + toggle-active + soft-delete
            Route::get('manage-blog-comments',                  [BlogCommentController::class, 'index'])->middleware('permission:blog-comments.view')->name('manage-blog-comments.index');
            Route::patch('manage-blog-comments/{id}/toggle',    [BlogCommentController::class, 'toggleActive'])->middleware('permission:blog-comments.edit')->whereNumber('id')->name('manage-blog-comments.toggle');
            Route::delete('manage-blog-comments/{id}',          [BlogCommentController::class, 'destroy'])->middleware('permission:blog-comments.delete')->whereNumber('id')->name('manage-blog-comments.destroy');

            // Form Enquiries (read-only: user submissions kept intact, no delete).
            Route::get('manage-contact-enquiries',      [ContactEnquiryController::class, 'index'])->middleware('permission:contact-enquiries.view')->name('manage-contact-enquiries.index');
            Route::get('manage-contact-enquiries/{id}', [ContactEnquiryController::class, 'show'])->middleware('permission:contact-enquiries.view')->whereNumber('id')->name('manage-contact-enquiries.show');

            Route::get('manage-job-applications',       [JobApplicationController::class, 'index'])->middleware('permission:job-applications.view')->name('manage-job-applications.index');
            Route::get('manage-job-applications/{id}',  [JobApplicationController::class, 'show'])->middleware('permission:job-applications.view')->whereNumber('id')->name('manage-job-applications.show');

            Route::get('manage-appointment-enquiries',      [AppointmentEnquiryController::class, 'index'])->middleware('permission:appointment-enquiries.view')->name('manage-appointment-enquiries.index');
            Route::get('manage-appointment-enquiries/{id}', [AppointmentEnquiryController::class, 'show'])->middleware('permission:appointment-enquiries.view')->whereNumber('id')->name('manage-appointment-enquiries.show');

            // ---- Appointments module ----
            // Appointment Users (clients) — read-only list + full per-client history
            Route::get('manage-appointment-users',      [AppointmentUserController::class, 'index'])->middleware('permission:appointment-users.view')->name('manage-appointment-users.index');
            Route::get('manage-appointment-users/{id}', [AppointmentUserController::class, 'show'])->middleware('permission:appointment-users.view')->whereNumber('id')->name('manage-appointment-users.show');

            // Appointments — filters, status management, CSV export
            Route::get('manage-appointments',                 [AppointmentController::class, 'index'])->middleware('permission:appointments.view')->name('manage-appointments.index');
            Route::post('manage-appointments/filter',         [AppointmentController::class, 'filter'])->middleware('permission:appointments.view')->name('manage-appointments.filter');
            Route::get('manage-appointments/export',          [AppointmentController::class, 'export'])->middleware('permission:appointments.view')->name('manage-appointments.export');
            Route::get('manage-appointments/{id}',            [AppointmentController::class, 'show'])->middleware('permission:appointments.view')->whereNumber('id')->name('manage-appointments.show');
            Route::post('manage-appointments/{id}/status',    [AppointmentController::class, 'updateStatus'])->middleware('permission:appointments.edit')->whereNumber('id')->name('manage-appointments.update-status');

            // Appointment Status master (dropdown source)
            $crud('manage-appointment-statuses', AppointmentStatusController::class, 'appointment-statuses');

            // ---- Reports (CSV export; filters are AJAX POST) ----
            // Landing lives at /reports/overview so its URL isn't a prefix of the
            // other report URLs (the theme's active-link JS matches by substring).
            Route::get('reports/overview', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('admin.reports.index');
            Route::get('reports', fn () => redirect()->route('admin.reports.index'));

            Route::get ('reports/appointments',        [ReportController::class, 'appointments'])->middleware('permission:reports.view')->name('admin.reports.appointments');
            Route::post('reports/appointments/filter', [ReportController::class, 'appointmentsFilter'])->middleware('permission:reports.view')->name('admin.reports.appointments.filter');
            Route::get ('reports/appointments/export', [ReportController::class, 'appointmentsExport'])->middleware('permission:reports.view')->name('admin.reports.appointments.export');

            Route::get ('reports/operational',        [ReportController::class, 'operational'])->middleware('permission:reports.view')->name('admin.reports.operational');
            Route::post('reports/operational/filter', [ReportController::class, 'operationalFilter'])->middleware('permission:reports.view')->name('admin.reports.operational.filter');
            Route::get ('reports/operational/export', [ReportController::class, 'operationalExport'])->middleware('permission:reports.view')->name('admin.reports.operational.export');

            Route::get ('reports/clients',        [ReportController::class, 'clients'])->middleware('permission:reports.view')->name('admin.reports.clients');
            Route::post('reports/clients/filter', [ReportController::class, 'clientsFilter'])->middleware('permission:reports.view')->name('admin.reports.clients.filter');
            Route::get ('reports/clients/export', [ReportController::class, 'clientsExport'])->middleware('permission:reports.view')->name('admin.reports.clients.export');

            // Communication report stays additionally hidden to non-superadmins in the sidebar.
            Route::get ('reports/communication',        [ReportController::class, 'communication'])->middleware('permission:reports.view')->name('admin.reports.communication');
            Route::post('reports/communication/filter', [ReportController::class, 'communicationFilter'])->middleware('permission:reports.view')->name('admin.reports.communication.filter');
            Route::get ('reports/communication/export', [ReportController::class, 'communicationExport'])->middleware('permission:reports.view')->name('admin.reports.communication.export');

            // Contact Us
            $crud('manage-contact-details', ContactDetailsController::class, 'contact-details');

            // Privacy Policy
            $crud('manage-privacy-policy', PrivacyPolicyController::class, 'privacy-policy');

            // Flyer
            Route::post('manage-flyer/{id}/toggle-status', [FlyerController::class, 'toggleStatus'])->middleware('permission:flyer.edit')->name('manage-flyer.toggle-status');
            $crud('manage-flyer', FlyerController::class, 'flyer');
    });


    // ----------------------
    // 🔹 Frontend Routes
    // ----------------------

    Route::get('/', [HomeController::class, 'index'])->name('frontend.index');
    Route::get('/gallery', [HomeController::class, 'gallery'])->name('frontend.gallery');
    Route::get('/events', [HomeController::class, 'events'])->name('frontend.events');
    Route::get('/specialities', [HomeController::class, 'specialities'])->name('frontend.specialities');
    Route::get('/specialities/{slug}', [HomeController::class, 'specialities_details'])->name('frontend.specialities_details');
    Route::get('/faqs', [HomeController::class, 'faqs'])->name('frontend.faqs');
    Route::get('/facilities', [HomeController::class, 'our_facilities'])->name('frontend.our_facilities');
    Route::get('/team', [HomeController::class, 'our_team'])->name('frontend.our_team');
    Route::get('/about-us', [HomeController::class, 'about_us'])->name('frontend.about_us');
    Route::get('/join-us', [HomeController::class, 'join_us'])->name('frontend.join_us');
    Route::get('/contact-us', [HomeController::class, 'contact_us'])->name('frontend.contact_us');
    Route::post('/contact-us/enquiry', [HomeController::class, 'contact_enquiry_store'])->name('frontend.contact_enquiry.store');
    Route::get('/thank-you', [HomeController::class, 'thank_you'])->name('frontend.thank_you');
    Route::get('/coming-soon', [HomeController::class, 'coming_soon'])->name('frontend.coming_soon');
    Route::get('/user-login', [HomeController::class, 'user_login'])->name('frontend.user_login');
    Route::post('/user-login/send-otp', [HomeController::class, 'send_otp'])->middleware('throttle:6,10')->name('frontend.send_otp');
    Route::post('/user-login/verify-otp', [HomeController::class, 'verify_otp'])->middleware('throttle:10,10')->name('frontend.verify_otp');
    Route::get('/book-an-appointment', [HomeController::class, 'book_an_appointment'])->name('frontend.book_an_appointment');
    Route::post('/book-an-appointment/submit', [HomeController::class, 'appointment_store'])->middleware('throttle:6,10')->name('frontend.appointment_store');
    Route::get('/appointment-thank-you', [HomeController::class, 'appointment_thank_you'])->name('frontend.appointment_thank_you');
    Route::post('/careers/apply', [HomeController::class, 'job_application_store'])->name('frontend.job_application.store');
    Route::get('/join-thank-you', [HomeController::class, 'join_thank_you'])->name('frontend.join_thank_you');    Route::get('/blog', [HomeController::class, 'blogs'])->name('frontend.blogs');
    Route::get('/blog/{slug}', [HomeController::class, 'blog_details'])->name('frontend.blog_details');
    Route::post('/blog/{slug}/comment', [HomeController::class, 'blog_comment_store'])->middleware('throttle:3,10')->name('frontend.blog_comment.store');

