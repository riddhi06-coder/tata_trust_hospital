<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $appName       = config('app.name');
        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        $logoPath      = public_path('admin/assets/images/logo/tata-trust-logo.webp');

        return (new MailMessage)
            ->subject('Reset Your Password — '.$appName)
            ->view('emails.reset_password', [
                'url'           => $url,
                'appName'       => $appName,
                'userName'      => $notifiable->name ?? null,
                'expireMinutes' => $expireMinutes,
            ])
            ->withSymfonyMessage(function (Email $message) use ($logoPath) {
                if (! is_file($logoPath)) {
                    return;
                }

                $part = (new DataPart(new File($logoPath), null, 'image/webp'))
                    ->asInline()
                    ->setContentId('logo@tata-trust');

                $message->addPart($part);
            });
    }
}
