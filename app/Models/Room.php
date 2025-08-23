<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'creator_id',
        'uuid',
        'visibility',
        'expires_at',
        'password',
        'is_locked',
        'recording_enabled',
        'recording_in_progress',
        'waiting_room_enabled',
        'settings'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_locked' => 'boolean',
        'recording_enabled' => 'boolean',
        'recording_in_progress' => 'boolean',
        'waiting_room_enabled' => 'boolean',
        'settings' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($room) {
            if (empty($room->uuid)) {
                $room->uuid = Str::uuid();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'room_users')
                    ->withPivot('role_in_room', 'joined_at', 'is_muted', 'hand_raised', 'permissions', 'status')
                    ->withTimestamps();
    }

    public function recordings(): HasMany
    {
        return $this->hasMany(RoomRecording::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(RoomAuditLog::class);
    }

    public function isHost(User $user): bool
    {
        return $this->creator_id === $user->id;
    }

    public function isParticipant(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPasswordProtected(): bool
    {
        return !empty($this->password);
    }

    public function checkPassword(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function setPassword(string $password): void
    {
        $this->password = Hash::make($password);
    }

    public function canJoin(User $user = null): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->is_locked && $user && !$this->isHost($user) && !$this->isParticipant($user)) {
            return false;
        }

        return true;
    }

    public function isRecording(): bool
    {
        return $this->recording_in_progress;
    }

    public function hasRecordings(): bool
    {
        return $this->recordings()->exists();
    }

    public function getHostUser(): ?User
    {
        return $this->users()->wherePivot('role_in_room', 'host')->first();
    }

    public function getParticipantCount(): int
    {
        return $this->users()->count();
    }

    public function getRaisedHandsCount(): int
    {
        return $this->users()->wherePivot('hand_raised', true)->count();
    }

    public function getUserRole(User $user): ?string
    {
        $userInRoom = $this->users()->where('user_id', $user->id)->first();
        return $userInRoom ? $userInRoom->pivot->role_in_room : null;
    }

    public function canUserPerformAction(User $user, string $action): bool
    {
        $role = $this->getUserRole($user);
        
        if (!$role) {
            return false;
        }

        // Host can perform all actions
        if ($role === 'host') {
            return true;
        }

        // Define participant permissions
        $participantPermissions = [
            'join_stream',
            'send_message',
            'raise_hand',
            'share_screen'
        ];

        return in_array($action, $participantPermissions);
    }

    public function isWaitingRoomEnabled(): bool
    {
        return $this->waiting_room_enabled;
    }

    public function getWaitingParticipants()
    {
        return $this->users()->wherePivot('status', 'waiting')->get();
    }

    public function getAdmittedParticipants()
    {
        return $this->users()->wherePivot('status', 'admitted')->get();
    }

    public function getWaitingCount(): int
    {
        return $this->users()->wherePivot('status', 'waiting')->count();
    }

    public function admitParticipant(User $user): bool
    {
        $participant = $this->users()->where('user_id', $user->id)->first();
        
        if (!$participant || $participant->pivot->status !== 'waiting') {
            return false;
        }

        $this->users()->updateExistingPivot($user->id, ['status' => 'admitted']);
        return true;
    }

    public function rejectParticipant(User $user): bool
    {
        $participant = $this->users()->where('user_id', $user->id)->first();
        
        if (!$participant || $participant->pivot->status !== 'waiting') {
            return false;
        }

        $this->users()->updateExistingPivot($user->id, ['status' => 'rejected']);
        return true;
    }

    public function isParticipantWaiting(User $user): bool
    {
        $participant = $this->users()->where('user_id', $user->id)->first();
        return $participant && $participant->pivot->status === 'waiting';
    }

    public function isParticipantAdmitted(User $user): bool
    {
        $participant = $this->users()->where('user_id', $user->id)->first();
        return $participant && $participant->pivot->status === 'admitted';
    }
}
