<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $application_id
 * @property int $redis_connection_id
 * @property array $queue_names
 * @property string $balance_strategy
 * @property int $min_processes
 * @property int $max_processes
 * @property int $tries
 * @property int $timeout
 * @property int $memory
 * @property bool $is_active
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read Application $application
 * @property-read RedisConnection $redisConnection
 */
class QueueConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'redis_connection_id',
        'queue_names',
        'balance_strategy',
        'min_processes',
        'max_processes',
        'tries',
        'timeout',
        'memory',
        'is_active',
    ];

    protected $casts = [
        'queue_names' => 'array',
        'balance_strategy' => 'string',
        'min_processes' => 'integer',
        'max_processes' => 'integer',
        'tries' => 'integer',
        'timeout' => 'integer',
        'memory' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the application that owns this queue configuration.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    /**
     * Get the Redis connection that owns this queue configuration.
     */
    public function redisConnection(): BelongsTo
    {
        return $this->belongsTo(RedisConnection::class);
    }

    /**
     * Scope a query to only include active configurations.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by balance strategy.
     */
    public function scopeByBalanceStrategy($query, string $strategy): void
    {
        $query->where('balance_strategy', $strategy);
    }

    /**
     * Get the Horizon configuration array for this queue setup.
     */
    public function getHorizonConfig(): array
    {
        return [
            'connection' => 'redis', // We'll use the Redis connection details from the related model
            'queue' => $this->queue_names,
            'balance' => $this->balance_strategy,
            'processes' => $this->min_processes,
            'max_processes' => $this->max_processes,
            'tries' => $this->tries,
            'timeout' => $this->timeout,
            'memory' => $this->memory,
        ];
    }

    /**
     * Check if this configuration is valid for Horizon.
     */
    public function isValidForHorizon(): bool
    {
        return $this->is_active
            && $this->application->is_active
            && $this->redisConnection->is_active
            && $this->redisConnection->health_status === 'healthy';
    }

    /**
     * Get the Redis connection config for this queue configuration.
     */
    public function getRedisConfig(): array
    {
        return $this->redisConnection->getConnectionConfig();
    }
}
