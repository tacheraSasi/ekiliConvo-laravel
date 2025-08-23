<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RoomTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description'
    ];

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_tag_assignments')
                    ->withTimestamps()
                    ->withPivot('assigned_at');
    }

    /**
     * Get rooms count for this tag
     */
    public function getRoomsCountAttribute(): int
    {
        return $this->rooms()->count();
    }

    /**
     * Get or create a tag by name
     */
    public static function findOrCreateByName(string $name, string $color = '#6B7280'): self
    {
        return static::firstOrCreate(
            ['name' => strtolower(trim($name))],
            ['color' => $color]
        );
    }

    /**
     * Get popular tags with room counts
     */
    public static function getPopularTags(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return static::withCount('rooms')
                    ->orderBy('rooms_count', 'desc')
                    ->limit($limit)
                    ->get();
    }
}
