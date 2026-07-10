<?php

namespace App\Mail;

use App\Models\ContactEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactEnquiry $enquiry;
    public string $heading;
    public string $intro;
    public string $note;
    public bool $hasLogo;

    public function __construct(ContactEnquiry $enquiry, string $heading, string $intro, string $note = '', bool $hasLogo = true)
    {
        $this->enquiry = $enquiry;
        $this->heading = $heading;
        $this->intro   = $intro;
        $this->note    = $note;
        $this->hasLogo = $hasLogo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->heading,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact_enquiry',
            with: [
                'enquiry' => $this->enquiry,
                'heading' => $this->heading,
                'intro'   => $this->intro,
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
