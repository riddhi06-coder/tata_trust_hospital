<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Generic admin notification for WhatsApp-chatbot events (booking requests and
 * "talk to our team" queries). Renders a simple label/value table so both flows
 * can share one email template.
 */
class WhatsAppNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $heading,
        public string $intro,
        public array $rows,
        public string $note = '',
        public bool $hasLogo = true,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->heading);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.whatsapp_notification',
            with: [
                'heading' => $this->heading,
                'intro'   => $this->intro,
                'rows'    => $this->rows,
                'note'    => $this->note,
                'hasLogo' => $this->hasLogo,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
