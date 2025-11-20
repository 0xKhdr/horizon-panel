<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

/**
 * @property int $id
 * @property string|null $log_name
 * @property string $description
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $causer_type
 * @property int|null $causer_id
 * @property array|null $properties
 * @property string|null $event
 * @property Carbon|null $created_at
 * @property-read Model|null $subject
 * @property-read Model|null $causer
 */
class ActivityLog extends Activity
{
    use HasFactory;

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'event',
        'created_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Scope a query to only include logs for a specific model.
     */
    public function scopeForModel($query, string $modelType, int $modelId): void
    {
        $query->where('subject_type', $modelType)
            ->where('subject_id', $modelId);
    }

    /**
     * Scope a query to only include logs by a specific user.
     */
    public function scopeByUser($query, int $userId): void
    {
        $query->where('causer_type', 'App\\Models\\User')
            ->where('causer_id', $userId);
    }

    /**
     * Scope a query to only include logs for a specific event.
     */
    public function scopeForEvent($query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    /**
     * Scope a query to only include logs with a specific log name.
     */
    public function scopeInLog($query, ...$logNames): Builder
    {
        return $query->whereIn('log_name', $logNames);
    }

    /**
     * Scope a query to only include recent logs (last 7 days).
     */
    public function scopeRecent($query, int $days = 7): void
    {
        $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get formatted properties for display.
     */
    public function getFormattedProperties(): array
    {
        return collect($this->properties ?? [])
            ->map(function ($value, $key) {
                return [
                    'key' => $key,
                    'value' => is_array($value) || is_object($value) ? json_encode($value) : $value,
                    'type' => gettype($value),
                ];
            })
            ->toArray();
    }

    /**
     * Get the icon class based on the event type.
     */
    public function getEventIcon(): string
    {
        return match ($this->event) {
            'created' => 'heroicon-o-plus-circle',
            'updated' => 'heroicon-o-pencil-square',
            'deleted' => 'heroicon-o-trash',
            'restored' => 'heroicon-o-arrow-path',
            default => 'heroicon-o-information-circle',
        };
    }

    /**
     * Get the color class based on the event type.
     */
    public function getEventColor(): string
    {
        return match ($this->event) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'restored' => 'warning',
            default => 'gray',
        };
    }
}
