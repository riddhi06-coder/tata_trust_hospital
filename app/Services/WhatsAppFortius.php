<?php

namespace App\Services;

use App\Support\CommunicationLogger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the Fortius WhatsApp API (waba.fortius.in.net).
 *
 * Fortius is a transparent proxy in front of Meta's WhatsApp Cloud API, so the
 * endpoint and JSON payloads follow Meta's Cloud API format exactly:
 *   POST {base_url}/{version}/{phoneNumberId}/messages   (Authorization: Bearer <token>)
 *
 * Every send (success or failure) is recorded in the communication log, mirroring
 * App\Services\MessageIndiaSms. All methods here send FREE-FORM messages, which are
 * only valid inside the 24-hour customer-service window (i.e. after the user messages
 * us). Business-initiated / >24h messages must use approved templates — added later.
 */
class WhatsAppFortius
{
    /** Plain text message (with link preview). */
    public function sendText(string $to, string $body, array $context = []): bool
    {
        return $this->send($to, [
            'type' => 'text',
            'text' => ['preview_url' => true, 'body' => $body],
        ], 'text', $body, $context);
    }

    /** Image message by public URL (JPEG/PNG only — WhatsApp does not accept webp). */
    public function sendImage(string $to, string $link, ?string $caption = null, array $context = []): bool
    {
        $image = ['link' => $link];
        if ($caption !== null && $caption !== '') {
            $image['caption'] = $caption;
        }

        return $this->send($to, ['type' => 'image', 'image' => $image], 'image', $caption, $context);
    }

    /**
     * Interactive reply buttons (max 3).
     * @param array $buttons e.g. [['id' => 'menu_book', 'title' => 'Book Appointment'], ...]
     */
    public function sendButtons(string $to, string $body, array $buttons, ?string $header = null, array $context = []): bool
    {
        $interactive = [
            'type'   => 'button',
            'body'   => ['text' => $body],
            'action' => [
                'buttons' => array_map(fn ($b) => [
                    'type'  => 'reply',
                    'reply' => ['id' => $b['id'], 'title' => mb_substr($b['title'], 0, 20)],
                ], array_slice($buttons, 0, 3)),
            ],
        ];

        if ($header) {
            $interactive['header'] = ['type' => 'text', 'text' => $header];
        }

        return $this->send($to, ['type' => 'interactive', 'interactive' => $interactive], 'menu', $body, $context);
    }

    /**
     * Interactive single-select list (use when there are more than 3 options).
     * @param array $rows e.g. [['id' => 'menu_timings', 'title' => 'Clinic Timings', 'description' => '...'], ...]
     */
    public function sendList(string $to, string $body, string $buttonText, array $rows, ?string $header = null, array $context = []): bool
    {
        $interactive = [
            'type'   => 'list',
            'body'   => ['text' => $body],
            'action' => [
                'button'   => mb_substr($buttonText, 0, 20),
                'sections' => [[
                    'title' => 'Menu',
                    'rows'  => array_map(fn ($r) => array_filter([
                        'id'          => $r['id'],
                        'title'       => mb_substr($r['title'], 0, 24),
                        'description' => isset($r['description']) ? mb_substr($r['description'], 0, 72) : null,
                    ], fn ($v) => $v !== null), $rows),
                ]],
            ],
        ];

        if ($header) {
            $interactive['header'] = ['type' => 'text', 'text' => $header];
        }

        return $this->send($to, ['type' => 'interactive', 'interactive' => $interactive], 'menu', $body, $context);
    }

    /**
     * Shared sender. Builds the Meta-format payload, POSTs to Fortius, logs every
     * attempt, and returns true on API success.
     */
    private function send(string $to, array $message, string $type, ?string $logBody, array $context = []): bool
    {
        $cfg = config('services.whatsapp');
        $to  = $this->normalize($to);

        // Fail-safe: if creds aren't configured, log locally + to the DB rather than error out.
        if (empty($cfg['token']) || empty($cfg['phone_number_id'])) {
            Log::warning('WhatsApp credentials missing — message not sent', ['to' => $to, 'type' => $type]);
            $this->record($to, $logBody, $type, 'failed', 'WhatsApp credentials are not configured.', null, $context);
            return false;
        }

        $payload = array_merge([
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $to,
        ], $message);

        $url = rtrim($cfg['base_url'], '/').'/'.$cfg['version'].'/'.$cfg['phone_number_id'].'/messages';

        try {
            $response = Http::timeout(15)
                ->withToken($cfg['token'])
                ->acceptJson()
                ->post($url, $payload);

            if (! $response->successful()) {
                Log::error('WhatsApp send failed', ['to' => $to, 'type' => $type, 'status' => $response->status()]);
                $this->record($to, $logBody, $type, 'failed', 'Gateway responded with HTTP '.$response->status(), $response->body(), $context);
                return false;
            }

            $this->record($to, $logBody, $type, 'sent', null, $response->body(), $context);
            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send exception: '.$e->getMessage(), ['to' => $to, 'type' => $type]);
            $this->record($to, $logBody, $type, 'failed', $e->getMessage(), null, $context);
            return false;
        }
    }

    /**
     * Normalise a mobile number to WhatsApp's expected form: country-code + number,
     * digits only, no leading '+'. Assumes India (91) when no country code is present.
     */
    public function normalize(string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', $mobile);

        if (strlen($digits) === 10) {
            return '91'.$digits;                 // bare 10-digit Indian mobile
        }
        if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            return '91'.substr($digits, 1);      // leading 0 + 10 digits
        }

        return $digits;                          // already has a country code
    }

    /** Persist one WhatsApp send attempt to the communication log. */
    private function record(string $to, ?string $message, string $type, string $status, ?string $error, ?string $providerResponse, array $context): void
    {
        CommunicationLogger::log([
            'channel'             => 'whatsapp',
            'type'                => 'wa_'.$type,
            'recipient'           => $to,
            'recipient_name'      => $context['recipient_name'] ?? null,
            'message'             => $message,
            'status'              => $status,
            'error'               => $error,
            'provider_response'   => $providerResponse,
            'related'             => $context['related'] ?? null,
            'appointment_user_id' => $context['appointment_user_id'] ?? null,
            'triggered_by'        => $context['triggered_by'] ?? null,
        ]);
    }
}
