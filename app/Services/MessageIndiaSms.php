<?php

namespace App\Services;

use App\Support\CommunicationLogger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the MessageIndia SMS API.
 * Every attempt (success or failure) is recorded in the communication log.
 *
 * The optional $context array carries extra logging metadata:
 *   - recipient_name       (string)
 *   - related              (Eloquent model — sets related_type/related_id)
 *   - appointment_user_id  (int)
 *   - triggered_by         (int — admin user id; defaults to Auth::id())
 */
class MessageIndiaSms
{
    private string $endpoint = 'http://sms.messageindia.in/v2/sendSMS';

    public function sendOtp(string $mobile, string $otp, array $context = []): bool
    {
        $message = "{$otp} is your OTP for Small Animal Hospital Mumbai (SAHM) appointment booking. Thank you.";

        return $this->send($mobile, $message, config('services.messageindia.template_id'), 'otp', $context);
    }

    public function sendAppointmentConfirmation(string $mobile, string $formattedDate, array $context = []): bool
    {
        $message = "Your pet's tentative appointment with Small Animal Hospital Mumbai (SAHM) is scheduled for {$formattedDate}. Please note that this is a tentative booking. You will receive a confirmation call from our Customer Care Department shortly. For any assistance, please contact us at 022 65383538.";

        return $this->send($mobile, $message, config('services.messageindia.appointment_template_id', '1707177744709776264'), 'appointment_confirmation', $context);
    }

    public function sendAppointmentCancellation(string $mobile, string $formattedDate, array $context = []): bool
    {
        $message = "Your pet's appointment at Small Animal Hospital Mumbai (SAHM) for {$formattedDate} has been cancelled. Please contact us to reschedule.";

        return $this->send($mobile, $message, config('services.messageindia.cancellation_template_id', '1707172283922616212'), 'appointment_cancellation', $context);
    }

    public function sendAppointmentReschedule(string $mobile, string $oldDate, string $newDate, array $context = []): bool
    {
        $message = "Your pet's appointment with Small Animal Hospital Mumbai (SAHM) has been rescheduled from {$oldDate} to {$newDate}.";

        return $this->send($mobile, $message, config('services.messageindia.reschedule_template_id', '1707172283932407122'), 'appointment_reschedule', $context);
    }

    /**
     * Shared transactional-SMS sender. Logs every attempt and returns
     * true on API success, false otherwise.
     */
    private function send(string $mobile, string $message, ?string $templateId, string $type, array $context = []): bool
    {
        $cfg = config('services.messageindia');

        // Fail-safe: if creds aren't configured, log locally + to the DB rather than error out.
        if (empty($cfg['username']) || empty($cfg['api_key'])) {
            Log::warning('MessageIndia SMS credentials missing — SMS not sent', ['mobile' => $mobile, 'type' => $type]);
            $this->record($mobile, $message, $type, 'failed', 'SMS gateway credentials are not configured.', null, $context);
            return false;
        }

        try {
            $response = Http::timeout(15)
                ->withoutVerifying()
                ->get($this->endpoint, [
                    'username'   => $cfg['username'],
                    'apikey'     => $cfg['api_key'],
                    'sendername' => $cfg['sender_name'],
                    'smstype'    => 'TRANS',
                    'numbers'    => $mobile,
                    'message'    => $message,
                    'peid'       => $cfg['pe_id'],
                    'templateid' => $templateId,
                ]);

            if (! $response->successful()) {
                Log::error('MessageIndia SMS failed', ['mobile' => $mobile, 'type' => $type, 'status' => $response->status()]);
                $this->record($mobile, $message, $type, 'failed', 'Gateway responded with HTTP '.$response->status(), $response->body(), $context);
                return false;
            }

            $this->record($mobile, $message, $type, 'sent', null, $response->body(), $context);
            return true;
        } catch (\Throwable $e) {
            Log::error('MessageIndia SMS exception: '.$e->getMessage(), ['mobile' => $mobile, 'type' => $type]);
            $this->record($mobile, $message, $type, 'failed', $e->getMessage(), null, $context);
            return false;
        }
    }

    /** Persist one SMS attempt to the communication log. */
    private function record(string $mobile, string $message, string $type, string $status, ?string $error, ?string $providerResponse, array $context): void
    {
        CommunicationLogger::log([
            'channel'             => 'sms',
            'type'                => $type,
            'recipient'           => $mobile,
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
