<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentEnquiry extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'appointment_enquiries';

    protected $fillable = [
        'appointment_user_id',
        'owner_name',
        'mobile',
        'email',
        'address',
        'pincode',
        'pet_name',
        'pet_age',
        'pet_type',
        'pet_gender',
        'consult_type',
        'reason',
        'appointment_date',
        'appointment_status_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];

    public function appointmentUser(): BelongsTo
    {
        return $this->belongsTo(AppointmentUser::class, 'appointment_user_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(AppointmentStatus::class, 'appointment_status_id');
    }

    /** Full status-change trail for this appointment, newest first. */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(AppointmentStatusHistory::class, 'appointment_enquiry_id')
            ->orderByDesc('id');
    }
}
