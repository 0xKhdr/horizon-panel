<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Application extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The Redis connections that belong to the application.
     */
    public function redisConnections(): BelongsToMany
    {
        return $this->belongsToMany(RedisConnection::class, 'application_redis_connection');
    }
}
