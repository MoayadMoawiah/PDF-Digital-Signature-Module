<?php

namespace App\Jobs;

use App\Mail\SigningInvitationMail;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Signer;
use App\Services\SigningTokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendSigningInvitation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly Document $document,
        public readonly Signer   $signer,
    ) {}

    public function handle(SigningTokenService $tokenService): void
    {
        if (! $this->signer->token) {
            $tokenService->assignToken($this->signer);
            $this->signer->refresh();
        }

        $signingUrl = $tokenService->signingUrl($this->signer);

        Mail::to($this->signer->email)->send(
            new SigningInvitationMail($this->document, $this->signer, $signingUrl)
        );

        $this->signer->update(['invitation_sent_at' => now()]);

        AuditLog::record($this->document, 'invitation_sent', $this->signer, [
            'email' => $this->signer->email,
        ]);
    }
}
