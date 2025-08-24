@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-600 to-purple-700 flex items-center justify-center p-5">
    <div class="bg-white rounded-3xl p-10 text-center shadow-2xl max-w-lg w-full">
        <div class="text-6xl mb-5 animate-pulse">⏳</div>
        
        <h1 class="text-3xl text-gray-800 mb-4 font-semibold">You're in the Waiting Room</h1>
        
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-full text-yellow-800 font-medium mb-5">
            <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
            Waiting for host approval
        </div>
        
        <p class="text-gray-600 text-lg mb-8 leading-relaxed">
            The host will admit you to the meeting shortly. Please wait while we notify them of your arrival.
        </p>
        
        <div class="bg-gray-50 rounded-xl p-5 mb-8">
            <div class="text-xl font-semibold text-blue-600 mb-2">{{ $room }}</div>
            <div class="text-gray-500 text-sm" id="participant-count">
                Checking participant count...
            </div>
        </div>
        
        <div class="flex gap-4 justify-center flex-wrap">
            <a href="{{ route('home') }}" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
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