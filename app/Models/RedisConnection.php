<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

/**
 * @property int $id
 * @property string $name
 * @property string $host
 * @property int $port
 * @property string|null $password
 * @property int $database
 * @property array|null $options
 * @property bool $is_active
 * @property string|null $health_status
 * @property \Carbon\Carbon|null $last_health_check_at
 * @property string|null $last_error
 * @property string $environment
 * @property string|null $region
 * @property string|null $provider
 * @property string|null $notes
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
class RedisConnection extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'host',
        'port',
        'password',
        'database',
        'options',
        'is_active',
        'health_status',
        'last_health_check_at',
        'last_error',
        'environment',
        'region',
        'provider',
        'notes',
    ];

    protected $casts = [
        'port' => 'integer',
        'database' => 'integer',
        'options' => 'array',
        'is_active' => 'boolean',
        'last_health_check_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * PHP 8.4 property hook for automatic password encryption/decryption
     */
    public string $password {
        set {
            // Encrypt password when setting
            $this->attributes['password'] = $value ? Crypt::encryptString($value) : null;
        }
        get {
            // Decrypt password when getting
            return $this->attributes['password'] ? Crypt::decryptString($this->attributes['password']) : null;
        }
    }

    /**
     * Get the applications that use this Redis connection.
     */
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_redis_connection')
            ->withPivot(['is_primary', 'priority', 'is_active', 'last_used_at'])
            ->withTimestamps();
    }

    /**
     * Get the queue configurations for this Redis connection.
     */
    public function queueConfigurations(): HasMany
    {
        return $this->hasMany(QueueConfiguration::class);
    }

    /**
     * Get the health logs for this Redis connection.
     */
    public function healthLogs(): HasMany
    {
        return $this->hasMany(RedisHealthLog::class);
    }

    /**
     * Scope a query to only include active connections.
     */
    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Scope a query to only include healthy connections.
     */
    public function scopeHealthy($query): void
    {
        $query->where('health_status', 'healthy');
    }

    /**
     * Scope a query to only include connections by environment.
     */
    public function scopeByEnvironment($query, string $environment): void
    {
        $query->where('environment', $environment);
    }

    /**
     * Get the full Redis connection configuration for Predis.
     */
    public function getConnectionConfig(): array
    {
        $config = [
            'scheme' => 'tcp',
            'host' => $this->host,
            'port' => $this->port,
            'database' => $this->database,
        ];

        if ($this->password) {
            $config['password'] = $this->password;
        }

        if ($this->options) {
            $config = array_merge($config, $this->options);
        }

        return $config;
    }

    /**
     * Test the Redis connection.
     */
    public function testConnection(): bool
    {
        try {
            $client = new \Predis\Client($this->getConnectionConfig());
            $client->ping();

            return true;
        } catch (\Exception $e) {
            $this->update([
                'health_status' => 'unhealthy',
                'last_error' => $e->getMessage(),
                'last_health_check_at' => now(),
            ]);

            return false;
        }
    }
}
