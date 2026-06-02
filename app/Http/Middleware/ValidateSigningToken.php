<?php

namespace App\Http\Middleware;

use App\Enums\SignerStatus;
use App\Models\AuditLog;
use App\Models\Signer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateSigningToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->route('token');

        $signer = Signer::where('token', $token)
            ->with('document')
            ->first();

        if (! $signer) {
            abort(404, 'Invalid signing token.');
        }

        // Already completed — gone
        if (in_array($signer->status, [SignerStatus::Signed, SignerStatus::Rejected])) {
            abort(410, 'This signing link has already been used.');
        }

        // Signer-level expiry
        if ($signer->expires_at && $signer->expires_at->isPast()) {
            $signer->update(['status' => SignerStatus::Expired->value]);
            AuditLog::record($signer->document, 'expired', $signer);
            return redirect()->route('sign.expired');
        }

        // Document-level expiry
        if ($signer->document->expires_at && $signer->document->expires_at->isPast()) {
            return redirect()->route('sign.expired');
        }

        // Document must be in a signable state
        if (in_array($signer->document->status->value, ['completed', 'expired'])) {
            abort(410, 'This document is no longer available for signing.');
        }

        // Log first view
        if ($signer->status === SignerStatus::Pending) {
            $signer->update(['status' => SignerStatus::Viewed->value]);
            AuditLog::record($signer->document, 'document_viewed', $signer, [
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        $request->merge(['_signer' => $signer]);
        $request->attributes->set('signer', $signer);

        return $next($request);
    }
}
