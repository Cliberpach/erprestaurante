<?php

namespace App\Models\Tenant;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceDispatchLog extends Model
{
    use HasFactory;
    protected $connection = 'tenant';
    protected $fillable = [
        'tenant_id',
        'invoiceable_id',
        'invoiceable_type',
        'status',
        'attempts',
        'max_attempts',
        'next_retry_at',
        'expires_at',
        'sent_at',
        'last_error',
        'metadata',
    ];

    protected $casts = [
        'last_error'    => 'array',
        'metadata'      => 'array',
        'next_retry_at' => 'datetime',
        'expires_at'    => 'datetime',
        'sent_at'       => 'datetime',
    ];

    // Estados
    const STATUS_PENDING    = 'PENDIENTE';
    const STATUS_PROCESSING = 'PROCESANDO';
    const STATUS_SENT       = 'ENVIADO';
    const STATUS_ACCEPTED   = 'ACEPTADO';
    const STATUS_FAILED     = 'RECHAZADO';
    const STATUS_EXPIRED    = 'EXPIRADO';

    // Relación polimórfica (boleta o factura)
    public function invoiceable()
    {
        return $this->morphTo();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function canRetry(): bool
    {
        return !$this->isExpired()
            && $this->attempts < $this->max_attempts
            && !in_array($this->status, [
                self::STATUS_SENT,
                self::STATUS_ACCEPTED,
            ]);
    }

    // Backoff exponencial: 15min → 1h → 3h → 8h → 24h
    public function calculateNextRetry(): Carbon
    {
        $minutes = match ($this->attempts) {
            1       => 15,
            2       => 60,
            3       => 180,
            4       => 480,
            default => 1440,
        };

        $nextRetry = now()->addMinutes($minutes);

        // No programar más allá de la expiración de SUNAT
        return $nextRetry->gt($this->expires_at)
            ? $this->expires_at->subMinutes(30)
            : $nextRetry;
    }

    public function markAsFailed(string $error, array $context = []): void
    {
        $this->increment('attempts');
        $this->refresh();

        $this->update([
            'last_error'    => ['message' => $error, 'context' => $context, 'at' => now()],
            'status'        => $this->canRetry() ? self::STATUS_PENDING : self::STATUS_FAILED,
            'next_retry_at' => $this->canRetry() ? $this->calculateNextRetry() : null,
        ]);
    }
}
