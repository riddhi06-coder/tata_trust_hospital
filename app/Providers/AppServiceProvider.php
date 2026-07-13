<?php

namespace App\Providers;

use App\Observers\AuditObserver;
use App\Support\ActivityLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Every model whose activity should be recorded in the audit trail. */
    private array $auditedModels = [
        \App\Models\AboutUs::class,
        \App\Models\AppointmentStatus::class,
        \App\Models\EventSetting::class,
        \App\Models\Events::class,
        \App\Models\FacilitySetting::class,
        \App\Models\Faq::class,
        \App\Models\FaqSetting::class,
        \App\Models\Gallery::class,
        \App\Models\GalleryImage::class,
        \App\Models\HomeBanner::class,
        \App\Models\HomeBoard::class,
        \App\Models\HomeFacilities::class,
        \App\Models\HomeFollowUs::class,
        \App\Models\HomeServices::class,
        \App\Models\HomeTeam::class,
        \App\Models\HomeTestimonials::class,
        \App\Models\MasterFacility::class,
        \App\Models\OurTeam::class,
        \App\Models\OurTeamSetting::class,
        \App\Models\Permission::class,
        \App\Models\Role::class,
        \App\Models\ShortIntroduction::class,
        \App\Models\Specialities::class,
        \App\Models\SpecialitiesDetails::class,
        \App\Models\SpecialitySetting::class,
        \App\Models\Testimonials::class,
        \App\Models\User::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Attach the audit observer to every tracked model.
        foreach ($this->auditedModels as $model) {
            $model::observe(AuditObserver::class);
        }

        // Authentication activity.
        Event::listen(Login::class, function (Login $event) {
            ActivityLogger::log('login', null, [
                'module'      => 'Authentication',
                'user_id'     => $event->user->getAuthIdentifier(),
                'user_name'   => $event->user->name ?? null,
                'description' => 'Logged in',
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            ActivityLogger::log('logout', null, [
                'module'      => 'Authentication',
                'user_id'     => optional($event->user)->getAuthIdentifier(),
                'user_name'   => optional($event->user)->name,
                'description' => 'Logged out',
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            $email = $event->credentials['email'] ?? 'unknown';
            ActivityLogger::log('login_failed', null, [
                'module'      => 'Authentication',
                'user_id'     => null,
                'user_name'   => $email,
                'description' => 'Failed login attempt for '.$email,
            ]);
        });
    }
}
