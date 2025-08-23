// Enterprise features for ekiliConvo rooms
let isHost = false;
let roomLocked = false;
let recordingInProgress = false;
let handRaised = false;
let meetingStartTime = null;
let meetingTimer = null;

// Initialize enterprise features when room is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeEnterpriseFeatures();
    
    // Check if current user is host and load host controls
    loadRoomParticipants();
    
    // Start meeting timer
    startMeetingTimer();
});

/**
 * Start meeting timer
 */
function startMeetingTimer() {
    meetingStartTime = new Date();
    
    // Add timer to UI
    const timerElement = document.createElement('div');
    timerElement.id = 'meeting-timer';
    timerElement.className = 'meeting-timer';
    timerElement.textContent = '00:00:00';
    
    // Insert after nav
    const nav = document.getElementById('nav');
    if (nav) {
        nav.insertAdjacentElement('afterend', timerElement);
    }
    
    // Update timer every second
    meetingTimer = setInterval(updateMeetingTimer, 1000);
}

/**
 * Update meeting timer display
 */
function updateMeetingTimer() {
    if (!meetingStartTime) return;
    
    const now = new Date();
    const elapsed = Math.floor((now - meetingStartTime) / 1000);
    
    const hours = Math.floor(elapsed / 3600);
    const minutes = Math.floor((elapsed % 3600) / 60);
    const seconds = elapsed % 60;
    
    const timeString = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    const timerElement = document.getElementById('meeting-timer');
    if (timerElement) {
        timerElement.textContent = timeString;
    }
}

/**
 * Initialize enterprise features UI and event listeners
 */
function initializeEnterpriseFeatures() {
    // Add host control panel to the UI
    addHostControlPanel();
    
    // Add raise hand button
    addRaiseHandButton();
    
    // Add recording controls
    addRecordingControls();
    
    // Add reaction buttons
    addReactionButtons();
    
    // Set up periodic participant updates
    setInterval(loadRoomParticipants, 10000); // Update every 10 seconds
}

/**
 * Add host control panel to the room interface
 */
function addHostControlPanel() {
    const controlPanel = `
        <div id="host-controls" class="host-controls" style="display: none;">
            <div class="control-section">
                <h4>Host Controls</h4>
                <div class="control-buttons">
                    <button id="toggle-lock-btn" class="control-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6z"/>
                        </svg>
                        <span id="lock-status">Unlock Room</span>
                    </button>
                    
                    <button id="toggle-recording-btn" class="control-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
                        </svg>
                        <span>Start Recording</span>
                    </button>
                    
                    <button id="room-settings-btn" class="control-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/>
                        </svg>
                        <span>Settings</span>
                    </button>
                </div>
            </div>
            
            <div class="participants-controls">
                <h5>Participants <span id="participant-count">0</span></h5>
                <div id="participants-list" class="participants-list"></div>
            </div>
        </div>
    `;
    
    // Insert after the existing member container
    const memberContainer = document.getElementById('members__container');
    if (memberContainer) {
        memberContainer.insertAdjacentHTML('afterend', controlPanel);
    }
    
    // Add event listeners for host controls
    setupHostControlListeners();
}

/**
 * Add raise hand button to the stream actions
 */
function addRaiseHandButton() {
    const raiseHandBtn = `
        <button id="raise-hand-btn" class="action-btn" title="Raise Hand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23 5.5V18.5c0 2.5-2 4.5-4.5 4.5h-10C6 23 4 21 4 18.5v-7C4 9 6 7 8.5 7H9V5.5C9 3 11 1 13.5 1S18 3 18 5.5V7h0.5C21 7 23 9 23 11.5V5.5z"/>
            </svg>
            <span id="hand-status">Raise Hand</span>
        </button>
    `;
    
    const streamActions = document.querySelector('.stream__actions');
    if (streamActions) {
        streamActions.insertAdjacentHTML('beforeend', raiseHandBtn);
        
        document.getElementById('raise-hand-btn').addEventListener('click', toggleRaiseHand);
    }
}

