<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomNote;
use App\Models\RoomTag;
use App\Models\RoomRecording;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    /**
     * Search across rooms, notes, and recordings
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all'); // all, rooms, notes, recordings
        $limit = $request->input('limit', 20);
        
        if (empty($query)) {
            return response()->json([
                'query' => $query,
                'results' => [],
                'total' => 0
            ]);
        }
        
        $results = [];
        
        if ($type === 'all' || $type === 'rooms') {
            $results['rooms'] = $this->searchRooms($query, $limit);
        }
        
        if ($type === 'all' || $type === 'notes') {
            $results['notes'] = $this->searchNotes($query, $limit, $request->user());
        }
        
        if ($type === 'all' || $type === 'recordings') {
            $results['recordings'] = $this->searchRecordings($query, $limit, $request->user());
        }
        
        // If searching all, combine and sort by relevance
        if ($type === 'all') {
            $combined = collect($results)->flatten(1)->sortByDesc('relevance_score')->take($limit);
            $results = ['combined' => $combined->values()];
        }
        
        $total = collect($results)->flatten(1)->count();
        
        return response()->json([
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'total' => $total
        ]);
    }

    /**
     * Search rooms by name, description, category, or tags
     */
    private function searchRooms(string $query, int $limit): array
    {
        $rooms = Room::where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('category', 'LIKE', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            })
            ->with(['creator', 'roomTags'])
            ->orderBy('last_activity_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $rooms->map(function ($room) use ($query) {
            return [
                'type' => 'room',
                'id' => $room->uuid,
                'title' => $room->name,
                'description' => $room->description,
                'category' => $room->category,
                'tags' => $room->tags ?? [],
                'creator' => $room->creator->name,
                'last_activity' => $room->last_activity_at?->diffForHumans(),
                'total_sessions' => $room->total_sessions,
                'participants_count' => $room->total_participants_all_time,
                'relevance_score' => $this->calculateRoomRelevance($room, $query),
                'url' => "/room/{$room->uuid}"
            ];
        })->toArray();
    }

    /**
     * Search notes by title and content
     */
    private function searchNotes(string $query, int $limit, $user): array
    {
        $notes = RoomNote::where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%");
            })
            ->where('is_shared', true) // Only search shared notes
            ->with(['room', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $notes->map(function ($note) use ($query) {
            return [
                'type' => 'note',
                'id' => $note->id,
                'title' => $note->title ?: 'Untitled ' . ucfirst($note->type),
                'content_excerpt' => $this->getExcerpt($note->content, $query),
                'note_type' => $note->type,
                'room_name' => $note->room->name,
                'room_uuid' => $note->room->uuid,
                'author' => $note->user->name,
                'created_at' => $note->created_at->diffForHumans(),
                'is_pinned' => $note->is_pinned,
                'is_task' => $note->isTask(),
                'is_completed' => $note->isCompleted(),
                'relevance_score' => $this->calculateNoteRelevance($note, $query),
                'url' => "/room/{$note->room->uuid}#note-{$note->id}"
            ];
        })->toArray();
    }

    /**
     * Search recordings by metadata
     */
    private function searchRecordings(string $query, int $limit, $user): array
    {
        $recordings = RoomRecording::whereHas('room', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->orWhereJsonContains('metadata', $query)
            ->with('room')
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();
        
        return $recordings->map(function ($recording) use ($query) {
            return [
                'type' => 'recording',
                'id' => $recording->id,
                'title' => $recording->room->name . ' Recording',
                'room_name' => $recording->room->name,
                'room_uuid' => $recording->room->uuid,
                'duration' => $recording->formatted_duration,
                'file_size' => $recording->formatted_file_size,
                'recorded_at' => $recording->started_at->diffForHumans(),
                'relevance_score' => $this->calculateRecordingRelevance($recording, $query),
                'url' => "/recordings/{$recording->id}/play"
            ];
        })->toArray();
    }

    /**
     * Get smart suggestions based on user activity
     */
    public function suggestions(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->input('limit', 10);
        
        $suggestions = [];
        
        // Recent rooms
        $recentRooms = $user->joinedRooms()
            ->where('last_activity_at', '>=', now()->subDays(7))
            ->orderBy('last_activity_at', 'desc')
            ->limit(5)
            ->get();
        
        foreach ($recentRooms as $room) {
            $suggestions[] = [
                'type' => 'recent_room',
                'title' => "Continue working in {$room->name}",
                'description' => "Last active {$room->last_activity_at->diffForHumans()}",
                'action' => "Join Room",
                'url' => "/room/{$room->uuid}",
                'priority' => 'high'
            ];
        }
        
        // Rooms with pending tasks
        $roomsWithTasks = Room::whereHas('notes', function ($q) {
                $q->pendingTasks();
            })
            ->whereHas('users', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->with(['notes' => function ($q) {
                $q->pendingTasks()->limit(3);
            }])
            ->limit(3)
            ->get();
        
        foreach ($roomsWithTasks as $room) {
            $taskCount = $room->notes->count();
            $suggestions[] = [
                'type' => 'pending_tasks',
                'title' => "You have {$taskCount} pending task(s) in {$room->name}",
                'description' => "Complete your action items",
                'action' => "View Tasks",
                'url' => "/room/{$room->uuid}#tasks",
                'priority' => 'medium'
            ];
        }
        
        // Popular tags for discovery
        $popularTags = RoomTag::getPopularTags(5);
        foreach ($popularTags as $tag) {
            if ($tag->rooms_count > 1) {
                $suggestions[] = [
                    'type' => 'explore_category',
                    'title' => "Explore {$tag->name} rooms",
                    'description' => "{$tag->rooms_count} rooms available",
                    'action' => "Browse",
                    'url' => "/search?tag={$tag->name}",
                    'priority' => 'low'
                ];
            }
        }
        
        // Sort by priority and limit
        $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
        usort($suggestions, function ($a, $b) use ($priorityOrder) {
            return $priorityOrder[$b['priority']] - $priorityOrder[$a['priority']];
        });
        
        return response()->json([
            'suggestions' => array_slice($suggestions, 0, $limit)
        ]);
    }

    /**
     * Get search filters and facets
     */
    public function filters(Request $request): JsonResponse
    {
        $categories = Room::select('category')
            ->whereNotNull('category')
            ->groupBy('category')
            ->pluck('category');
        
        $tags = RoomTag::getPopularTags(20);
        
        $noteTypes = [
            ['value' => 'note', 'label' => 'Notes', 'count' => RoomNote::where('type', 'note')->count()],
            ['value' => 'agenda', 'label' => 'Agendas', 'count' => RoomNote::where('type', 'agenda')->count()],
            ['value' => 'task', 'label' => 'Tasks', 'count' => RoomNote::where('type', 'task')->count()],
            ['value' => 'summary', 'label' => 'Summaries', 'count' => RoomNote::where('type', 'summary')->count()]
        ];
        
        return response()->json([
            'categories' => $categories,
            'tags' => $tags->map(function ($tag) {
                return [
                    'name' => $tag->name,
                    'color' => $tag->color,
                    'count' => $tag->rooms_count
                ];
            }),
            'note_types' => $noteTypes
        ]);
    }

    /**
     * Calculate relevance score for room
     */
    private function calculateRoomRelevance(Room $room, string $query): float
    {
        $score = 0;
        $queryLower = strtolower($query);
        
        // Name match (highest weight)
        if (stripos($room->name, $query) !== false) {
            $score += 10;
            if (stripos($room->name, $query) === 0) {
                $score += 5; // Bonus for starting with query
            }
        }
        
        // Description match
        if ($room->description && stripos($room->description, $query) !== false) {
            $score += 5;
        }
        
        // Category match
        if ($room->category && stripos($room->category, $query) !== false) {
            $score += 3;
        }
        
        // Tags match
        if ($room->tags && in_array($queryLower, array_map('strtolower', $room->tags))) {
            $score += 7;
        }
        
        // Recent activity bonus
        if ($room->last_activity_at && $room->last_activity_at->isAfter(now()->subDays(7))) {
            $score += 2;
        }
        
        return $score;
    }

    /**
     * Calculate relevance score for note
     */
    private function calculateNoteRelevance(RoomNote $note, string $query): float
    {
        $score = 0;
        
        // Title match
        if ($note->title && stripos($note->title, $query) !== false) {
            $score += 10;
        }
        
        // Content match
        if (stripos($note->content, $query) !== false) {
            $score += 5;
        }
        
        // Pinned notes get bonus
        if ($note->is_pinned) {
            $score += 3;
        }
        
        // Recent notes get bonus
        if ($note->created_at->isAfter(now()->subDays(7))) {
            $score += 2;
        }
        
        return $score;
    }

    /**
     * Calculate relevance score for recording
     */
    private function calculateRecordingRelevance(RoomRecording $recording, string $query): float
    {
        $score = 0;
        
        // Room name match
        if (stripos($recording->room->name, $query) !== false) {
            $score += 8;
        }
        
        // Recent recordings get bonus
        if ($recording->completed_at && $recording->completed_at->isAfter(now()->subDays(7))) {
            $score += 2;
        }
        
        return $score;
    }

    /**
     * Get excerpt around search term
     */
    private function getExcerpt(string $content, string $query, int $length = 150): string
    {
        $pos = stripos($content, $query);
        if ($pos === false) {
            return substr($content, 0, $length) . '...';
        }
        
        $start = max(0, $pos - 50);
        $excerpt = substr($content, $start, $length);
        
        if ($start > 0) {
            $excerpt = '...' . $excerpt;
        }
        
        if (strlen($content) > $start + $length) {
            $excerpt .= '...';
        }
        
        // Highlight the search term
        $excerpt = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', $excerpt);
        
        return $excerpt;
    }
}
