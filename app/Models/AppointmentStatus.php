<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentStatus extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'appointment_statuses';

    protected $fillable = [
        'name',
        'slug',
        'color',
        'sort_order',
        'is_active',
        'is_default',
        'requires_appointment_date',
        'sms_trigger',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active'                 => 'boolean',
        'is_default'                => 'boolean',
        'requires_appointment_date' => 'boolean',
        'sort_order'                => 'integer',
    ];

    /** Appointments currently sitting at this status. */
    public function appointments(): HasMany
    {
        return $this->hasMany(AppointmentEnquiry::class, 'appointment_status_id');
    }
}
