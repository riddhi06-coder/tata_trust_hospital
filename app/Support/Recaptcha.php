<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Server-side Google reCAPTCHA v2 verification. Used by the contact + appointment
 * forms. If no secret key is configured, verification is skipped (returns true)
 * so local/dev environments without keys aren't blocked.
 */
class Recaptcha
{
    public static function verify(?string $response, ?string $ip = null): bool
    {
        $secret = config('services.recaptcha.secret_key');

        // No key configured -> don't block (e.g. local dev).
        if (empty($secret)) {
            return true;
        }

        if (empty($response)) {
            return false;
        }

        try {
            $result = Http::asForm()->timeout(10)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secret,
                'response' => $response,
                'remoteip' => $ip,
            ]);

            return (bool) ($result->json('success') === true);
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA verification failed: '.$e->getMessage());
            // On a network error to Google, don't hard-block legitimate users.
            return true;
        }
    }
}