/**
 * Add recording controls
 */
function addRecordingControls() {
    const recordingIndicator = `
        <div id="recording-indicator" style="display: none;" class="recording-indicator">
            <div class="recording-dot"></div>
            <span>Recording in progress</span>
        </div>
    `;
    
    const nav = document.getElementById('nav');
    if (nav) {
        nav.insertAdjacentHTML('beforeend', recordingIndicator);
    }
}

/**
 * Add reaction buttons
 */
function addReactionButtons() {
    const reactionsPanel = `
        <div id="reactions-panel" class="reactions-panel">
            <button class="reaction-btn" data-reaction="👍" title="Thumbs up">👍</button>
            <button class="reaction-btn" data-reaction="👏" title="Clap">👏</button>
            <button class="reaction-btn" data-reaction="❤️" title="Heart">❤️</button>
            <button class="reaction-btn" data-reaction="😂" title="Laugh">😂</button>
            <button class="reaction-btn" data-reaction="🎉" title="Party">🎉</button>
        </div>
    `;
    
    const streamContainer = document.getElementById('stream__container');
    if (streamContainer) {
        streamContainer.insertAdjacentHTML('beforeend', reactionsPanel);
        
        // Add event listeners for reactions
        document.querySelectorAll('.reaction-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                sendReaction(e.target.dataset.reaction);
            });
        });
    }
}

/**
 * Set up event listeners for host controls
 */
function setupHostControlListeners() {
    // Room lock/unlock
    document.getElementById('toggle-lock-btn')?.addEventListener('click', toggleRoomLock);
    
    // Recording controls
    document.getElementById('toggle-recording-btn')?.addEventListener('click', toggleRecording);
    
    // Room settings
    document.getElementById('room-settings-btn')?.addEventListener('click', showRoomSettings);
}

/**
 * Load and display room participants with their status
 */
async function loadRoomParticipants() {
    try {
        const response = await fetch(`/api/rooms/${ROOM_UUID}/participants`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateParticipantsDisplay(data);
            updateRoomStatus(data);
        }
    } catch (error) {
        console.error('Failed to load participants:', error);
    }
}

/**
 * Update participants display
 */
function updateParticipantsDisplay(data) {
    const participantsList = document.getElementById('participants-list');
    const participantCount = document.getElementById('participant-count');
    
    if (!participantsList || !participantCount) return;
    
    participantCount.textContent = data.total_count;
    
    participantsList.innerHTML = data.participants.map(participant => `
        <div class="participant-item" data-user-id="${participant.id}">
            <div class="participant-info">
                <span class="participant-name">${participant.name}</span>
                <span class="participant-role">${participant.role}</span>
                ${participant.hand_raised ? '<span class="hand-raised">✋</span>' : ''}
                ${participant.is_muted ? '<span class="muted">🔇</span>' : ''}
            </div>
            
            ${isHost && participant.role !== 'host' ? `
                <div class="participant-controls">
                    <button class="control-btn small" onclick="muteParticipant(${participant.id})" 
                            title="${participant.is_muted ? 'Unmute' : 'Mute'}">
                        ${participant.is_muted ? '🔊' : '🔇'}
                    </button>
                    <button class="control-btn small" onclick="removeParticipant(${participant.id})" 
                            title="Remove">
                        ❌
                    </button>
                </div>
            ` : ''}
        </div>
    `).join('');
    
    // Check if current user is host
    const currentUserData = data.participants.find(p => p.name === displayName);
    if (currentUserData && currentUserData.role === 'host') {
        isHost = true;
        document.getElementById('host-controls').style.display = 'block';
    }
    
    // Update hand status for current user
    if (currentUserData) {
        handRaised = currentUserData.hand_raised;
        updateHandButton();
    }
}

/**
 * Update room status indicators
 */
