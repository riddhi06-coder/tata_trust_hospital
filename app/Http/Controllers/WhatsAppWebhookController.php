<?php

namespace App\Http\Controllers;

use App\Support\WhatsAppBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Inbound WhatsApp webhook (Fortius → us). Fortius forwards messages in Meta's
 * Cloud API webhook shape: entry[] → changes[] → value → messages[].
 *
 * This endpoint is public (no auth) and CSRF-exempt (see bootstrap/app.php).
 * It requires a public HTTPS URL, so it only functions on production/staging
 * (or via an ngrok tunnel) — not on local XAMPP.
 */
class WhatsAppWebhookController extends Controller
{
    /** Verification handshake (GET) — echo the challenge when the verify token matches. */
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        $expected = (string) config('services.whatsapp.verify_token');

        if ($mode === 'subscribe' && $expected !== '' && hash_equals($expected, (string) $token)) {
            return response((string) $challenge, 200);
        }

        return response('Forbidden', 403);
    }

    /** Inbound messages (POST). Always returns 200 quickly so the provider doesn't retry. */
    public function handle(Request $request, WhatsAppBot $bot)
    {
        $payload = $request->all();

        // TEMP (testing): log the raw inbound so we can confirm Fortius's exact
        // payload shape and adjust the parser if it differs from Meta's format.
        // Remove once the format is confirmed.
        Log::info('WhatsApp inbound payload', $payload);

        try {
            foreach ($payload['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    $value   = $change['value'] ?? [];
                    $profile = $value['contacts'][0]['profile']['name'] ?? null;

                    foreach ($value['messages'] ?? [] as $message) {
                        $from = $message['from'] ?? null;
                        if (! $from) {
                            continue;
                        }

                        [$text, $interactiveId] = $this->extract($message);
                        $bot->handle($from, $profile, $text, $interactiveId);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('WhatsApp webhook error: '.$e->getMessage());
        }

        return response()->json(['status' => 'ok']);
    }

    /** Pull the user's text and any interactive-selection id out of a message. */
    private function extract(array $m): array
    {
        return match ($m['type'] ?? '') {
            'text'        => [$m['text']['body'] ?? '', null],
            'interactive' => $this->extractInteractive($m['interactive'] ?? []),
            'button'      => [$m['button']['text'] ?? '', $m['button']['payload'] ?? null],
            default       => ['', null],
        };
    }

    private function extractInteractive(array $i): array
    {
        return match ($i['type'] ?? '') {
            'button_reply' => [$i['button_reply']['title'] ?? '', $i['button_reply']['id'] ?? null],
            'list_reply'   => [$i['list_reply']['title'] ?? '', $i['list_reply']['id'] ?? null],
            default        => ['', null],
        };
    }
}
