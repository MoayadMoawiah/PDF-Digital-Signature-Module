<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocumentStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendSigningInvitation;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\Signer;
use App\Services\SigningTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $status    = $request->input('status');
        $documents = Document::with(['signers', 'creator'])
            ->where('created_by', Auth::id())
            ->when($status, fn($q) => $q->where('status', $status))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->through(fn($doc) => [
                'id'             => $doc->id,
                'title'          => $doc->title,
                'status'         => $doc->status->value,
                'status_color'   => $doc->status->color(),
                'signers_count'  => $doc->signers->count(),
                'signed_count'   => $doc->signers->where('status', 'signed')->count(),
                'created_at'     => $doc->created_at->toDateTimeString(),
                'expires_at'     => $doc->expires_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Documents/Index', [
            'documents'      => $documents,
            'current_status' => $status,
            'statuses'       => array_column(DocumentStatus::cases(), 'value'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Documents/Create', [
            'default_expiry_days' => config('signature.default_expiry_days'),
        ]);
    }

    public function store(Request $request, SigningTokenService $tokenService)
    {
        $maxMb = config('signature.max_file_size_mb', 20);

        $validated = $request->validate([
            'title'                     => ['required', 'string', 'max:255'],
            'pdf'                       => ['required', 'file', 'mimes:pdf', "max:{$this->mbToKb($maxMb)}"],
            'expires_at'                => ['nullable', 'date', 'after:now'],
            'signers'                   => ['required', 'array', 'min:1'],
            'signers.*.name'            => ['required', 'string', 'max:255'],
            'signers.*.email'           => ['required', 'email'],
            'signers.*.expires_at'      => ['nullable', 'date', 'after:now'],
        ]);

        // Unique emails per document
        $emails = array_column($validated['signers'], 'email');
        if (count($emails) !== count(array_unique($emails))) {
            return back()->withErrors(['signers' => 'Each signer must have a unique email address.']);
        }

        $document = DB::transaction(function () use ($validated, $request, $tokenService) {
            $ulidDir = Str::ulid();
            $path    = $request->file('pdf')->storeAs(
                "documents/{$ulidDir}",
                'original.pdf',
                'local'
            );

            $sha256 = hash_file('sha256', Storage::path($path));

            $document = Document::create([
                'title'             => $validated['title'],
                'original_filename' => $request->file('pdf')->getClientOriginalName(),
                'storage_path'      => $path,
                'status'            => DocumentStatus::Draft->value,
                'created_by'        => Auth::id(),
                'expires_at'        => $validated['expires_at'] ?? null,
            ]);

            AuditLog::record($document, 'document_created', null, [
                'sha256'            => $sha256,
                'original_filename' => $document->original_filename,
            ]);

            foreach ($validated['signers'] as $i => $signerData) {
                $signer = Signer::create([
                    'document_id'   => $document->id,
                    'name'          => $signerData['name'],
                    'email'         => $signerData['email'],
                    'signing_order' => $i + 1,
                    'token'         => $tokenService->generateToken(),
                    'expires_at'    => $signerData['expires_at']
                        ?? now()->addDays(config('signature.default_expiry_days')),
                ]);

                SendSigningInvitation::dispatch($document, $signer);
            }

            $document->update(['status' => DocumentStatus::Pending->value]);

            return $document;
        });

        return redirect()->route('admin.documents.show', $document)
            ->with('success', 'Document created and invitations sent.');
    }

    public function show(Document $document): Response
    {
        $this->authorizeDocument($document);

        $document->load(['signers', 'auditLogs.signer']);

        return Inertia::render('Admin/Documents/Show', [
            'document' => [
                'id'             => $document->id,
                'title'          => $document->title,
                'status'         => $document->status->value,
                'status_color'   => $document->status->color(),
                'created_at'     => $document->created_at->toDateTimeString(),
                'expires_at'     => $document->expires_at?->toDateTimeString(),
                'has_signed_pdf' => (bool) $document->signed_path,
            ],
            'signers' => $document->signers->map(fn($s) => [
                'id'                 => $s->id,
                'name'               => $s->name,
                'email'              => $s->email,
                'signing_order'      => $s->signing_order,
                'status'             => $s->status->value,
                'status_color'       => $s->status->color(),
                'rejection_reason'   => $s->rejection_reason,
                'signed_at'          => $s->signed_at?->toDateTimeString(),
                'invitation_sent_at' => $s->invitation_sent_at?->toDateTimeString(),
                'expires_at'         => $s->expires_at?->toDateTimeString(),
            ]),
            'audit_logs' => $document->auditLogs->map(fn($log) => [
                'id'           => $log->id,
                'action'       => $log->action,
                'signer_name'  => $log->signer?->name,
                'ip_address'   => $log->ip_address,
                'user_agent'   => $log->user_agent,
                'metadata'     => $log->metadata,
                'performed_at' => $log->performed_at->toDateTimeString(),
            ]),
        ]);
    }

    public function download(Document $document)
    {
        $this->authorizeDocument($document);

        if (! $document->signed_path || ! Storage::exists($document->signed_path)) {
            abort(404, 'Signed PDF not available.');
        }

        $filename = 'signed_' . str_replace(' ', '_', $document->title) . '.pdf';

        return Storage::download($document->signed_path, $filename);
    }

    public function destroy(Document $document)
    {
        $this->authorizeDocument($document);

        Storage::deleteDirectory('documents/' . $document->id);
        $document->delete();

        return redirect()->route('admin.documents.index')
            ->with('success', 'Document deleted.');
    }

    private function authorizeDocument(Document $document): void
    {
        if ($document->created_by !== Auth::id()) {
            abort(403);
        }
    }

    private function mbToKb(int $mb): int
    {
        return $mb * 1024;
    }
}