function updateRoomStatus(data) {
    roomLocked = data.is_locked;
    recordingInProgress = data.is_recording;
    
    // Update lock button
    const lockBtn = document.getElementById('toggle-lock-btn');
    const lockStatus = document.getElementById('lock-status');
    if (lockBtn && lockStatus) {
        lockStatus.textContent = roomLocked ? 'Unlock Room' : 'Lock Room';
        lockBtn.classList.toggle('active', roomLocked);
    }
    
    // Update recording indicator
    const recordingIndicator = document.getElementById('recording-indicator');
    const recordingBtn = document.getElementById('toggle-recording-btn');
    
    if (recordingIndicator) {
        recordingIndicator.style.display = recordingInProgress ? 'flex' : 'none';
    }
    
    if (recordingBtn) {
        const span = recordingBtn.querySelector('span');
        if (span) {
            span.textContent = recordingInProgress ? 'Stop Recording' : 'Start Recording';
        }
        recordingBtn.classList.toggle('recording', recordingInProgress);
    }
}

/**
 * Toggle raise hand status
 */
async function toggleRaiseHand() {
    try {
        const response = await fetch(`/api/rooms/${ROOM_UUID}/toggle-hand`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            handRaised = data.hand_raised;
            updateHandButton();
            
            // Send RTM message to update other participants
            if (channel) {
                channel.sendMessage({
                    text: JSON.stringify({
                        'type': 'hand_status_changed',
                        'uid': uid,
                        'displayName': displayName,
                        'hand_raised': handRaised
                    })
                });
            }
        }
    } catch (error) {
        console.error('Failed to toggle hand:', error);
    }
}

/**
 * Update raise hand button appearance
 */
function updateHandButton() {
    const btn = document.getElementById('raise-hand-btn');
    const status = document.getElementById('hand-status');
    
    if (btn && status) {
        btn.classList.toggle('active', handRaised);
        status.textContent = handRaised ? 'Lower Hand' : 'Raise Hand';
    }
}

/**
 * Mute a participant (host only)
 */
async function muteParticipant(userId) {
    if (!isHost) return;
    
    try {
        const response = await fetch(`/api/rooms/${ROOM_UUID}/mute-participant`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        if (response.ok) {
            const data = await response.json();
            
            // Send RTM message to force mute the participant
            if (channel) {
                channel.sendMessage({
                    text: JSON.stringify({
                        'type': 'force_mute',
                        'target_uid': userId,
                        'is_muted': data.is_muted
                    })
                });
            }
            
            // Refresh participants list
            loadRoomParticipants();
        }
    } catch (error) {
        console.error('Failed to mute participant:', error);
    }
}

/**
 * Remove a participant (host only)
 */
async function removeParticipant(userId) {
    if (!isHost) return;
    
    if (!confirm('Are you sure you want to remove this participant?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/rooms/${ROOM_UUID}/remove-participant`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ user_id: userId })
        });
        
        if (response.ok) {
            // Send RTM message to kick the participant
            if (channel) {
                channel.sendMessage({
                    text: JSON.stringify({
                        'type': 'participant_kicked',
                        'target_uid': userId
                    })
                });
            }
            
            // Refresh participants list
            loadRoomParticipants();
        }
    } catch (error) {
        console.error('Failed to remove participant:', error);
    }
}

/**
 * Toggle room lock status (host only)
 */
async function toggleRoomLock() {
    if (!isHost) return;
    
    try {
        const response = await fetch(`/api/rooms/${ROOM_UUID}/toggle-lock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            roomLocked = data.is_locked;
            
            // Update UI
            updateRoomStatus({ is_locked: roomLocked, is_recording: recordingInProgress });
            
            // Notify participants via RTM
            if (channel) {
                channel.sendMessage({
                    text: JSON.stringify({
                        'type': 'room_lock_changed',
                        'is_locked': roomLocked
                    })
                });
            }
        }
    } catch (error) {
        console.error('Failed to toggle room lock:', error);
    }
}

/**
 * Toggle recording (host only)
 */
async function toggleRecording() {
    if (!isHost) return;
    
    try {
        const endpoint = recordingInProgress ? 'stop-recording' : 'start-recording';
        const response = await fetch(`/api/rooms/${ROOM_UUID}/${endpoint}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            recordingInProgress = !recordingInProgress;
            
            // Update UI
            updateRoomStatus({ is_locked: roomLocked, is_recording: recordingInProgress });
            
            // Notify participants via RTM
            if (channel) {
                channel.sendMessage({
                    text: JSON.stringify({
                        'type': 'recording_status_changed',
                        'is_recording': recordingInProgress
                    })
                });
            }
            
            // Show toast message
            showToast(data.message);
        }
    } catch (error) {
        console.error('Failed to toggle recording:', error);
    }
}

