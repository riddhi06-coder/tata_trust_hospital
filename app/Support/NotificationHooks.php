<?php

namespace App\Support;

use App\Models\AdminNotification;
use App\Models\AppointmentEnquiry;
use App\Models\ContactEnquiry;
use App\Models\JobApplication;
use App\Models\WhatsAppBookingRequest;
use App\Models\WhatsAppTeamQuery;
use Illuminate\Support\Str;

/**
 * Registers model "created" listeners that raise a backend AdminNotification
 * whenever a new enquiry / query / appointment arrives — from the website or
 * WhatsApp. Centralised here so the source controllers/bot stay untouched.
 */
class NotificationHooks
{
    public static function register(): void
    {
        ContactEnquiry::created(function (ContactEnquiry $m) {
            AdminNotification::raise([
                'type'    => 'contact_enquiry',
                'source'  => 'website',
                'title'   => 'New Contact Enquiry',
                'body'    => trim(($m->full_name ?: 'Someone').($m->subject ? ' — '.$m->subject : '')),
                'url'     => route('manage-contact-enquiries.show', $m->id),
                'icon'    => 'mail',
                'color'   => 'info',
                'related_type' => ContactEnquiry::class,
                'related_id'   => $m->id,
            ]);
        });

        AppointmentEnquiry::created(function (AppointmentEnquiry $m) {
            $when = $m->appointment_date ? ' on '.\Illuminate\Support\Carbon::parse($m->appointment_date)->format('d M Y') : '';
            AdminNotification::raise([
                'type'    => 'appointment_enquiry',
                'source'  => 'website',
                'title'   => 'New Appointment Booking',
                'body'    => trim(($m->owner_name ?: 'A pet parent').($m->pet_name ? ' booked for '.$m->pet_name : ' booked a visit').$when),
                'url'     => route('manage-appointment-enquiries.show', $m->id),
                'icon'    => 'calendar',
                'color'   => 'success',
                'related_type' => AppointmentEnquiry::class,
                'related_id'   => $m->id,
            ]);
        });

        JobApplication::created(function (JobApplication $m) {
            AdminNotification::raise([
                'type'    => 'job_application',
                'source'  => 'website',
                'title'   => 'New Job Application',
                'body'    => trim(($m->full_name ?: 'An applicant').($m->applying_for ? ' — '.$m->applying_for : '')),
                'url'     => route('manage-job-applications.show', $m->id),
                'icon'    => 'briefcase',
                'color'   => 'warning',
                'related_type' => JobApplication::class,
                'related_id'   => $m->id,
            ]);
        });

        WhatsAppBookingRequest::created(function (WhatsAppBookingRequest $m) {
            AdminNotification::raise([
                'type'    => 'whatsapp_booking',
                'source'  => 'whatsapp',
                'title'   => 'New WhatsApp Booking',
                'body'    => trim(($m->parent_name ?: 'A pet parent').($m->pet_name ? ' — '.$m->pet_name : '').' requested an appointment'),
                'url'     => route('manage-whatsapp-bookings.show', $m->id),
                'icon'    => 'message-circle',
                'color'   => 'success',
                'related_type' => WhatsAppBookingRequest::class,
                'related_id'   => $m->id,
            ]);
        });

        WhatsAppTeamQuery::created(function (WhatsAppTeamQuery $m) {
            AdminNotification::raise([
                'type'    => 'whatsapp_query',
                'source'  => 'whatsapp',
                'title'   => 'New WhatsApp Query',
                'body'    => trim(($m->name ?: 'A pet parent').': '.Str::limit((string) $m->message, 80)),
                'url'     => route('manage-whatsapp-queries.show', $m->id),
                'icon'    => 'message-circle',
                'color'   => 'info',
                'related_type' => WhatsAppTeamQuery::class,
                'related_id'   => $m->id,
            ]);
        });
    }
}
