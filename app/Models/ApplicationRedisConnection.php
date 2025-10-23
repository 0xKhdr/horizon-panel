<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $application_id
 * @property int $redis_connection_id
 * @property bool $is_primary
 * @property int $priority
 * @property bool $is_active
 * @property \Carbon\Carbon|null $last_used_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Application $application
 * @property-read RedisConnection $redisConnection
 */
class ApplicationRedisConnection extends Pivot
{
    use HasFactory;

    protected $table = 'application_redis_connection';

    protected $fillable = [
        'application_id',
        'redis_connection_id',
        'is_primary',
        'priority',
        'is_active',
        'last_used_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'priority' => 'integer',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the application that owns the relationship.
     */
    public function application(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the Redis connection that owns the relationship.
     */
    public function redisConnection(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RedisConnection::class);
    }

    /**
     * Scope to get only primary connections.
     */
    public function scopePrimary($query): void
    {
        $query->where('is_primary', true);
    }

    /**
     * Scope to get only active connections.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope to order by priority.
     */
    public function scopeByPriority($query, string $direction = 'asc'): void
    {
        $query->orderBy('priority', $direction);
    }

    /**
     * Mark this connection as used.
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Check if this is a primary connection.
     */
    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    /**
     * Check if this connection is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
