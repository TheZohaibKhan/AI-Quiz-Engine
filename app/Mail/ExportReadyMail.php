<?php

namespace App\Mail;

use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportReadyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        string $mailSubject,
        public readonly ?string $downloadUrl = null,
        public readonly ?CarbonImmutable $expiresAt = null,
        public readonly ?string $pdfData = null,
        public readonly ?string $pdfName = null,
    ) {
        $this->subject = $mailSubject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subject);
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.export-ready');
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        if ($this->pdfData === null) {
            return [];
        }

        return [
            Attachment::fromData(fn () => $this->pdfData, $this->pdfName ?? 'export.pdf')
                ->withMime('application/pdf'),
        ];
    }
}