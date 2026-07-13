<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around the MessageIndia SMS API.
 * Used exclusively for OTP delivery on the appointment login flow.
 */
class MessageIndiaSms
{
    private string $endpoint = 'http://sms.messageindia.in/v2/sendSMS';

    /**
     * Send an OTP to the given 10-digit mobile number.
     * Returns true on API success, false otherwise (error is logged).
     */
    public function sendOtp(string $mobile, string $otp): bool
    {
        $cfg = config('services.messageindia');

        // Fail-safe: if creds aren't configured, log locally rather than error out.
        if (empty($cfg['username']) || empty($cfg['api_key'])) {
            Log::warning('MessageIndia SMS credentials missing — OTP not sent', [
                'mobile' => $mobile,
                'otp'    => $otp,
            ]);
            return false;
        }

        $message = "{$otp} is your OTP for Small Animal Hospital Mumbai (SAHM) appointment booking. Thank you.";

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
                    'templateid' => $cfg['template_id'],
                ]);

            if (! $response->successful()) {
                Log::error('MessageIndia SMS request failed', [
                    'mobile' => $mobile,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('MessageIndia SMS exception: '.$e->getMessage(), ['mobile' => $mobile]);
            return false;
        }
    }

    /**
     * Send the tentative-appointment confirmation SMS.
     * $formattedDate must already be human-friendly (e.g. "12 Jul 2026").
     */
    public function sendAppointmentConfirmation(string $mobile, string $formattedDate): bool
    {
        $cfg = config('services.messageindia');

        if (empty($cfg['username']) || empty($cfg['api_key'])) {
            Log::warning('MessageIndia SMS credentials missing — appointment confirmation not sent', [
                'mobile' => $mobile,
                'date'   => $formattedDate,
            ]);
            return false;
        }

        $message = "Your pet's tentative appointment with Small Animal Hospital Mumbai (SAHM) is scheduled for {$formattedDate}. Please note that this is a tentative booking. You will receive a confirmation call from our Customer Care Department shortly. For any assistance, please contact us at 022 65383538.";

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
                    'templateid' => $cfg['appointment_template_id'] ?? '1707177744709776264',
                ]);

            if (! $response->successful()) {
                Log::error('MessageIndia appointment SMS failed', [
                    'mobile' => $mobile,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('MessageIndia appointment SMS exception: '.$e->getMessage(), ['mobile' => $mobile]);
            return false;
        }
    }

    /**
     * Notify the owner that their appointment has been cancelled.
     * $formattedDate must already be human-friendly (e.g. "12 Jul 2026").
     */
    public function sendAppointmentCancellation(string $mobile, string $formattedDate): bool
    {
        $message = "Your pet's appointment at Small Animal Hospital Mumbai (SAHM) for {$formattedDate} has been cancelled. Please contact us to reschedule.";

        return $this->send($mobile, $message, config('services.messageindia.cancellation_template_id', '1707172283922616212'));
    }

    /**
     * Notify the owner that their appointment has been rescheduled.
     * Both dates must already be human-friendly (e.g. "12 Jul 2026").
     */
    public function sendAppointmentReschedule(string $mobile, string $oldDate, string $newDate): bool
    {
        $message = "Your pet's appointment with Small Animal Hospital Mumbai (SAHM) has been rescheduled from {$oldDate} to {$newDate}.";

        return $this->send($mobile, $message, config('services.messageindia.reschedule_template_id', '1707172283932407122'));
    }

    /**
     * Shared transactional-SMS sender. Returns true on API success, false otherwise.
     */
    private function send(string $mobile, string $message, string $templateId): bool
    {
        $cfg = config('services.messageindia');

        if (empty($cfg['username']) || empty($cfg['api_key'])) {
            Log::warning('MessageIndia SMS credentials missing — SMS not sent', [
                'mobile'   => $mobile,
                'template' => $templateId,
            ]);
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
                Log::error('MessageIndia SMS failed', [
                    'mobile'   => $mobile,
                    'template' => $templateId,
                    'status'   => $response->status(),
                    'body'     => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('MessageIndia SMS exception: '.$e->getMessage(), ['mobile' => $mobile]);
            return false;
        }
    }
}
