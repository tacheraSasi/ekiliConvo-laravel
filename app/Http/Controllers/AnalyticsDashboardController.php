<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomAnalytics;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class AnalyticsDashboardController extends Controller
{
    /**
     * Show the analytics dashboard
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);
        
        // Basic metrics for the dashboard
        $metrics = [
            'total_rooms' => Room::count(),
            'user_created_rooms' => $user->createdRooms()->count(),
            'user_joined_rooms' => $user->joinedRooms()->count(),
            'active_rooms' => Room::where('last_activity_at', '>=', $startDate)->count(),
            'total_sessions' => Room::sum('total_sessions'),
            'user_total_notes' => $user->notes()->count(),
            'user_pending_tasks' => $user->notes()->pendingTasks()->count()
        ];
        
        // Recent rooms for quick access
        $recentRooms = $user->joinedRooms()
            ->orderBy('room_users.updated_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('analytics.dashboard', [
            'metrics' => $metrics,
            'recentRooms' => $recentRooms,
            'days' => $days,
            'user' => $user
        ]);
    }

    /**
     * Show room-specific analytics
     */
    public function room(Request $request, string $roomUuid): View
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        
        // Check if user has access to this room
        if (!$room->isParticipant($request->user()) && !$room->isHost($request->user())) {
            abort(403, 'You do not have access to this room.');
        }
        
        $days = $request->input('days', 30);
        $startDate = Carbon::now()->subDays($days);
        
        // Get analytics for the period
        $analytics = $room->getAnalytics($startDate);
        
        // Calculate summary metrics
        $summary = [
            'total_duration_hours' => round($analytics->sum('total_duration_minutes') / 60, 1),
            'total_participants' => $analytics->sum('total_participants'),
            'total_sessions' => $analytics->sum('total_sessions'),
            'average_participants' => $analytics->avg('total_participants') ?? 0,
            'peak_concurrent' => $analytics->max('max_concurrent_participants') ?? 0,
            'notes_count' => $room->notes()->count(),
            'tasks_count' => $room->notes()->tasks()->count(),
            'pending_tasks_count' => $room->getPendingTasks()->count()
        ];
        
        return view('analytics.room', [
            'room' => $room,
            'analytics' => $analytics,
            'summary' => $summary,
            'days' => $days
        ]);
    }

    /**
     * Show search interface
     */
    public function search(Request $request): View
    {
        $query = $request->input('q', '');
        $type = $request->input('type', 'all');
        
        return view('analytics.search', [
            'query' => $query,
            'type' => $type
        ]);
    }

    /**
     * Show user profile and settings
     */
    public function profile(Request $request): View
    {
        $user = $request->user();
        $summary = $user->getActivitySummary();
        
        return view('analytics.profile', [
            'user' => $user,
            'summary' => $summary
        ]);
    }
}
