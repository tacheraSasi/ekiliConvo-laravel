<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomNote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomNotesController extends Controller
{
    /**
     * Get all notes for a room
     */
    public function index(Request $request, string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $type = $request->input('type', 'all');
        $shared_only = $request->boolean('shared_only', false);
        
        $query = $room->notes()->with('user');
        
        if ($type !== 'all') {
            $query->where('type', $type);
        }
        
        if ($shared_only) {
            $query->shared();
        }
        
        $notes = $query->orderBy('is_pinned', 'desc')
                      ->orderBy('created_at', 'desc')
                      ->get();
        
        return response()->json([
            'notes' => $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'type' => $note->type,
                    'title' => $note->title,
                    'content' => $note->content,
                    'formatted_content' => $note->formatted_content,
                    'is_shared' => $note->is_shared,
                    'is_pinned' => $note->is_pinned,
                    'due_date' => $note->due_date,
                    'metadata' => $note->metadata,
                    'author' => [
                        'id' => $note->user->id,
                        'name' => $note->user->name,
                        'avatar_url' => $note->user->getAvatarUrl()
                    ],
                    'created_at' => $note->created_at,
                    'updated_at' => $note->updated_at,
                    // Task-specific fields
                    'is_task' => $note->isTask(),
                    'is_completed' => $note->isCompleted(),
                    'is_overdue' => $note->isOverdue(),
                    'priority' => $note->getPriority()
                ];
            })
        ]);
    }

    /**
     * Create a new note
     */
    public function store(Request $request, string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        
        $validated = $request->validate([
            'type' => 'required|in:note,agenda,task,summary',
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_shared' => 'boolean',
            'is_pinned' => 'boolean',
            'due_date' => 'nullable|date',
            'metadata' => 'array',
            'metadata.priority' => 'nullable|in:low,medium,high',
            'metadata.status' => 'nullable|in:pending,in_progress,completed'
        ]);
        
        $note = $room->notes()->create([
            'user_id' => $request->user()->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'is_shared' => $validated['is_shared'] ?? true,
            'is_pinned' => $validated['is_pinned'] ?? false,
            'due_date' => $validated['due_date'] ?? null,
            'metadata' => $validated['metadata'] ?? []
        ]);
        
        $note->load('user');
        
        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'type' => $note->type,
                'title' => $note->title,
                'content' => $note->content,
                'formatted_content' => $note->formatted_content,
                'is_shared' => $note->is_shared,
                'is_pinned' => $note->is_pinned,
                'due_date' => $note->due_date,
                'metadata' => $note->metadata,
                'author' => [
                    'id' => $note->user->id,
                    'name' => $note->user->name,
                    'avatar_url' => $note->user->getAvatarUrl()
                ],
                'created_at' => $note->created_at,
                'is_task' => $note->isTask(),
                'is_completed' => $note->isCompleted(),
                'priority' => $note->getPriority()
            ]
        ], 201);
    }

    /**
     * Update a note
     */
    public function update(Request $request, string $roomUuid, int $noteId): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $note = $room->notes()->findOrFail($noteId);
        
        // Check if user can edit this note
        if ($note->user_id !== $request->user()->id && !$room->isHost($request->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'string',
            'is_shared' => 'boolean',
            'is_pinned' => 'boolean',
            'due_date' => 'nullable|date',
            'metadata' => 'array',
            'metadata.priority' => 'nullable|in:low,medium,high',
            'metadata.status' => 'nullable|in:pending,in_progress,completed'
        ]);
        
        $note->update($validated);
        $note->load('user');
        
        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'type' => $note->type,
                'title' => $note->title,
                'content' => $note->content,
                'formatted_content' => $note->formatted_content,
                'is_shared' => $note->is_shared,
                'is_pinned' => $note->is_pinned,
                'due_date' => $note->due_date,
                'metadata' => $note->metadata,
                'author' => [
                    'id' => $note->user->id,
                    'name' => $note->user->name,
                    'avatar_url' => $note->user->getAvatarUrl()
                ],
                'updated_at' => $note->updated_at,
                'is_task' => $note->isTask(),
                'is_completed' => $note->isCompleted(),
                'is_overdue' => $note->isOverdue(),
                'priority' => $note->getPriority()
            ]
        ]);
    }

    /**
     * Delete a note
     */
    public function destroy(Request $request, string $roomUuid, int $noteId): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $note = $room->notes()->findOrFail($noteId);
        
        // Check if user can delete this note
        if ($note->user_id !== $request->user()->id && !$room->isHost($request->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $note->delete();
        
        return response()->json(['success' => true]);
    }

    /**
     * Mark a task as completed
     */
    public function markCompleted(Request $request, string $roomUuid, int $noteId): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $note = $room->notes()->findOrFail($noteId);
        
        if (!$note->isTask()) {
            return response()->json(['error' => 'Note is not a task'], 400);
        }
        
        $note->markCompleted();
        
        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'is_completed' => $note->isCompleted(),
                'metadata' => $note->metadata
            ]
        ]);
    }

    /**
     * Toggle pin status of a note
     */
    public function togglePin(Request $request, string $roomUuid, int $noteId): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $note = $room->notes()->findOrFail($noteId);
        
        // Only shared notes can be pinned, and only hosts can pin
        if (!$note->is_shared || !$room->isHost($request->user())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $note->update(['is_pinned' => !$note->is_pinned]);
        
        return response()->json([
            'success' => true,
            'is_pinned' => $note->is_pinned
        ]);
    }

    /**
     * Get notes summary for room
     */
    public function summary(Request $request, string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        
        $totalNotes = $room->notes()->count();
        $sharedNotes = $room->notes()->shared()->count();
        $pinnedNotes = $room->notes()->pinned()->count();
        $totalTasks = $room->notes()->tasks()->count();
        $pendingTasks = $room->getPendingTasks()->count();
        $completedTasks = $room->notes()->tasks()
            ->where('metadata->status', 'completed')
            ->count();
        
        $recentNotes = $room->notes()
            ->shared()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'summary' => [
                'total_notes' => $totalNotes,
                'shared_notes' => $sharedNotes,
                'pinned_notes' => $pinnedNotes,
                'total_tasks' => $totalTasks,
                'pending_tasks' => $pendingTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0
            ],
            'recent_notes' => $recentNotes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'type' => $note->type,
                    'title' => $note->title ?: substr($note->content, 0, 50) . '...',
                    'author_name' => $note->user->name,
                    'created_at' => $note->created_at->diffForHumans(),
                    'is_pinned' => $note->is_pinned
                ];
            })
        ]);
    }
}
