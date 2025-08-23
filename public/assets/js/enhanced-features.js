// Enhanced Analytics and Collaboration Features for ekiliConvo
let analyticsData = {
    sessionStart: null,
    participantCount: 0,
    maxConcurrent: 0,
    qualityMetrics: {
        latency: [],
        packetLoss: 0,
        reconnections: 0
    }
};

let notesManager = {
    notes: [],
    currentFilter: 'all'
};

/**
 * Initialize enhanced features
 */
function initializeEnhancedFeatures() {
    initializeAnalyticsTracking();
    initializeNotesInterface();
    initializeSearchInterface();
    initializeQualityMonitoring();
    
    console.log('Enhanced features initialized');
}

/**
 * Analytics Tracking
 */
function initializeAnalyticsTracking() {
    analyticsData.sessionStart = new Date();
    
    // Track participant count changes
    window.addEventListener('participant-joined', (event) => {
        analyticsData.participantCount++;
        analyticsData.maxConcurrent = Math.max(analyticsData.maxConcurrent, analyticsData.participantCount);
        updateParticipantAnalytics();
    });
    
    window.addEventListener('participant-left', (event) => {
        analyticsData.participantCount = Math.max(0, analyticsData.participantCount - 1);
    });
    
    // Track quality metrics
    setInterval(checkConnectionQuality, 30000); // Every 30 seconds
    
    // Send analytics when session ends
    window.addEventListener('beforeunload', recordSessionAnalytics);
}

function checkConnectionQuality() {
    if (client && localTracks && localTracks.length > 0) {
        // Simulate quality metrics (in real implementation, get from Agora SDK)
        const latency = Math.random() * 200 + 50; // 50-250ms
        const packetLoss = Math.random() * 5; // 0-5%
        
        analyticsData.qualityMetrics.latency.push(latency);
        
        if (packetLoss > 2) {
            analyticsData.qualityMetrics.packetLoss++;
        }
        
        updateQualityIndicator(latency, packetLoss);
    }
}

function updateQualityIndicator(latency, packetLoss) {
    let qualityScore = 100;
    if (latency > 150) qualityScore -= 20;
    if (latency > 200) qualityScore -= 20;
    if (packetLoss > 1) qualityScore -= 30;
    
    const indicator = document.getElementById('quality-indicator');
    if (indicator) {
        const color = qualityScore > 80 ? 'green' : qualityScore > 60 ? 'yellow' : 'red';
        indicator.innerHTML = `
            <div class="quality-indicator ${color}">
                <span class="quality-score">${Math.round(qualityScore)}</span>
                <span class="quality-label">Quality</span>
            </div>
        `;
    }
}

