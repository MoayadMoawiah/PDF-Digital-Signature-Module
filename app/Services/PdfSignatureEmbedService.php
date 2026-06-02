<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\SignerStatus;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Signer;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class PdfSignatureEmbedService
{
    public function embed(Document $document, Signer $signer): void
    {
        $inputPath  = $this->resolveInputPath($document);
        $outputName = 'signed_' . $signer->id . '.pdf';
        $outputPath = 'documents/' . $document->id . '/' . $outputName;

        $absoluteInput  = Storage::path($inputPath);
        $absoluteOutput = Storage::path($outputPath);

        $this->validatePath($absoluteInput);

        $signedAt = $signer->signed_at?->toDateTimeString() ?? now()->toDateTimeString();

        $result = Process::run([
            'node',
            base_path('scripts/embed-signature.js'),
            '--pdf',            $absoluteInput,
            '--signature-base64', $signer->signature_data,
            '--signer-name',    $signer->name,
            '--signer-email',   $signer->email,
            '--signed-at',      $signedAt,
            '--ip-address',     $signer->ip_address ?? '',
            '--page',           'last',
            '--output',         $absoluteOutput,
        ]);

        if (! $result->successful()) {
            throw new RuntimeException(
                'PDF embed failed for signer ' . $signer->id . ': ' . $result->errorOutput()
            );
        }

        $document->update(['signed_path' => $outputPath]);

        $signer->purgeSignatureData();

        AuditLog::record($document, 'signed_pdf_generated', $signer, [
            'output_path' => $outputPath,
        ]);

        $this->updateDocumentStatus($document);
    }

    private function resolveInputPath(Document $document): string
    {
        // Chain signatures: use last signer's output as next input
        $lastSigned = $document->signers()
            ->where('status', SignerStatus::Signed->value)
            ->orderByDesc('signed_at')
            ->first();

        if ($lastSigned) {
            $candidatePath = 'documents/' . $document->id . '/signed_' . $lastSigned->id . '.pdf';
            if (Storage::exists($candidatePath)) {
                return $candidatePath;
            }
        }

        return $document->storage_path;
    }

    private function validatePath(string $path): void
    {
        $realPath = realpath($path);
        $storagePath = realpath(storage_path('app'));

        if ($realPath === false || ! str_starts_with($realPath, $storagePath)) {
            throw new RuntimeException('Invalid file path: potential path traversal detected.');
        }
    }

    private function updateDocumentStatus(Document $document): void
    {
        $document->refresh();

        $total    = $document->signers()->count();
        $signed   = $document->signers()->where('status', SignerStatus::Signed->value)->count();
        $rejected = $document->signers()->where('status', SignerStatus::Rejected->value)->count();

        if ($signed === $total) {
            $document->update(['status' => DocumentStatus::Completed->value]);
        } elseif ($signed > 0) {
            $document->update(['status' => DocumentStatus::PartiallySigned->value]);
        } elseif ($rejected === $total) {
            $document->update(['status' => DocumentStatus::Expired->value]);
        }
    }
}
