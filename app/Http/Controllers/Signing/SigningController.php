<?php

namespace App\Http\Controllers\Signing;

use App\Enums\DocumentStatus;
use App\Enums\SignerStatus;
use App\Http\Controllers\Controller;
use App\Mail\RejectionNotificationMail;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Signer;
use App\Services\PdfSignatureEmbedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SigningController extends Controller
{
    public function show(Request $request, string $token): Response
    {
        /** @var Signer $signer */
        $signer   = $request->attributes->get('signer');
        $document = $signer->document;

        // Build a temporary URL to stream the PDF for the viewer
        $pdfUrl = route('sign.pdf', ['token' => $token]);

        return Inertia::render('Signing/Sign', [
            'token'    => $token,
            'signer'   => [
                'id'         => $signer->id,
                'name'       => $signer->name,
                'email'      => $signer->email,
                'expires_at' => $signer->expires_at?->toIso8601String(),
            ],
            'document' => [
                'id'         => $document->id,
                'title'      => $document->title,
                'expires_at' => $document->expires_at?->toIso8601String(),
            ],
            'pdf_url' => $pdfUrl,
        ]);
    }

    public function pdf(Request $request, string $token)
    {
        /** @var Signer $signer */
        $signer   = $request->attributes->get('signer');
        $document = $signer->document;

        if (! Storage::exists($document->storage_path)) {
            abort(404);
        }

        return response()->file(
            Storage::path($document->storage_path),
            ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'inline']
        );
    }

    public function submit(Request $request, string $token, PdfSignatureEmbedService $embedService)
    {
        /** @var Signer $signer */
        $signer   = $request->attributes->get('signer');
        $document = $signer->document;

        $request->validate([
            'signature_data' => ['required', 'string', 'regex:/^data:image\/png;base64,/'],
        ]);

        $signer->update([
            'signature_data' => $request->input('signature_data'),
            'status'         => SignerStatus::Signed->value,
            'signed_at'      => now(),
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
        ]);

        AuditLog::record($document, 'signed', $signer, [
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $embedService->embed($document, $signer);
        } catch (\Throwable $e) {
            \Log::error('PDF embed failed', ['error' => $e->getMessage(), 'signer_id' => $signer->id]);
        }

        return redirect()->route('sign.complete');
    }

    public function reject(Request $request, string $token)
    {
        /** @var Signer $signer */
        $signer   = $request->attributes->get('signer');
        $document = $signer->document;

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $signer->update([
            'status'           => SignerStatus::Rejected->value,
            'rejection_reason' => $request->input('rejection_reason'),
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
        ]);

        AuditLog::record($document, 'rejected', $signer, [
            'reason' => $request->input('rejection_reason'),
        ]);

        // Notify document owner
        try {
            Mail::to($document->creator->email)->send(
                new RejectionNotificationMail($document, $signer)
            );
        } catch (\Throwable $e) {
            \Log::warning('Rejection notification email failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('sign.rejected');
    }
}