async function recordSessionAnalytics() {
    if (!analyticsData.sessionStart || !ROOM_UUID) return;
    
    const sessionDuration = Math.round((new Date() - analyticsData.sessionStart) / 60000); // minutes
    const avgLatency = analyticsData.qualityMetrics.latency.length > 0 
        ? analyticsData.qualityMetrics.latency.reduce((a, b) => a + b) / analyticsData.qualityMetrics.latency.length 
        : 0;
    
    const data = {
        participants: analyticsData.participantCount,
        duration_minutes: sessionDuration,
        max_concurrent: analyticsData.maxConcurrent,
        quality_metrics: {
            avg_latency: avgLatency,
            packet_loss: analyticsData.qualityMetrics.packetLoss,
            reconnections: analyticsData.qualityMetrics.reconnections,
            quality_score: calculateQualityScore(avgLatency, analyticsData.qualityMetrics.packetLoss)
        },
        participant_data: {
            joins: analyticsData.maxConcurrent,
            completions: analyticsData.participantCount // Simplified
        }
    };
    
    try {
        await fetch(`/api/rooms/${ROOM_UUID}/analytics/record-session`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
    } catch (error) {
        console.error('Failed to record session analytics:', error);
    }
}

function calculateQualityScore(latency, packetLoss) {
    let score = 100;
    if (latency > 150) score -= 20;
    if (latency > 200) score -= 20;
    if (packetLoss > 1) score -= 30;
    return Math.max(0, score);
}

/**
 * Notes and Collaboration Interface
 */
function initializeNotesInterface() {
    addNotesPanel();
    loadRoomNotes();
    setupNotesEventListeners();
}

function addNotesPanel() {
    const notesPanel = `
        <div id="notes-panel" class="notes-panel hidden">
            <div class="notes-header">
                <h3>Room Notes & Tasks</h3>
                <div class="notes-controls">
                    <select id="notes-filter">
                        <option value="all">All</option>
                        <option value="note">Notes</option>
                        <option value="agenda">Agenda</option>
                        <option value="task">Tasks</option>
                        <option value="summary">Summary</option>
                    </select>
                    <button id="add-note-btn" class="btn-primary">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                        </svg>
                        Add Note
                    </button>
                </div>
            </div>
            <div id="notes-content" class="notes-content">
                <!-- Notes will be loaded here -->
            </div>
        </div>
        
        <!-- Add Note Modal -->
        <div id="note-modal" class="modal hidden">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="note-modal-title">Add Note</h4>
                    <button id="close-note-modal" class="close-modal">&times;</button>
                </div>
                <form id="note-form">
                    <div class="form-group">
                        <label for="note-type">Type</label>
                        <select id="note-type" required>
                            <option value="note">Note</option>
                            <option value="agenda">Agenda Item</option>
                            <option value="task">Task</option>
                            <option value="summary">Summary</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note-title">Title (optional)</label>
                        <input type="text" id="note-title" placeholder="Note title">
                    </div>
                    <div class="form-group">
                        <label for="note-content">Content</label>
                        <textarea id="note-content" required placeholder="Enter your note content..."></textarea>
                    </div>
                    <div class="form-group" id="task-fields" style="display: none;">
                        <label for="note-due-date">Due Date</label>
                        <input type="datetime-local" id="note-due-date">
                        <label for="note-priority">Priority</label>
                        <select id="note-priority">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="note-shared" checked>
                            Share with all participants
                        </label>
                    </div>
                    <div class="modal-actions">
                        <button type="button" id="cancel-note">Cancel</button>
                        <button type="submit" class="btn-primary">Save Note</button>
                    </div>
                </form>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', notesPanel);
}

function setupNotesEventListeners() {
    // Toggle notes panel
    document.addEventListener('click', (e) => {
        if (e.target.id === 'notes-toggle-btn' || e.target.closest('#notes-toggle-btn')) {
            toggleNotesPanel();
        }
    });
    
    // Filter notes
    document.getElementById('notes-filter').addEventListener('change', (e) => {
        notesManager.currentFilter = e.target.value;
        loadRoomNotes();
    });
    
    // Add note button
    document.getElementById('add-note-btn').addEventListener('click', () => {
        openNoteModal();
    });
    
    // Note type change
    document.getElementById('note-type').addEventListener('change', (e) => {
        const taskFields = document.getElementById('task-fields');
        taskFields.style.display = e.target.value === 'task' ? 'block' : 'none';
    });
    
    // Note form submission
    document.getElementById('note-form').addEventListener('submit', handleNoteSubmission);
    
    // Modal controls
    document.getElementById('close-note-modal').addEventListener('click', closeNoteModal);
    document.getElementById('cancel-note').addEventListener('click', closeNoteModal);
}

async function loadRoomNotes() {
    try {
        const params = new URLSearchParams({
            type: notesManager.currentFilter,
            shared_only: 'true'
        });
        
        const response = await fetch(`/api/rooms/${ROOM_UUID}/notes?${params}`, {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            displayNotes(data.notes);
        }
    } catch (error) {
        console.error('Failed to load notes:', error);
    }
}

function displayNotes(notes) {
    const container = document.getElementById('notes-content');
    
    if (notes.length === 0) {
        container.innerHTML = `
            <div class="no-notes">
                <p>No notes found for this filter.</p>
                <button onclick="openNoteModal()" class="btn-secondary">Add the first note</button>
            </div>
        `;
        return;
    }
    
    const notesHtml = notes.map(note => `
        <div class="note-item ${note.type}" data-note-id="${note.id}">
            <div class="note-header">
                <div class="note-meta">
                    <span class="note-type ${note.type}">${note.type}</span>
                    ${note.is_pinned ? '<span class="pinned">📌</span>' : ''}
                    ${note.is_task && note.is_completed ? '<span class="completed">✅</span>' : ''}
                    ${note.is_task && note.is_overdue ? '<span class="overdue">⚠️</span>' : ''}
                </div>
                <div class="note-actions">
                    ${note.is_task && !note.is_completed ? '<button onclick="markTaskCompleted(' + note.id + ')">Mark Complete</button>' : ''}
                    <button onclick="editNote(' + note.id + ')">Edit</button>
                </div>
            </div>
            <div class="note-content">
                ${note.title ? '<h4>' + note.title + '</h4>' : ''}
                <div class="note-text">${note.formatted_content}</div>
                ${note.due_date ? '<div class="due-date">Due: ' + new Date(note.due_date).toLocaleString() + '</div>' : ''}
            </div>
            <div class="note-footer">
                <span class="note-author">${note.author.name}</span>
                <span class="note-time">${new Date(note.created_at).toLocaleString()}</span>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = notesHtml;
}

function openNoteModal(noteId = null) {
    const modal = document.getElementById('note-modal');
    const title = document.getElementById('note-modal-title');
    
    if (noteId) {
        title.textContent = 'Edit Note';
        // Load note data for editing
        loadNoteForEditing(noteId);
    } else {
        title.textContent = 'Add Note';
        document.getElementById('note-form').reset();
    }
    
    modal.classList.remove('hidden');
}

function closeNoteModal() {
    document.getElementById('note-modal').classList.add('hidden');
    document.getElementById('note-form').reset();
}

async function handleNoteSubmission(e) {
    e.preventDefault();
    
    const formData = {
        type: document.getElementById('note-type').value,
        title: document.getElementById('note-title').value,
        content: document.getElementById('note-content').value,
        is_shared: document.getElementById('note-shared').checked,
        metadata: {}
    };
    
    if (formData.type === 'task') {
        formData.due_date = document.getElementById('note-due-date').value;
        formData.metadata.priority = document.getElementById('note-priority').value;
        formData.metadata.status = 'pending';
    }
    
    try {
        const response = await fetch(`/api/rooms/${ROOM_UUID}/notes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(formData)
        });
        
        if (response.ok) {
            closeNoteModal();
            loadRoomNotes();
            showNotification('Note added successfully', 'success');
        } else {
            throw new Error('Failed to save note');
        }
    } catch (error) {
        console.error('Error saving note:', error);
        showNotification('Failed to save note', 'error');
    }
}

async function markTaskCompleted(noteId) {
    try {
        const response = await fetch(`/api/rooms/${ROOM_UUID}/notes/${noteId}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (response.ok) {
            loadRoomNotes();
            showNotification('Task marked as completed', 'success');
        }
    } catch (error) {
        console.error('Error marking task completed:', error);
        showNotification('Failed to update task', 'error');
    }
}

function toggleNotesPanel() {
    const panel = document.getElementById('notes-panel');
    panel.classList.toggle('hidden');
    
    // Add notes toggle button to stream actions if not exists
    const streamActions = document.querySelector('.stream__actions');
    if (streamActions && !document.getElementById('notes-toggle-btn')) {
        const notesBtn = `
            <button id="notes-toggle-btn" class="btn mic-btn">
                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M9.5 3A1.5 1.5 0 0 0 8 1.5v3A1.5 1.5 0 0 0 9.5 6h1A1.5 1.5 0 0 0 12 4.5v-3A1.5 1.5 0 0 0 10.5 0h-1zM6 6V1.5a.5.5 0 0 1 1 0V6a.5.5 0 0 1-1 0zM4.5 6V1.5a.5.5 0 0 1 1 0V6a.5.5 0 0 1-1 0zM2 8v6h12V8H2z"/>
                </svg>
                <span>Notes</span>
            </button>
        `;
        streamActions.insertAdjacentHTML('beforeend', notesBtn);
    }
}

/**
 * Search Interface
 */
function initializeSearchInterface() {
    addSearchBox();
}

function addSearchBox() {
    const searchBox = `
        <div id="search-box" class="search-box">
            <input type="text" id="global-search" placeholder="Search rooms, notes, recordings..." />
            <div id="search-results" class="search-results hidden"></div>
        </div>
    `;
    
    const nav = document.getElementById('nav');
    if (nav) {
        nav.insertAdjacentHTML('beforeend', searchBox);
        setupSearchListeners();
    }
}

function setupSearchListeners() {
    const searchInput = document.getElementById('global-search');
    const searchResults = document.getElementById('search-results');
    let searchTimeout;
    
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        
        searchTimeout = setTimeout(() => performQuickSearch(query), 300);
    });
    
    searchInput.addEventListener('focus', () => {
        if (searchInput.value.trim().length >= 2) {
            searchResults.classList.remove('hidden');
        }
    });
    
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#search-box')) {
            searchResults.classList.add('hidden');
        }
    });
}

