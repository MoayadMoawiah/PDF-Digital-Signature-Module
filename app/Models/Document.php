<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasUlids;

    protected $fillable = [
        'title',
        'original_filename',
        'storage_path',
        'signed_path',
        'status',
        'created_by',
        'expires_at',
    ];

    protected $casts = [
        'status'     => DocumentStatus::class,
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signers(): HasMany
    {
        return $this->hasMany(Signer::class)->orderBy('signing_order');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class)->orderBy('performed_at');
    }

    public function scopeByStatus($query, DocumentStatus $status)
    {
        return $query->where('status', $status->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', DocumentStatus::Pending->value);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', DocumentStatus::Completed->value);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function allSigned(): bool
    {
        return $this->signers()->where('status', '!=', 'signed')->doesntExist();
    }

    public function anyRejected(): bool
    {
        return $this->signers()->where('status', 'rejected')->exists();
    }

    public function getSignedPdfPathAttribute(): ?string
    {
        return $this->signed_path;
    }
}
