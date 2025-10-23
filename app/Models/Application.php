<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $color
 * @property string|null $icon
 * @property bool $is_active
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class Application extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the Redis connections associated with this application.
     */
    public function redisConnections(): BelongsToMany
    {
        return $this->belongsToMany(RedisConnection::class, 'application_redis_connection')
            ->withPivot(['is_primary', 'priority', 'is_active', 'last_used_at'])
            ->withTimestamps();
    }

    /**
     * Get the queue configurations for this application.
     */
    public function queueConfigurations(): HasMany
    {
        return $this->hasMany(QueueConfiguration::class);
    }

    /**
     * Get the primary Redis connection for this application.
     */
    public function primaryRedisConnection(): BelongsToMany
    {
        return $this->redisConnections()->wherePivot('is_primary', true);
    }

    /**
     * Get the active Redis connections for this application.
     */
    public function activeRedisConnections(): BelongsToMany
    {
        return $this->redisConnections()->wherePivot('is_active', true);
    }

    /**
     * Scope a query to only include active applications.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name',
                'slug',
                'description',
                'color',
                'icon',
                'is_active',
                'metadata',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
