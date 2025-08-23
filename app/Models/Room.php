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
        'description',
        'creator_id',
        'uuid',
        'visibility',
        'category',
        'expires_at',
        'password',
        'is_locked',
        'recording_enabled',
        'recording_in_progress',
        'settings',
        'tags',
        'total_sessions',
        'total_participants_all_time',
        'last_activity_at',
        'quality_settings',
        'notification_settings',
        'theme',
        'custom_background'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_locked' => 'boolean',
        'recording_enabled' => 'boolean',
        'recording_in_progress' => 'boolean',
        'settings' => 'array',
        'tags' => 'array',
        'quality_settings' => 'array',
        'notification_settings' => 'array'
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
                    ->withPivot('role_in_room', 'joined_at', 'is_muted', 'hand_raised', 'permissions')
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

    public function analytics(): HasMany
    {
        return $this->hasMany(RoomAnalytics::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(RoomNote::class);
    }

    public function roomTags(): BelongsToMany
    {
        return $this->belongsToMany(RoomTag::class, 'room_tag_assignments')
                    ->withTimestamps()
                    ->withPivot('assigned_at');
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

    /**
     * Update room activity timestamp
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Increment session count
     */
    public function incrementSessions(): void
    {
        $this->increment('total_sessions');
        $this->updateActivity();
    }

    /**
     * Add a tag to the room
     */
    public function addTag(string $tagName, string $color = '#6B7280'): void
    {
        $tag = RoomTag::findOrCreateByName($tagName, $color);
        $this->roomTags()->syncWithoutDetaching([$tag->id]);
        
        // Update the tags JSON field for quick access
        $currentTags = $this->tags ?? [];
        if (!in_array($tagName, $currentTags)) {
            $currentTags[] = $tagName;
            $this->update(['tags' => $currentTags]);
        }
    }

    /**
     * Remove a tag from the room
     */
    public function removeTag(string $tagName): void
    {
        $tag = RoomTag::where('name', strtolower(trim($tagName)))->first();
        if ($tag) {
            $this->roomTags()->detach($tag->id);
            
            // Update the tags JSON field
            $currentTags = $this->tags ?? [];
            $currentTags = array_diff($currentTags, [$tagName]);
            $this->update(['tags' => array_values($currentTags)]);
        }
    }

    /**
     * Get analytics for a date range
     */
    public function getAnalytics(\Carbon\Carbon $startDate = null, \Carbon\Carbon $endDate = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->analytics();
        
        if ($startDate) {
            $query->where('date', '>=', $startDate->toDateString());
        }
        
        if ($endDate) {
            $query->where('date', '<=', $endDate->toDateString());
        }
        
        return $query->orderBy('date', 'desc')->get();
    }

    /**
     * Get total duration across all sessions
     */
    public function getTotalDurationMinutes(): int
    {
        return $this->analytics()->sum('total_duration_minutes');
    }

    /**
     * Get average participants per session
     */
    public function getAverageParticipants(): float
    {
        $analytics = $this->analytics();
        $totalParticipants = $analytics->sum('total_participants');
        $totalSessions = $analytics->sum('total_sessions');
        
        return $totalSessions > 0 ? $totalParticipants / $totalSessions : 0;
    }

    /**
     * Get shared notes for display
     */
    public function getSharedNotes()
    {
        return $this->notes()->shared()->orderBy('is_pinned', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Get pending tasks
     */
    public function getPendingTasks()
    {
        return $this->notes()->pendingTasks()->orderBy('due_date', 'asc');
    }

    /**
     * Get room summary for analytics
     */
    public function getSummary(): array
    {
        return [
            'total_sessions' => $this->total_sessions,
            'total_participants' => $this->total_participants_all_time,
            'total_duration_minutes' => $this->getTotalDurationMinutes(),
            'average_participants' => $this->getAverageParticipants(),
            'tags' => $this->tags ?? [],
            'has_recordings' => $this->hasRecordings(),
            'pending_tasks_count' => $this->getPendingTasks()->count(),
            'shared_notes_count' => $this->getSharedNotes()->count(),
            'last_activity' => $this->last_activity_at?->diffForHumans(),
        ];
    }
}
