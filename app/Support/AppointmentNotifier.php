<?php

namespace App\Support;

use App\Mail\AppointmentEnquiryMail;
use App\Models\AppointmentEnquiry;
use App\Services\MessageIndiaSms;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Shared "new appointment booked" notifier used by BOTH the website booking
 * form and the WhatsApp chatbot, so a WhatsApp booking runs the exact same
 * post-confirmation process (owner SMS, owner email, admin email + logging).
 *
 * Null-safe: WhatsApp bookings may omit email / date (team fills on the call),
 * so those notifications are simply skipped when the data isn't available.
 */
class AppointmentNotifier
{
    public static function notifyNewBooking(AppointmentEnquiry $enquiry, ?MessageIndiaSms $sms = null): void
    {
        $sms = $sms ?: app(MessageIndiaSms::class);

        $formattedDate = $enquiry->appointment_date
            ? $enquiry->appointment_date->format('d M Y')
            : null;

        $smsContext = [
            'recipient_name'      => $enquiry->owner_name,
            'related'             => $enquiry,
            'appointment_user_id' => $enquiry->appointment_user_id,
        ];

        // Tentative-booking SMS to the owner (needs a mobile + a date).
        if ($enquiry->mobile && $formattedDate) {
            try {
                $sms->sendAppointmentConfirmation($enquiry->mobile, $formattedDate, $smsContext);
            } catch (\Throwable $e) {
                Log::error('Appointment SMS failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
            }
        }

        $adminTo = config('mail.admin_notifications.appointment', config('mail.admin_notification'));
        $hasLogo = file_exists(public_path('frontend/assets/img/logo/tata-trust-logo.webp'));
        $dateText = $formattedDate ?: 'a date to be confirmed';

        // Owner acknowledgement email (only if we have an email on file).
        if (! empty($enquiry->email)) {
            try {
                Mail::to($enquiry->email)->send(new AppointmentEnquiryMail(
                    $enquiry,
                    'Appointment Request Received - SAHM',
                    'Thank you, '.e($enquiry->owner_name).'!',
                    'We have received your appointment request. Our team will contact you shortly to confirm your visit. Details of your booking are below.',
                    'Please note: this is a tentative booking. Our Customer Care Department will call you to confirm the date and time.',
                    $hasLogo
                ));
                CommunicationLogger::log([
                    'channel' => 'email', 'type' => 'appointment_owner', 'recipient' => $enquiry->email,
                    'recipient_name' => $enquiry->owner_name, 'subject' => 'Appointment Request Received - SAHM',
                    'message' => 'Owner acknowledgement for appointment on '.$dateText, 'status' => 'sent',
                    'related' => $enquiry, 'appointment_user_id' => $enquiry->appointment_user_id,
                ]);
            } catch (\Throwable $e) {
                Log::error('Appointment owner mail failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
            }
        }

        // Admin notification email.
        if ($adminTo) {
            try {
                $mail = (new AppointmentEnquiryMail(
                    $enquiry,
                    'New Appointment Enquiry - '.$enquiry->owner_name,
                    'New Appointment Enquiry',
                    'A new appointment request has been received ('.ucfirst((string) $enquiry->source).'). Details are below.',
                    '',
                    $hasLogo
                ));
                if (! empty($enquiry->email)) {
                    $mail->replyTo($enquiry->email, $enquiry->owner_name);
                }
                Mail::to($adminTo)->send($mail);
                CommunicationLogger::log([
                    'channel' => 'email', 'type' => 'appointment_admin', 'recipient' => $adminTo,
                    'subject' => 'New Appointment Enquiry - '.$enquiry->owner_name,
                    'message' => 'Admin notification for appointment on '.$dateText, 'status' => 'sent',
                    'related' => $enquiry, 'appointment_user_id' => $enquiry->appointment_user_id,
                ]);
            } catch (\Throwable $e) {
                Log::error('Appointment admin mail failed: '.$e->getMessage(), ['enquiry_id' => $enquiry->id]);
            }
        }
    }
}
