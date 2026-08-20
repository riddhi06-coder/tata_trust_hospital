<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Per-sender chatbot state for the reactive WhatsApp flow.
 * `step` tracks where the user is in a multi-step flow (e.g. lead capture);
 * `data` holds any fields collected along the way.
 */
class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'wa_id',
        'name',
        'step',
        'data',
        'last_message_at',
    ];

    protected $casts = [
        'data'            => 'array',
        'last_message_at' => 'datetime',
    ];
}
