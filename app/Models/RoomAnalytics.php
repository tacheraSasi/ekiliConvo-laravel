<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'date',
        'total_participants',
        'max_concurrent_participants',
        'total_duration_minutes',
        'total_sessions',
        'participant_data',
        'quality_metrics',
        'activity_summary'
    ];

    protected $casts = [
        'date' => 'date',
        'participant_data' => 'array',
        'quality_metrics' => 'array',
        'activity_summary' => 'array'
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->total_duration_minutes / 60);
        $minutes = $this->total_duration_minutes % 60;
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m";
    }

    /**
     * Calculate average session duration
     */
    public function getAverageSessionDurationAttribute(): float
    {
        return $this->total_sessions > 0 ? $this->total_duration_minutes / $this->total_sessions : 0;
    }

    /**
     * Get participant retention rate (participants who joined vs those who stayed)
     */
    public function getParticipantRetentionRateAttribute(): float
    {
        if (!$this->participant_data || !isset($this->participant_data['joins']) || !isset($this->participant_data['completions'])) {
            return 0;
        }

        $joins = $this->participant_data['joins'] ?? 0;
        $completions = $this->participant_data['completions'] ?? 0;

        return $joins > 0 ? ($completions / $joins) * 100 : 0;
    }

    /**
     * Get quality score based on metrics
     */
    public function getQualityScoreAttribute(): int
    {
        if (!$this->quality_metrics) {
            return 100; // Default good quality
        }

        $metrics = $this->quality_metrics;
        $score = 100;

        // Deduct points for poor quality indicators
        if (isset($metrics['avg_latency']) && $metrics['avg_latency'] > 200) {
            $score -= 20;
        }
        if (isset($metrics['packet_loss']) && $metrics['packet_loss'] > 2) {
            $score -= 30;
        }
        if (isset($metrics['reconnections']) && $metrics['reconnections'] > 5) {
            $score -= 25;
        }

        return max(0, min(100, $score));
    }

    /**
     * Create or update analytics for a room and date
     */
    public static function updateAnalytics(int $roomId, array $data): self
    {
        $date = $data['date'] ?? now()->toDateString();
        
        return static::updateOrCreate(
            ['room_id' => $roomId, 'date' => $date],
            $data
        );
    }
}
