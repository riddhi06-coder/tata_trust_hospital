<?php

namespace App\Mail;

use App\Models\AppointmentEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public AppointmentEnquiry $enquiry;
    public string $mailSubject;
    public string $heading;
    public string $intro;
    public string $note;
    public bool $hasLogo;

    public function __construct(AppointmentEnquiry $enquiry, string $subject, string $heading, string $intro, string $note = '', bool $hasLogo = true)
    {
        $this->enquiry     = $enquiry;
        $this->mailSubject = $subject;
        $this->heading     = $heading;
        $this->intro       = $intro;
        $this->note        = $note;
        $this->hasLogo     = $hasLogo;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->mailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.appointment_enquiry',
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
