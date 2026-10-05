<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * A single backend activity notification (shared inbox across admins).
 */
class AdminNotification extends Model
{
    protected $table = 'admin_notifications';

    protected $fillable = [
        'type', 'source', 'title', 'body', 'url', 'icon', 'color',
        'related_type', 'related_id', 'read_at', 'read_by', 'read_by_name',
        'last_reminded_at', 'reminder_count',
    ];

    protected $casts = [
        'read_at'          => 'datetime',
        'last_reminded_at' => 'datetime',
    ];

    /** Create a notification. Never throws — a logging failure must not break the request. */
    public static function raise(array $data): ?self
    {
        try {
            return static::create(array_merge([
                'source' => 'website',
                'icon'   => 'bell',
                'color'  => 'primary',
            ], $data));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('AdminNotification push failed: '.$e->getMessage());
            return null;
        }
    }

    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }

    public function markRead(): void
    {
        if ($this->read_at) {
            return;
        }
        $this->forceFill([
            'read_at'      => now(),
            'read_by'      => Auth::id(),
            'read_by_name' => optional(Auth::user())->name,
        ])->save();
    }

    /** Human-friendly "2 minutes ago". */
    public function getAgoAttribute(): string
    {
        return optional($this->created_at)->diffForHumans() ?? '';
    }
}
