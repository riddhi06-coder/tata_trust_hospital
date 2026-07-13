<?php

namespace App\Support;

use App\Models\CommunicationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Central writer for the mail/SMS communication log. Never throws — a logging
 * failure must never break the actual send or the request.
 *
 * Accepts a `related` Eloquent model to auto-fill related_type/related_id,
 * and falls back to the authenticated user for the "triggered_by" fields.
 */
class CommunicationLogger
{
    public static function log(array $data): void
    {
        try {
            $related = $data['related'] ?? null;

            CommunicationLog::create([
                'channel'             => $data['channel'],
                'type'                => $data['type'],
                'recipient'           => $data['recipient'],
                'recipient_name'      => $data['recipient_name'] ?? null,
                'subject'             => $data['subject'] ?? null,
                'message'             => isset($data['message']) ? Str::limit((string) $data['message'], 2000, '') : null,
                'status'              => $data['status'],
                'error'               => isset($data['error']) ? Str::limit((string) $data['error'], 2000, '') : null,
                'provider_response'   => isset($data['provider_response']) ? Str::limit((string) $data['provider_response'], 2000, '') : null,
                'related_type'        => $related instanceof Model ? get_class($related) : ($data['related_type'] ?? null),
                'related_id'          => $related instanceof Model ? $related->getKey() : ($data['related_id'] ?? null),
                'appointment_user_id' => $data['appointment_user_id'] ?? null,
                'triggered_by'        => $data['triggered_by'] ?? Auth::id(),
                'triggered_by_name'   => $data['triggered_by_name'] ?? optional(Auth::user())->name,
                'created_at'          => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('CommunicationLogger failed: '.$e->getMessage());
        }
    }
}
