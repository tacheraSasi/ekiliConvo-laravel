<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAuditLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // We only need created_at for audit logs

    protected $fillable = [
        'room_id',
        'user_id',
        'action',
        'actor_name',
        'target_name',
        'details'
    ];

    protected $casts = [
        'details' => 'array',
        'created_at' => 'datetime'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Common audit actions
    const ACTION_JOINED = 'joined';
    const ACTION_LEFT = 'left';
    const ACTION_KICKED = 'kicked';
    const ACTION_MUTED = 'muted';
    const ACTION_UNMUTED = 'unmuted';
    const ACTION_HAND_RAISED = 'hand_raised';
    const ACTION_HAND_LOWERED = 'hand_lowered';
    const ACTION_RECORDING_STARTED = 'recording_started';
    const ACTION_RECORDING_STOPPED = 'recording_stopped';
    const ACTION_ROOM_LOCKED = 'room_locked';
    const ACTION_ROOM_UNLOCKED = 'room_unlocked';
    const ACTION_SCREEN_SHARE_STARTED = 'screen_share_started';
    const ACTION_SCREEN_SHARE_STOPPED = 'screen_share_stopped';

    public static function log(int $roomId, string $action, ?int $userId = null, ?string $actorName = null, ?string $targetName = null, array $details = []): void
    {
        static::create([
            'room_id' => $roomId,
            'user_id' => $userId,
            'action' => $action,
            'actor_name' => $actorName,
            'target_name' => $targetName,
            'details' => $details
        ]);
    }
}