/**
 * Send reaction
 */
function sendReaction(emoji) {
    if (channel) {
        channel.sendMessage({
            text: JSON.stringify({
                'type': 'reaction',
                'emoji': emoji,
                'displayName': displayName,
                'uid': uid
            })
        });
    }
    
    // Show local reaction animation
    showReactionAnimation(emoji);
}

/**
 * Show reaction animation
 */
function showReactionAnimation(emoji) {
    const reactionDiv = document.createElement('div');
    reactionDiv.className = 'reaction-animation';
    reactionDiv.textContent = emoji;
    reactionDiv.style.cssText = `
        position: fixed;
        font-size: 2rem;
        z-index: 1000;
        pointer-events: none;
        animation: reactionFloat 2s ease-out forwards;
    `;
    
    // Position randomly in the center area
    const centerX = window.innerWidth / 2;
    const centerY = window.innerHeight / 2;
    reactionDiv.style.left = (centerX + (Math.random() - 0.5) * 200) + 'px';
    reactionDiv.style.top = (centerY + (Math.random() - 0.5) * 200) + 'px';
    
    document.body.appendChild(reactionDiv);
    
    // Remove after animation
    setTimeout(() => {
        reactionDiv.remove();
    }, 2000);
}

/**
 * Show room settings modal
 */
function showRoomSettings() {
    // Implementation for room settings modal
    console.log('Room settings modal - to be implemented');
}

/**
 * Show toast notification
 */
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #333;
        color: white;
        padding: 12px 20px;
        border-radius: 4px;
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    .host-controls {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 16px;
        margin: 16px 0;
        border: 1px solid #ddd;
    }
    
    .control-section h4 {
        margin: 0 0 12px 0;
        font-size: 14px;
        font-weight: 600;
    }
    
    .control-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }
    
    .control-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border: 1px solid #ccc;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s;
    }
    
    .control-btn:hover {
        background: #f0f0f0;
    }
    
    .control-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    
    .control-btn.recording {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }
    
    .control-btn.small {
        padding: 4px 6px;
        font-size: 10px;
    }
    
    .participants-list {
        max-height: 200px;
        overflow-y: auto;
    }
    
    .participant-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid #eee;
    }
    
    .participant-info {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
    }
    
    .participant-name {
        font-weight: 500;
        font-size: 12px;
    }
    
    .participant-role {
        font-size: 10px;
        color: #666;
        background: #e9ecef;
        padding: 2px 6px;
        border-radius: 12px;
    }
    
    .hand-raised, .muted {
        font-size: 10px;
    }
    
    .participant-controls {
        display: flex;
        gap: 4px;
    }
    
    .recording-indicator {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #dc3545;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 8px;
        background: rgba(220, 53, 69, 0.1);
        border-radius: 4px;
    }
    
    .recording-dot {
        width: 8px;
        height: 8px;
        background: #dc3545;
        border-radius: 50%;
        animation: pulse 1s infinite;
    }
    
    .reactions-panel {
        position: absolute;
        bottom: 80px;
        right: 20px;
        display: flex;
        gap: 8px;
        background: rgba(255, 255, 255, 0.9);
        padding: 8px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .reaction-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 50%;
        background: transparent;
        cursor: pointer;
        transition: transform 0.2s;
        font-size: 16px;
    }
    
    .reaction-btn:hover {
        transform: scale(1.2);
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    
    @keyframes reactionFloat {
        0% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        100% {
            opacity: 0;
            transform: translateY(-100px) scale(1.5);
        }
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);