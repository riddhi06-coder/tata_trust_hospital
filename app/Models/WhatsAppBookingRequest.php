<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A completed appointment-booking intake collected via the WhatsApp chatbot.
 */
class WhatsAppBookingRequest extends Model
{
    protected $table = 'whatsapp_booking_requests';

    protected $fillable = [
        'wa_id', 'client_type',
        'parent_name', 'address', 'email', 'mobile', 'pincode',
        'pet_name', 'species', 'sex', 'breed', 'colour', 'dob_age', 'weight',
        'neutered', 'complaint', 'how_heard', 'referred_by',
        'reason', 'preferred_day', 'preferred_time',
        'status', 'converted_at',
    ];

    protected $casts = [
        'converted_at' => 'datetime',
    ];

    /** Public-facing reference, e.g. #B-0042. */
    public function reference(): string
    {
        return 'B-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }
}
