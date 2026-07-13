<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentStatusHistory extends Model
{
    // Append-only audit record for appointment status changes.
    public $timestamps = false;

    protected $table = 'appointment_status_histories';

    protected $fillable = [
        'appointment_enquiry_id',
        'from_status_id',
        'to_status_id',
        'note',
        'changed_by',
        'changed_by_name',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function enquiry(): BelongsTo
    {
        return $this->belongsTo(AppointmentEnquiry::class, 'appointment_enquiry_id');
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'to_status_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
