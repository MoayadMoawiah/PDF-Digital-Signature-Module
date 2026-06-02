<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Signer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectionNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Document $document,
        public readonly Signer   $signer,
    ) {}

    public function envelope(): Envelope
    {
        $locale  = app()->getLocale();
        $subject = $locale === 'ar'
            ? "رُفض التوقيع: {$this->document->title}"
            : "Signature Rejected: {$this->document->title}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.signing-complete',
            with: [
                'document'    => $this->document,
                'signer'      => $this->signer,
                'isRejection' => true,
                'companyName' => config('signature.company_name'),
                'logoUrl'     => config('signature.company_logo_url'),
            ]
        );
    }
}
