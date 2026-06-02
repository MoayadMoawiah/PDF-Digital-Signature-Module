<?php

namespace App\Models;

use App\Enums\SignerStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Signer extends Model
{
    use HasUlids;

    protected $fillable = [
        'document_id',
        'name',
        'email',
        'signing_order',
        'token',
        'status',
        'rejection_reason',
        'signature_data',
        'signed_at',
        'ip_address',
        'user_agent',
        'invitation_sent_at',
        'expires_at',
    ];

    protected $casts = [
        'status'             => SignerStatus::class,
        'signed_at'          => 'datetime',
        'invitation_sent_at' => 'datetime',
        'expires_at'         => 'datetime',
    ];

    protected $hidden = ['signature_data'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canSign(): bool
    {
        return $this->status === SignerStatus::Pending || $this->status === SignerStatus::Viewed;
    }

    public function purgeSignatureData(): void
    {
        $this->updateQuietly(['signature_data' => null]);
    }
}
