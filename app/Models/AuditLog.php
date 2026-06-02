<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'document_id',
        'signer_id',
        'action',
        'ip_address',
        'user_agent',
        'metadata',
        'performed_at',
    ];

    protected $casts = [
        'metadata'     => 'array',
        'performed_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(Signer::class);
    }

    public static function record(
        Document $document,
        string $action,
        ?Signer $signer = null,
        array $metadata = []
    ): self {
        return static::create([
            'document_id'  => $document->id,
            'signer_id'    => $signer?->id,
            'action'       => $action,
            'ip_address'   => Request::ip(),
            'user_agent'   => Request::userAgent(),
            'metadata'     => $metadata ?: null,
            'performed_at' => now(),
        ]);
    }
}
