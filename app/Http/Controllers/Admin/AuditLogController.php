<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function show(Document $document): Response
    {
        if ($document->created_by !== Auth::id()) {
            abort(403);
        }

        $document->load(['auditLogs.signer']);

        return Inertia::render('Admin/AuditLogs/Index', [
            'document' => [
                'id'    => $document->id,
                'title' => $document->title,
            ],
            'audit_logs' => $document->auditLogs->map(fn($log) => [
                'id'           => $log->id,
                'action'       => $log->action,
                'signer_name'  => $log->signer?->name,
                'signer_email' => $log->signer?->email,
                'ip_address'   => $log->ip_address,
                'user_agent'   => $log->user_agent,
                'metadata'     => $log->metadata,
                'performed_at' => $log->performed_at->toDateTimeString(),
            ]),
        ]);
    }
}
