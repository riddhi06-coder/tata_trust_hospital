<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public JobApplication $application;
    public string $heading;
    public string $intro;
    public string $note;
    public bool $hasLogo;
    public bool $attachResume;
    public ?string $resumeAbsolutePath;
    public ?string $resumeOriginalName;
    public ?string $jdAbsolutePath;
    public ?string $jdOriginalName;

    public function __construct(
        JobApplication $application,
        string $heading,
        string $intro,
        string $note = '',
        bool $hasLogo = true,
        bool $attachResume = false,
        ?string $resumeAbsolutePath = null,
        ?string $resumeOriginalName = null,
        ?string $jdAbsolutePath = null,
        ?string $jdOriginalName = null,
    ) {
        $this->application        = $application;
        $this->heading            = $heading;
        $this->intro              = $intro;
        $this->note               = $note;
        $this->hasLogo            = $hasLogo;
        $this->attachResume       = $attachResume;
        $this->resumeAbsolutePath = $resumeAbsolutePath;
        $this->resumeOriginalName = $resumeOriginalName;
        $this->jdAbsolutePath     = $jdAbsolutePath;
        $this->jdOriginalName     = $jdOriginalName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->heading);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job_application',
            with: [
                'application' => $this->application,
                'heading'     => $this->heading,
                'intro'       => $this->intro,
                'note'        => $this->note,
                'hasLogo'     => $this->hasLogo,
            ],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if ($this->attachResume && $this->resumeAbsolutePath && file_exists($this->resumeAbsolutePath)) {
            $attachments[] = Attachment::fromPath($this->resumeAbsolutePath)
                ->as($this->resumeOriginalName ?: basename($this->resumeAbsolutePath));
        }

        if ($this->jdAbsolutePath && file_exists($this->jdAbsolutePath)) {
            $attachments[] = Attachment::fromPath($this->jdAbsolutePath)
                ->as($this->jdOriginalName ?: basename($this->jdAbsolutePath));
        }

        return $attachments;
    }
}
