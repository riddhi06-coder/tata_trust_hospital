<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    // Append-only log — only created_at is used.
    public $timestamps = false;

    protected $table = 'communication_logs';

    protected $fillable = [
        'channel',
        'type',
        'recipient',
        'recipient_name',
        'subject',
        'message',
        'status',
        'error',
        'provider_response',
        'related_type',
        'related_id',
        'appointment_user_id',
        'triggered_by',
        'triggered_by_name',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function appointmentUser(): BelongsTo
    {
        return $this->belongsTo(AppointmentUser::class, 'appointment_user_id');
    }

    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /** Human-friendly label for the message type. */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'otp'                        => 'OTP',
            'appointment_confirmation'   => 'Appointment Confirmation',
            'appointment_cancellation'   => 'Appointment Cancellation',
            'appointment_reschedule'     => 'Appointment Reschedule',
            'appointment_owner'          => 'Appointment (Owner)',
            'appointment_admin'          => 'Appointment (Admin)',
            'contact_enquiry_user'       => 'Contact Enquiry (User)',
            'contact_enquiry_admin'      => 'Contact Enquiry (Admin)',
            'job_application_user'       => 'Job Application (User)',
            'job_application_admin'      => 'Job Application (Admin)',
            default                      => ucwords(str_replace('_', ' ', $this->type)),
        };
    }

    public function statusBadgeClass(): string
    {
        return $this->status === 'sent' ? 'bg-success' : 'bg-danger';
    }

    public function channelBadgeClass(): string
    {
        return $this->channel === 'sms' ? 'bg-info' : 'bg-primary';
    }
}
