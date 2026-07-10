<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentOtp extends Model
{
    protected $table = 'appointment_otps';

    protected $fillable = [
        'mobile',
        'otp',
        'expires_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
