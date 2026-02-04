<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * IP Address model for storing and managing IP address records.
 *
 * @property int $id
 * @property string $ip_address
 * @property string $label
 * @property string|null $comment
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 */
class IpAddress extends Model
{
    /** @use HasFactory<\Database\Factories\IpAddressFactory> */
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ip_address',
        'label',
        'comment',
        'user_id',
    ];

    /**
     * Get the user that owns this IP address.
     *
     * @return BelongsTo<User, IpAddress>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activity log options for this model.
     *
     * @return LogOptions  The configured log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['ip_address', 'label', 'comment'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "IP address has been {$eventName}");
    }

    /**
     * Customize the activity before it is saved.
     *
     * @param  \Spatie\Activitylog\Models\Activity  $activity  The activity being logged
     * @param  string  $eventName  The event name (created, updated, deleted)
     */
    public function tapActivity(\Spatie\Activitylog\Models\Activity $activity, string $eventName): void
    {
        $activity->properties = $activity->properties->merge([
            'session_id' => session('audit_session_id'),
        ]);
    }
}
