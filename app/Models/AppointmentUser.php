<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentUser extends Model
{
    use SoftDeletes, TracksDeletedBy;

    protected $table = 'appointment_users';

    protected $fillable = [
        'mobile',
        'name',
        'email',
        'address',
        'pincode',
        'last_verified_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'last_verified_at' => 'datetime',
    ];

    /** Every appointment this client has booked, newest visit first. */
    public function appointments(): HasMany
    {
        return $this->hasMany(AppointmentEnquiry::class, 'appointment_user_id')
            ->whereNull('deleted_by')
            ->orderByDesc('appointment_date')
            ->orderByDesc('id');
    }
}
