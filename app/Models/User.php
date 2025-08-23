<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar_url',
        'bio',
        'preferred_language',
        'notification_preferences',
        'accessibility_settings',
        'timezone',
        'is_online',
        'last_seen_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'accessibility_settings' => 'array',
            'is_online' => 'boolean'
        ];
    }

    public function createdRooms(): HasMany
    {
        return $this->hasMany(Room::class, 'creator_id');
    }

    public function joinedRooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_users')
                    ->withPivot('role_in_room', 'joined_at')
                    ->withTimestamps();
    }

    public function notes(): HasMany
    {
        return $this->hasMany(RoomNote::class);
    }

    /**
     * Update user's online status
     */
    public function setOnline(): void
    {
        $this->update([
            'is_online' => true,
            'last_seen_at' => now()
        ]);
    }

    /**
     * Set user offline
     */
    public function setOffline(): void
    {
        $this->update([
            'is_online' => false,
            'last_seen_at' => now()
        ]);
    }

    /**
     * Get user's preferred language or default
     */
    public function getLanguage(): string
    {
        return $this->preferred_language ?? 'en';
    }

    /**
     * Check if user has notification enabled for a type
     */
    public function hasNotificationEnabled(string $type): bool
    {
        $preferences = $this->notification_preferences ?? [];
        return $preferences[$type] ?? true; // Default to enabled
    }

    /**
     * Get user's avatar URL or default
     */
    public function getAvatarUrl(): string
    {
        return $this->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=74f5a1&color=000';
    }

    /**
     * Get user's total meeting time
     */
    public function getTotalMeetingMinutes(): int
    {
        return $this->joinedRooms()
                    ->with('analytics')
                    ->get()
                    ->flatMap->analytics
                    ->sum('total_duration_minutes');
    }

    /**
     * Get user's activity summary
     */
    public function getActivitySummary(): array
    {
        $createdRooms = $this->createdRooms()->count();
        $joinedRooms = $this->joinedRooms()->count();
        $totalNotes = $this->notes()->count();
        $pendingTasks = $this->notes()->pendingTasks()->count();
        
        return [
            'rooms_created' => $createdRooms,
            'rooms_joined' => $joinedRooms,
            'total_notes' => $totalNotes,
            'pending_tasks' => $pendingTasks,
            'total_meeting_minutes' => $this->getTotalMeetingMinutes(),
            'last_seen' => $this->last_seen_at?->diffForHumans(),
            'is_online' => $this->is_online
        ];
    }
}
