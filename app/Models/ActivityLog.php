<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    // Logs are immutable append-only records; only created_at is used.
    public $timestamps = false;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'event',
        'module',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'changed_fields',
        'ip_address',
        'method',
        'url',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values'     => 'array',
        'new_values'     => 'array',
        'changed_fields' => 'array',
        'created_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Bootstrap-flavoured badge class for the event. */
    public function eventBadgeClass(): string
    {
        return match ($this->event) {
            'created'      => 'bg-success',
            'updated'      => 'bg-primary',
            'deleted'      => 'bg-danger',
            'restored'     => 'bg-info',
            'login'        => 'bg-secondary',
            'logout'       => 'bg-dark',
            'login_failed' => 'bg-warning',
            default        => 'bg-secondary',
        };
    }
}
