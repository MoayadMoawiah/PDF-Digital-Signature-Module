<?php

namespace App\Mail;

use App\Models\Document;
use App\Models\Signer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SigningInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Document $document,
        public readonly Signer   $signer,
        public readonly string   $signingUrl,
    ) {}

    public function envelope(): Envelope
    {
        $locale      = app()->getLocale();
        $companyName = config('signature.company_name');

        $subject = $locale === 'ar'
            ? "طلب توقيع: {$this->document->title}"
            : "Signature Request: {$this->document->title}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.signing-invitation',
            with: [
                'document'    => $this->document,
                'signer'      => $this->signer,
                'signingUrl'  => $this->signingUrl,
                'companyName' => config('signature.company_name'),
                'logoUrl'     => config('signature.company_logo_url'),
                'expiryDays'  => config('signature.default_expiry_days'),
            ]
        );
    }
}
