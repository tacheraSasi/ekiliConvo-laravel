<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomAnalytics;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get overall platform analytics
     */
    public function getPlatformAnalytics(Request $request): JsonResponse
    {
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);
        
        // Basic platform metrics
        $totalRooms = Room::count();
        $totalUsers = User::count();
        $activeRooms = Room::where('last_activity_at', '>=', $startDate)->count();
        $totalSessions = Room::sum('total_sessions');
        
        // Analytics aggregation
        $analyticsData = RoomAnalytics::where('date', '>=', $startDate->toDateString())
            ->selectRaw('
                SUM(total_participants) as total_participants,
                SUM(total_duration_minutes) as total_duration,
                SUM(total_sessions) as total_sessions,
                MAX(max_concurrent_participants) as peak_concurrent,
                AVG(total_participants / NULLIF(total_sessions, 0)) as avg_participants_per_session
            ')
            ->first();
        
        // Daily activity for heatmap
        $dailyActivity = RoomAnalytics::where('date', '>=', $startDate->toDateString())
            ->selectRaw('date, SUM(total_sessions) as sessions, SUM(total_participants) as participants')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Popular room categories
        $categoryStats = Room::selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'period' => "{$days} days",
            'overview' => [
                'total_rooms' => $totalRooms,
                'total_users' => $totalUsers,
                'active_rooms' => $activeRooms,
                'total_sessions' => $totalSessions,
                'total_participants' => $analyticsData->total_participants ?? 0,
                'total_duration_hours' => round(($analyticsData->total_duration ?? 0) / 60, 1),
                'peak_concurrent' => $analyticsData->peak_concurrent ?? 0,
                'avg_participants_per_session' => round($analyticsData->avg_participants_per_session ?? 0, 1)
            ],
            'daily_activity' => $dailyActivity,
            'category_breakdown' => $categoryStats,
            'quality_metrics' => $this->getQualityMetrics($startDate)
        ]);
    }

    /**
     * Get analytics for a specific room
     */
    public function getRoomAnalytics(Request $request, string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);
        
        // Basic room info
        $roomData = [
            'id' => $room->id,
            'name' => $room->name,
            'uuid' => $room->uuid,
            'created_at' => $room->created_at,
            'total_sessions' => $room->total_sessions,
            'total_participants_all_time' => $room->total_participants_all_time,
            'last_activity_at' => $room->last_activity_at,
            'category' => $room->category,
            'tags' => $room->tags ?? []
        ];
        
        // Get analytics for the period
        $analytics = $room->getAnalytics($startDate);
        
        // Aggregate analytics
        $totalDuration = $analytics->sum('total_duration_minutes');
        $totalParticipants = $analytics->sum('total_participants');
        $totalSessions = $analytics->sum('total_sessions');
        $peakConcurrent = $analytics->max('max_concurrent_participants') ?? 0;
        
        // Participant trends
        $participantTrends = $analytics->map(function ($item) {
            return [
                'date' => $item->date->format('Y-m-d'),
                'participants' => $item->total_participants,
                'sessions' => $item->total_sessions,
                'duration' => $item->total_duration_minutes,
                'quality_score' => $item->quality_score
            ];
        });
        
        // Get room notes and tasks summary
        $notesCount = $room->notes()->count();
        $tasksCount = $room->notes()->tasks()->count();
        $pendingTasksCount = $room->getPendingTasks()->count();
        
        return response()->json([
            'room' => $roomData,
            'period' => "{$days} days",
            'summary' => [
                'total_duration_hours' => round($totalDuration / 60, 1),
                'total_participants' => $totalParticipants,
                'total_sessions' => $totalSessions,
                'average_session_duration' => $totalSessions > 0 ? round($totalDuration / $totalSessions, 1) : 0,
                'average_participants_per_session' => $totalSessions > 0 ? round($totalParticipants / $totalSessions, 1) : 0,
                'peak_concurrent_participants' => $peakConcurrent,
                'notes_count' => $notesCount,
                'tasks_count' => $tasksCount,
                'pending_tasks_count' => $pendingTasksCount
            ],
            'trends' => $participantTrends,
            'recent_activity' => $this->getRecentActivity($room)
        ]);
    }

    /**
     * Get user analytics
     */
    public function getUserAnalytics(Request $request): JsonResponse
    {
        $user = $request->user();
        $days = $request->input('days', 30);
        
        $summary = $user->getActivitySummary();
        
        // Get user's room participation data
        $recentRooms = $user->joinedRooms()
            ->with(['analytics' => function ($query) use ($days) {
                $query->where('date', '>=', Carbon::now()->subDays($days)->toDateString());
            }])
            ->orderBy('room_users.created_at', 'desc')
            ->limit(10)
            ->get();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar_url' => $user->getAvatarUrl(),
                'preferred_language' => $user->getLanguage(),
                'is_online' => $user->is_online
            ],
            'summary' => $summary,
            'recent_rooms' => $recentRooms->map(function ($room) {
                return [
                    'uuid' => $room->uuid,
                    'name' => $room->name,
                    'role' => $room->pivot->role_in_room,
                    'joined_at' => $room->pivot->joined_at,
                    'last_activity' => $room->last_activity_at,
                    'total_sessions' => $room->total_sessions
                ];
            })
        ]);
    }

    /**
     * Record analytics data for a room session
     */
    public function recordSessionData(Request $request, string $roomUuid): JsonResponse
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        
        $validated = $request->validate([
            'participants' => 'required|integer|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'max_concurrent' => 'required|integer|min:0',
            'quality_metrics' => 'array',
            'participant_data' => 'array'
        ]);
        
        $today = Carbon::now()->toDateString();
        
        // Get or create analytics record for today
        $analytics = RoomAnalytics::firstOrCreate(
            ['room_id' => $room->id, 'date' => $today],
            [
                'total_participants' => 0,
                'max_concurrent_participants' => 0,
                'total_duration_minutes' => 0,
                'total_sessions' => 0
            ]
        );
        
        // Update analytics
        $analytics->update([
            'total_participants' => $analytics->total_participants + $validated['participants'],
            'max_concurrent_participants' => max($analytics->max_concurrent_participants, $validated['max_concurrent']),
            'total_duration_minutes' => $analytics->total_duration_minutes + $validated['duration_minutes'],
            'total_sessions' => $analytics->total_sessions + 1,
            'quality_metrics' => array_merge($analytics->quality_metrics ?? [], $validated['quality_metrics'] ?? []),
            'participant_data' => array_merge($analytics->participant_data ?? [], $validated['participant_data'] ?? [])
        ]);
        
        // Update room totals
        $room->increment('total_sessions');
        $room->update([
            'total_participants_all_time' => $room->total_participants_all_time + $validated['participants'],
            'last_activity_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'analytics_id' => $analytics->id
        ]);
    }

    /**
     * Get quality metrics for a period
     */
    private function getQualityMetrics(Carbon $startDate): array
    {
        $analytics = RoomAnalytics::where('date', '>=', $startDate->toDateString())
            ->whereNotNull('quality_metrics')
            ->get();
        
        if ($analytics->isEmpty()) {
            return [
                'average_quality_score' => 100,
                'connection_issues' => 0,
                'total_reconnections' => 0
            ];
        }
        
        $totalScore = 0;
        $totalReconnections = 0;
        $connectionIssues = 0;
        $count = 0;
        
        foreach ($analytics as $record) {
            $metrics = $record->quality_metrics;
            if (isset($metrics['quality_score'])) {
                $totalScore += $metrics['quality_score'];
                $count++;
            }
            if (isset($metrics['reconnections'])) {
                $totalReconnections += $metrics['reconnections'];
                if ($metrics['reconnections'] > 2) {
                    $connectionIssues++;
                }
            }
        }
        
        return [
            'average_quality_score' => $count > 0 ? round($totalScore / $count, 1) : 100,
            'connection_issues' => $connectionIssues,
            'total_reconnections' => $totalReconnections
        ];
    }

    /**
     * Get recent activity for a room
     */
    private function getRecentActivity(Room $room): array
    {
        $recentLogs = $room->auditLogs()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return $recentLogs->map(function ($log) {
            return [
                'action' => $log->action,
                'actor_name' => $log->actor_name,
                'target_name' => $log->target_name,
                'details' => $log->details,
                'created_at' => $log->created_at
            ];
        })->toArray();
    }
}
