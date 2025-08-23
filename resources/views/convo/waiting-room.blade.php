@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .waiting-room-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 20px;
    }

    .waiting-room-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 100%;
    }

    .waiting-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    .waiting-title {
        font-size: 2rem;
        color: #333;
        margin-bottom: 15px;
        font-weight: 600;
    }

    .waiting-message {
        color: #666;
        font-size: 1.1rem;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .room-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
    }

    .room-name {
        font-size: 1.3rem;
        font-weight: 600;
        color: #667eea;
        margin-bottom: 10px;
    }

    .participant-count {
        color: #888;
        font-size: 0.9rem;
    }

    .waiting-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 20px;
        color: #856404;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: #ffa500;
        border-radius: 50%;
        animation: blink 1.5s infinite;
    }

    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0.3; }
    }
</style>

<div class="waiting-room-container">
    <div class="waiting-room-card">
        <div class="waiting-icon">⏳</div>
        
        <h1 class="waiting-title">You're in the Waiting Room</h1>
        
        <div class="status-indicator">
            <span class="status-dot"></span>
            Waiting for host approval
        </div>
        
        <p class="waiting-message">
            The host will admit you to the meeting shortly. Please wait while we notify them of your arrival.
        </p>
        
        <div class="room-info">
            <div class="room-name">{{ $room }}</div>
            <div class="participant-count" id="participant-count">
                Checking participant count...
            </div>
        </div>
        
        <div class="waiting-actions">
            <a href="{{ route('home') }}" class="btn btn-secondary">
                Leave Waiting Room
            </a>
        </div>
    </div>
</div>

<script>
const roomUuid = '{{ $roomUuid }}';
let checkInterval;

// Check admission status periodically
function checkAdmissionStatus() {
    fetch(`/api/rooms/${roomUuid}/admission-status`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'admitted') {
            // Redirect to the room
            window.location.href = `/room/${roomUuid}`;
        } else if (data.status === 'rejected') {
            // Show rejection message and redirect to lobby
            alert('The host has declined your request to join this meeting.');
            window.location.href = '{{ route("home") }}';
        }
        
        // Update participant count
        if (data.participant_count !== undefined) {
            document.getElementById('participant-count').textContent = 
                `${data.participant_count} participant${data.participant_count !== 1 ? 's' : ''} in meeting`;
        }
    })
    .catch(error => {
        console.error('Error checking admission status:', error);
    });
}

// Start checking when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Check immediately
    checkAdmissionStatus();
    
    // Then check every 3 seconds
    checkInterval = setInterval(checkAdmissionStatus, 3000);
});

// Clean up interval when leaving page
window.addEventListener('beforeunload', function() {
    if (checkInterval) {
        clearInterval(checkInterval);
    }
});
</script>
@endsection