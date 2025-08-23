@extends('layouts.app')

@section('title', 'Search - ekiliConvo')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Search</h1>
        
        <!-- Search Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form id="search-form" class="space-y-4">
                <div class="flex space-x-4">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            id="search-input" 
                            name="q" 
                            value="{{ $query }}"
                            placeholder="Search rooms, notes, recordings..." 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                    </div>
                    <div>
                        <select id="search-type" name="type" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All</option>
                            <option value="rooms" {{ $type === 'rooms' ? 'selected' : '' }}>Rooms</option>
                            <option value="notes" {{ $type === 'notes' ? 'selected' : '' }}>Notes</option>
                            <option value="recordings" {{ $type === 'recordings' ? 'selected' : '' }}>Recordings</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search Results -->
    <div id="search-results" class="space-y-6">
        @if($query)
        <div class="text-gray-600">
            <p>Searching for: <strong>"{{ $query }}"</strong></p>
        </div>
        @endif
        
        <!-- Results will be loaded here -->
        <div id="results-container">
            @if(!$query)
            <!-- Smart Suggestions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Smart Suggestions</h3>
                <div id="suggestions-container">
                    <div class="text-center text-gray-500 py-8">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <p>Loading suggestions...</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Filters Sidebar (initially hidden) -->
    <div id="filters-sidebar" class="hidden fixed inset-y-0 right-0 w-80 bg-white shadow-lg z-50 overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                <button id="close-filters" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div id="filters-content">
                <!-- Filters will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchType = document.getElementById('search-type');
    const resultsContainer = document.getElementById('results-container');
    const suggestionsContainer = document.getElementById('suggestions-container');

    // Load suggestions on page load if no query
    if (!searchInput.value.trim()) {
        loadSuggestions();
    } else {
        performSearch();
    }

    // Handle search form submission
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    // Handle input changes for live search
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        if (this.value.trim()) {
            searchTimeout = setTimeout(() => performSearch(), 300);
        } else {
            loadSuggestions();
        }
    });

    async function performSearch() {
        const query = searchInput.value.trim();
        const type = searchType.value;
        
        if (!query) {
            loadSuggestions();
            return;
        }

        // Show loading state
        resultsContainer.innerHTML = `
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Searching...</p>
            </div>
        `;

        try {
            const response = await fetch(`/api/search?q=${encodeURIComponent(query)}&type=${type}`, {
                headers: {
                    'Authorization': 'Bearer ' + getCsrfToken(),
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                displayResults(data);
            } else {
                throw new Error('Search failed');
            }
        } catch (error) {
            console.error('Search error:', error);
            resultsContainer.innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <p>Search failed. Please try again.</p>
                </div>
            `;
        }
    }

    async function loadSuggestions() {
        try {
            const response = await fetch('/api/search/suggestions', {
                headers: {
                    'Authorization': 'Bearer ' + getCsrfToken(),
                    'Content-Type': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                displaySuggestions(data.suggestions);
            }
        } catch (error) {
            console.error('Failed to load suggestions:', error);
            suggestionsContainer.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <p>Unable to load suggestions</p>
                </div>
            `;
        }
    }

    function displayResults(data) {
        if (data.total === 0) {
            resultsContainer.innerHTML = `
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
                    <p class="text-gray-600">Try adjusting your search terms or filters</p>
                </div>
            `;
            return;
        }

        let html = `<div class="mb-4 text-sm text-gray-600">Found ${data.total} results</div>`;

        if (data.type === 'all' && data.results.combined) {
            html += renderCombinedResults(data.results.combined);
        } else {
            // Render specific type results
            if (data.results.rooms) html += renderRoomResults(data.results.rooms);
            if (data.results.notes) html += renderNoteResults(data.results.notes);
            if (data.results.recordings) html += renderRecordingResults(data.results.recordings);
        }

        resultsContainer.innerHTML = html;
    }

    function renderCombinedResults(results) {
        return results.map(result => {
            switch (result.type) {
                case 'room':
                    return renderRoomItem(result);
                case 'note':
                    return renderNoteItem(result);
                case 'recording':
                    return renderRecordingItem(result);
                default:
                    return '';
            }
        }).join('');
    }

    function renderRoomResults(rooms) {
        if (!rooms.length) return '';
        
        let html = '<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">';
        html += '<div class="px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Rooms</h3></div>';
        html += '<div class="divide-y divide-gray-200">';
        html += rooms.map(renderRoomItem).join('');
        html += '</div></div>';
        
        return html;
    }

    function renderNoteResults(notes) {
        if (!notes.length) return '';
        
        let html = '<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">';
        html += '<div class="px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Notes</h3></div>';
        html += '<div class="divide-y divide-gray-200">';
        html += notes.map(renderNoteItem).join('');
        html += '</div></div>';
        
        return html;
    }

    function renderRecordingResults(recordings) {
        if (!recordings.length) return '';
        
        let html = '<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">';
        html += '<div class="px-6 py-4 border-b border-gray-200"><h3 class="text-lg font-semibold text-gray-900">Recordings</h3></div>';
        html += '<div class="divide-y divide-gray-200">';
        html += recordings.map(renderRecordingItem).join('');
        html += '</div></div>';
        
        return html;
    }

    function renderRoomItem(room) {
        return `
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">${room.title}</h4>
                            <p class="text-sm text-gray-600">${room.description || 'No description'}</p>
                            <div class="flex items-center space-x-4 mt-1 text-xs text-gray-500">
                                <span>${room.total_sessions} sessions</span>
                                <span>${room.participants_count} participants</span>
                                ${room.category ? `<span class="bg-gray-100 px-2 py-1 rounded">${room.category}</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <a href="${room.url}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Join Room
                    </a>
                </div>
            </div>
        `;
    }

    function renderNoteItem(note) {
        return `
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <h4 class="font-medium text-gray-900">${note.title}</h4>
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">${note.note_type}</span>
                            ${note.is_pinned ? '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pinned</span>' : ''}
                        </div>
                        <div class="text-sm text-gray-600 mb-2">${note.content_excerpt}</div>
                        <div class="text-xs text-gray-500">
                            <span>in ${note.room_name}</span> • 
                            <span>by ${note.author}</span> • 
                            <span>${note.created_at}</span>
                        </div>
                    </div>
                    <a href="${note.url}" class="text-blue-600 hover:text-blue-800 text-sm font-medium ml-4">
                        View
                    </a>
                </div>
            </div>
        `;
    }

    function renderRecordingItem(recording) {
        return `
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">${recording.title}</h4>
                            <div class="text-sm text-gray-600">
                                <span>${recording.duration}</span> • 
                                <span>${recording.file_size}</span> • 
                                <span>${recording.recorded_at}</span>
                            </div>
                        </div>
                    </div>
                    <a href="${recording.url}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Play
                    </a>
                </div>
            </div>
        `;
    }

    function displaySuggestions(suggestions) {
        if (!suggestions.length) {
            suggestionsContainer.innerHTML = `
                <div class="text-center py-4 text-gray-500">
                    <p>No suggestions available</p>
                </div>
            `;
            return;
        }

        const html = suggestions.map(suggestion => `
            <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 mb-1">${suggestion.title}</h4>
                        <p class="text-sm text-gray-600 mb-2">${suggestion.description}</p>
                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">${suggestion.type.replace('_', ' ')}</span>
                    </div>
                    <a href="${suggestion.url}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium ml-4">
                        ${suggestion.action}
                    </a>
                </div>
            </div>
        `).join('');

        suggestionsContainer.innerHTML = html;
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
});
</script>
@endsection