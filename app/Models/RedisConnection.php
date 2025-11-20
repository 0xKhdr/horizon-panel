<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RedisConnection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'host',
        'port',
        'password',
        'database',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'encrypted', // Laravel 9+ encryption cast
    ];

    /**
     * The applications that this Redis connection belongs to.
     */
    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'application_redis_connection');
    }
}
