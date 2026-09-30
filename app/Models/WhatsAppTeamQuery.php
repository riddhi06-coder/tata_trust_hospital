<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A "Talk to our team" message captured via the WhatsApp chatbot.
 */
class WhatsAppTeamQuery extends Model
{
    protected $table = 'whatsapp_team_queries';

    protected $fillable = [
        'wa_id', 'name', 'message', 'status',
    ];
}
