<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $redis_connection_id
 * @property string $status
 * @property float|null $latency_ms
 * @property string|null $error_message
 * @property int|null $memory_used_mb
 * @property int|null $connected_clients
 * @property int|null $keys_count
 * @property array|null $metadata
 * @property \Carbon\Carbon $checked_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read RedisConnection $redisConnection
 */
class RedisHealthLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'redis_connection_id',
        'status',
        'latency_ms',
        'error_message',
        'memory_used_mb',
        'connected_clients',
        'keys_count',
        'metadata',
        'checked_at',
    ];

    protected $casts = [
        'latency_ms' => 'decimal:2',
        'memory_used_mb' => 'integer',
        'connected_clients' => 'integer',
        'keys_count' => 'integer',
        'metadata' => 'array',
        'checked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the Redis connection that owns this health log.
     */
    public function redisConnection(): BelongsTo
    {
        return $this->belongsTo(RedisConnection::class);
    }

    /**
     * Scope a query to only include logs with a specific status.
     */
    public function scopeByStatus($query, string $status): void
    {
        $query->where('status', $status);
    }

    /**
     * Scope a query to only include logs within a date range.
     */
    public function scopeDateRange($query, $startDate, $endDate): void
    {
        $query->whereBetween('checked_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include logs for a specific Redis connection.
     */
    public function scopeForConnection($query, int $connectionId): void
    {
        $query->where('redis_connection_id', $connectionId);
    }

    /**
     * Scope a query to only include recent logs (last 24 hours).
     */
    public function scopeRecent($query): void
    {
        $query->where('checked_at', '>=', now()->subDay());
    }

    /**
     * Check if this log indicates a healthy connection.
     */
    public function isHealthy(): bool
    {
        return $this->status === 'healthy';
    }

    /**
     * Check if this log indicates an unhealthy connection.
     */
    public function isUnhealthy(): bool
    {
        return in_array($this->status, ['unhealthy', 'error', 'timeout']);
    }

    /**
     * Get the performance metrics summary.
     */
    public function getPerformanceSummary(): array
    {
        return [
            'latency_ms' => $this->latency_ms,
            'memory_used_mb' => $this->memory_used_mb,
            'connected_clients' => $this->connected_clients,
            'keys_count' => $this->keys_count,
            'status' => $this->status,
        ];
    }
}
