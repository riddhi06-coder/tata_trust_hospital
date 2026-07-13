<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Central writer for the audit trail. Never throws — a logging failure must
 * never break the actual request.
 */
class ActivityLogger
{
    /** Friendly module labels keyed by model class basename. */
    private const MODULES = [
        'HomeBanner'          => 'Home Banner',
        'ShortIntroduction'   => 'Short Introduction',
        'HomeServices'        => 'Home Services',
        'HomeFacilities'      => 'Home Facilities',
        'HomeTeam'            => 'Home Team',
        'HomeTestimonials'    => 'Home Testimonials',
        'HomeBoard'           => 'Home Board',
        'HomeFollowUs'        => 'Home Follow Us',
        'OurTeam'             => 'Team Member',
        'OurTeamSetting'      => 'Our Team Settings',
        'Testimonials'        => 'Testimonial',
        'Gallery'             => 'Gallery',
        'GalleryImage'        => 'Gallery Image',
        'Events'              => 'Event',
        'EventSetting'        => 'Event Settings',
        'Specialities'        => 'Speciality',
        'SpecialitiesDetails' => 'Speciality Details',
        'SpecialitySetting'   => 'Speciality Settings',
        'Faq'                 => 'FAQ',
        'FaqSetting'          => 'FAQ Settings',
        'MasterFacility'      => 'Facility',
        'FacilitySetting'     => 'Facility Settings',
        'AboutUs'             => 'About Us',
        'AppointmentStatus'   => 'Appointment Status',
        'Role'                => 'Role',
        'Permission'          => 'Permission',
        'User'                => 'User',
    ];

    public static function log(string $event, ?Model $model = null, array $data = []): void
    {
        try {
            $request = request();

            ActivityLog::create([
                'user_id'        => $data['user_id']   ?? Auth::id(),
                'user_name'      => $data['user_name'] ?? optional(Auth::user())->name ?? 'System',
                'event'          => $event,
                'module'         => $data['module'] ?? ($model ? self::moduleLabel($model) : null),
                'auditable_type' => $model ? get_class($model) : null,
                'auditable_id'   => $model?->getKey(),
                'description'    => $data['description'] ?? null,
                'old_values'     => $data['old_values'] ?? null,
                'new_values'     => $data['new_values'] ?? null,
                'changed_fields' => $data['changed_fields'] ?? null,
                'ip_address'     => $request?->ip(),
                'method'         => $request?->method(),
                'url'            => $request ? Str::limit($request->fullUrl(), 2000, '') : null,
                'user_agent'     => $request ? Str::limit((string) $request->userAgent(), 2000, '') : null,
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('ActivityLogger failed: '.$e->getMessage());
        }
    }

    public static function moduleLabel(Model $model): string
    {
        $base = class_basename($model);

        return self::MODULES[$base] ?? Str::headline($base);
    }
}
