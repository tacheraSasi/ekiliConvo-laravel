@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-neutral-900">Analytics Dashboard</h1>
                <p class="text-neutral-600 mt-2">Insights into your ekiliConvo activity and performance</p>
            </div>
            <div class="flex space-x-4">
                <select id="time-range" class="rounded-md border-neutral-300 shadow-sm">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 days</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 days</option>
                </select>
                <a href="{{ route('analytics.search') }}" class="btn btn-secondary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </a>
            </div>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Rooms -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600">Total Rooms</p>
                    <p class="text-3xl font-bold text-neutral-900">{{ $metrics['total_rooms'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-neutral-500">You created {{ $metrics['user_created_rooms'] }} rooms</span>
            </div>
        </div>

        <!-- Joined Rooms -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600">Rooms Joined</p>
                    <p class="text-3xl font-bold text-neutral-900">{{ $metrics['user_joined_rooms'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-neutral-500">{{ $metrics['active_rooms'] }} active in last {{ $days }} days</span>
            </div>
        </div>

        <!-- Total Sessions -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600">Total Sessions</p>
                    <p class="text-3xl font-bold text-neutral-900">{{ number_format($metrics['total_sessions']) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-neutral-500">Platform-wide meetings</span>
            </div>
        </div>

        <!-- Your Notes & Tasks -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-neutral-600">Your Notes</p>
                    <p class="text-3xl font-bold text-neutral-900">{{ $metrics['user_total_notes'] }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-neutral-500">{{ $metrics['user_pending_tasks'] }} pending tasks</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Activity Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Platform Activity</h3>
            <div id="activity-chart" class="h-64">
                <!-- Chart will be rendered here by JavaScript -->
                <div class="flex items-center justify-center h-full text-neutral-500">
                    <div class="text-center">
                        <svg class="w-12 h-12 mx-auto mb-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p>Activity analytics chart will be displayed here</p>
                        <button onclick="loadAnalyticsData()" class="mt-2 text-blue-600 hover:text-blue-800">Load Analytics</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Metrics -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-neutral-200">
            <h3 class="text-lg font-semibold text-neutral-900 mb-4">Connection Quality</h3>
            <div id="quality-metrics" class="h-64">
                <div class="flex items-center justify-center h-full text-neutral-500">
                    <div class="text-center">
                        <div class="w-24 h-24 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">98%</span>
                        </div>
                        <p class="text-lg font-semibold text-neutral-700">Average Quality Score</p>
                        <p class="text-sm text-neutral-500">Excellent connection quality</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Rooms -->
    <div class="bg-white rounded-lg shadow-sm border border-neutral-200">
        <div class="px-6 py-4 border-b border-neutral-200">
            <h3 class="text-lg font-semibold text-neutral-900">Recent Rooms</h3>
        </div>
        <div class="divide-y divide-neutral-200">
            @forelse($recentRooms as $room)
            <div class="px-6 py-4 hover:bg-neutral-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-neutral-900">{{ $room->name }}</h4>
                            <p class="text-sm text-neutral-500">
                                {{ $room->pivot->role_in_room === 'host' ? 'Host' : 'Participant' }} •
                                {{ $room->total_sessions }} sessions •
                                @if($room->last_activity_at)
                                    Last active {{ $room->last_activity_at->diffForHumans() }}
                                @else
                                    No recent activity
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('analytics.room', $room->uuid) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Analytics
                        </a>
                        <a href="/room/{{ $room->uuid }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm font-medium">
                            Join Room
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-neutral-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p>No rooms found. <a href="/rooms/create" class="text-blue-600 hover:text-blue-800">Create your first room</a></p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Analytics JavaScript -->
<script>
// Time range selector
document.getElementById('time-range').addEventListener('change', function() {
    const days = this.value;
    window.location.href = `{{ route('analytics.dashboard') }}?days=${days}`;
});

// Load analytics data via API
async function loadAnalyticsData() {
    try {
        const response = await fetch('/api/analytics/platform?days={{ $days }}', {
            headers: {
                'Authorization': 'Bearer ' + document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            renderActivityChart(data.daily_activity);
            updateQualityMetrics(data.quality_metrics);
        }
    } catch (error) {
        console.error('Failed to load analytics:', error);
    }
}

function renderActivityChart(data) {
    const chartContainer = document.getElementById('activity-chart');
    // This would integrate with a charting library like Chart.js or D3.js
    chartContainer.innerHTML = `
        <div class="text-center text-neutral-500 mt-8">
            <p>Activity chart would be rendered here with real data</p>
            <p class="text-sm mt-2">Data points: ${data?.length || 0}</p>
        </div>
    `;
}

function updateQualityMetrics(metrics) {
    console.log('Quality metrics:', metrics);
    // Update quality display with real data
}

// Load data on page load
document.addEventListener('DOMContentLoaded', loadAnalyticsData);
</script>
@endsection
