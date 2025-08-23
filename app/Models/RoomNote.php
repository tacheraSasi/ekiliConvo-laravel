<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'user_id',
        'type',
        'title',
        'content',
        'metadata',
        'is_shared',
        'is_pinned',
        'due_date'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_shared' => 'boolean',
        'is_pinned' => 'boolean',
        'due_date' => 'datetime'
    ];

    // Note types
    const TYPE_NOTE = 'note';
    const TYPE_AGENDA = 'agenda';
    const TYPE_TASK = 'task';
    const TYPE_SUMMARY = 'summary';

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if note is a task
     */
    public function isTask(): bool
    {
        return $this->type === self::TYPE_TASK;
    }

    /**
     * Check if task is completed
     */
    public function isCompleted(): bool
    {
        return $this->isTask() && ($this->metadata['status'] ?? '') === 'completed';
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue(): bool
    {
        return $this->isTask() && $this->due_date && $this->due_date->isPast() && !$this->isCompleted();
    }

    /**
     * Get task priority
     */
    public function getPriority(): string
    {
        return $this->metadata['priority'] ?? 'medium';
    }

    /**
     * Mark task as completed
     */
    public function markCompleted(): void
    {
        if ($this->isTask()) {
            $metadata = $this->metadata ?? [];
            $metadata['status'] = 'completed';
            $metadata['completed_at'] = now()->toISOString();
            $this->update(['metadata' => $metadata]);
        }
    }

    /**
     * Get formatted content for display
     */
    public function getFormattedContentAttribute(): string
    {
        // Basic markdown-like formatting
        $content = $this->content;
        
        // Convert **bold** to <strong>
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        
        // Convert *italic* to <em>
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        
        // Convert newlines to <br>
        $content = nl2br($content);
        
        return $content;
    }

    /**
     * Scope to get shared notes
     */
    public function scopeShared($query)
    {
        return $query->where('is_shared', true);
    }

    /**
     * Scope to get pinned notes
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope to get tasks
     */
    public function scopeTasks($query)
    {
        return $query->where('type', self::TYPE_TASK);
    }

    /**
     * Scope to get pending tasks
     */
    public function scopePendingTasks($query)
    {
        return $query->tasks()
                    ->where(function ($q) {
                        $q->whereNull('metadata->status')
                          ->orWhere('metadata->status', '!=', 'completed');
                    });
    }
}