async function performQuickSearch(query) {
    try {
        const response = await fetch(`/api/search?q=${encodeURIComponent(query)}&limit=5`, {
            headers: {
                'Authorization': 'Bearer ' + getAuthToken(),
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            displayQuickSearchResults(data.results.combined || []);
        }
    } catch (error) {
        console.error('Search error:', error);
    }
}

function displayQuickSearchResults(results) {
    const container = document.getElementById('search-results');
    
    if (results.length === 0) {
        container.innerHTML = '<div class="no-results">No results found</div>';
        container.classList.remove('hidden');
        return;
    }
    
    const resultsHtml = results.map(result => `
        <div class="search-result-item" onclick="window.location.href='${result.url}'">
            <div class="result-type">${result.type}</div>
            <div class="result-title">${result.title}</div>
            <div class="result-description">${result.description || ''}</div>
        </div>
    `).join('');
    
    container.innerHTML = resultsHtml + `
        <div class="search-result-item view-all" onclick="window.location.href='/analytics/search?q=${encodeURIComponent(document.getElementById('global-search').value)}'">
            View all results →
        </div>
    `;
    container.classList.remove('hidden');
}

/**
 * Quality Monitoring
 */
function initializeQualityMonitoring() {
    addQualityIndicator();
    
    // Monitor connection stats if available
    if (typeof client !== 'undefined' && client) {
        setInterval(updateConnectionStats, 10000);
    }
}

function addQualityIndicator() {
    const qualityIndicator = `
        <div id="quality-indicator" class="quality-indicator">
            <div class="quality-indicator good">
                <span class="quality-score">--</span>
                <span class="quality-label">Quality</span>
            </div>
        </div>
    `;
    
    const nav = document.getElementById('nav');
    if (nav) {
        nav.insertAdjacentHTML('beforeend', qualityIndicator);
    }
}

function updateConnectionStats() {
    // This would get real stats from Agora SDK in production
    // For now, simulate quality monitoring
    const simulatedLatency = Math.random() * 200 + 50;
    const simulatedPacketLoss = Math.random() * 5;
    
    updateQualityIndicator(simulatedLatency, simulatedPacketLoss);
}

/**
 * Utility Functions
 */
function getAuthToken() {
    // In production, this would get the actual auth token
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}

function showNotification(message, type = 'info') {
    // Create a notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function updateParticipantAnalytics() {
    // Update any real-time analytics displays
    const participantCount = document.getElementById('participant-count');
    if (participantCount) {
        participantCount.textContent = analyticsData.participantCount;
    }
}

// Initialize enhanced features when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof ROOM_UUID !== 'undefined') {
        initializeEnhancedFeatures();
    }
});

// Export functions for global use
window.ekiliConvoEnhanced = {
    initializeEnhancedFeatures,
    toggleNotesPanel,
    performQuickSearch,
    recordSessionAnalytics,
    markTaskCompleted
};