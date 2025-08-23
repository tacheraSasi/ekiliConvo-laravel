let uid = sessionStorage.getItem('uid')
if(!uid){
    uid = String(Math.floor(Math.random() * 10000))
    sessionStorage.setItem('uid', uid)
}

let token = null;
let client;

let rtmClient;
let channel;

// Use room UUID from the page instead of URL params
let roomId = ROOM_UUID || 'main'

let displayName = sessionStorage.getItem('display_name')
if(!displayName){
    window.location = '/lobby'
}

let localTracks = []
let remoteUsers = {}

let localScreenTracks;
let sharingScreen = false;

// File sharing variables
let fileInput;
let maxFileSize = 50 * 1024 * 1024; // 50MB limit

let joinRoomInit = async () => {
    rtmClient = await AgoraRTM.createInstance(APP_ID)
    await rtmClient.login({uid,token})

    await rtmClient.addOrUpdateLocalUserAttributes({'name':displayName})

    channel = await rtmClient.createChannel(roomId)
    await channel.join()

    channel.on('MemberJoined', handleMemberJoined)
    channel.on('MemberLeft', handleMemberLeft)
    channel.on('ChannelMessage', handleChannelMessage)

    // Initialize file sharing
    initializeFileSharing()

    getMembers()
    addBotMessageToDom(`Welcome to the convo ${displayName}! 👋`)

    // Enhanced client configuration with TURN/STUN servers
    client = AgoraRTC.createClient({
        mode:'rtc', 
        codec:'vp8',
        // Configure TURN/STUN servers for better connectivity
        turnServer: {
            turnServerURL: "stun:stun.agora.io:3478",
            username: "",
            password: ""
        }
    })

    // Enable connection quality monitoring
    client.enableAudioVolumeIndicator();
    
    await client.join(APP_ID, roomId, token, uid)

    client.on('user-published', handleUserPublished)
    client.on('user-left', handleUserLeft)
    client.on('network-quality', handleNetworkQuality)
    client.on('connection-state-changed', handleConnectionStateChanged)
    client.on('volume-indicator', handleVolumeIndicator)

    // Start connection monitoring
    startConnectionMonitoring()
}

/**
 * Handle network quality changes
 */
function handleNetworkQuality(stats) {
    // Update connection quality indicators
    updateConnectionQuality(uid, stats.uplinkNetworkQuality)
    
    // Show warning if quality is poor
    if (stats.uplinkNetworkQuality >= 4) {
        showToast('Poor network connection detected', 'warning')
    }
}

/**
 * Handle connection state changes
 */
function handleConnectionStateChanged(curState, revState, reason) {
    console.log('Connection state changed:', curState, revState, reason)
    
    if (curState === 'DISCONNECTED') {
        showToast('Connection lost. Attempting to reconnect...', 'error')
        attemptReconnection()
    } else if (curState === 'CONNECTED') {
        showToast('Successfully connected', 'success')
    }
}

/**
 * Handle volume indicator for active speaker detection
 */
function handleVolumeIndicator(volumes) {
    volumes.forEach((volume, index) => {
        const userId = volume.uid
        const level = volume.level
        
        // Highlight active speaker
        if (level > 10) { // Threshold for speaking
            highlightActiveSpeaker(userId)
        } else {
            removeActiveSpeakerHighlight(userId)
        }
    })
}

/**
 * Update connection quality indicator
 */
function updateConnectionQuality(userId, quality) {
    const container = document.getElementById(`user-container-${userId}`)
    if (!container) return
    
    // Remove existing quality indicator
    const existingIndicator = container.querySelector('.connection-quality')
    if (existingIndicator) {
        existingIndicator.remove()
    }
    
    // Add new quality indicator
    const indicator = document.createElement('div')
    indicator.className = 'connection-quality'
    
    if (quality <= 2) {
        indicator.classList.add('excellent')
    } else if (quality <= 3) {
        indicator.classList.add('good')
    } else {
        indicator.classList.add('poor')
    }
    
    container.appendChild(indicator)
}

/**
 * Highlight active speaker
 */
function highlightActiveSpeaker(userId) {
    const container = document.getElementById(`user-container-${userId}`)
    if (container) {
        container.classList.add('speaking')
    }
}

/**
 * Remove active speaker highlight
 */
function removeActiveSpeakerHighlight(userId) {
    const container = document.getElementById(`user-container-${userId}`)
    if (container) {
        container.classList.remove('speaking')
    }
}

/**
 * Start connection monitoring
 */
function startConnectionMonitoring() {
    setInterval(async () => {
        if (client) {
            const stats = await client.getRTCStats()
            
            // Log connection statistics for debugging
            console.log('RTC Stats:', stats)
            
            // Update UI with connection info if needed
            updateConnectionStats(stats)
        }
    }, 5000) // Check every 5 seconds
}

/**
 * Update connection statistics display
 */
function updateConnectionStats(stats) {
    // This could update a debug panel or connection status indicator
    // For now, we'll just log to console
    if (stats.Duration > 0) {
        const avgBitrate = stats.RecvBitrate || 0
        if (avgBitrate < 100) { // Very low bitrate warning
            console.warn('Low bitrate detected:', avgBitrate, 'kbps')
        }
    }
}

/**
 * Attempt reconnection
 */
async function attemptReconnection() {
    let attempts = 0
    const maxAttempts = 5
    
    while (attempts < maxAttempts) {
        try {
            attempts++
            showToast(`Reconnection attempt ${attempts}/${maxAttempts}`, 'warning')
            
            await client.leave()
            await client.join(APP_ID, roomId, token, uid)
            
            // Republish tracks if they exist
            if (localTracks && localTracks.length > 0) {
                await client.publish(localTracks)
            }
            
            showToast('Reconnected successfully', 'success')
            break
            
        } catch (error) {
            console.error(`Reconnection attempt ${attempts} failed:`, error)
            
            if (attempts === maxAttempts) {
                showToast('Failed to reconnect. Please refresh the page.', 'error')
            } else {
                // Wait before next attempt
                await new Promise(resolve => setTimeout(resolve, 2000 * attempts))
            }
        }
    }
}

/**
 * Initialize file sharing functionality
 */
function initializeFileSharing() {
    // Create hidden file input
    fileInput = document.createElement('input')
    fileInput.type = 'file'
    fileInput.style.display = 'none'
    fileInput.accept = 'image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.zip,.rar'
    fileInput.addEventListener('change', handleFileSelection)
    document.body.appendChild(fileInput)

    // Add file sharing button to message form
    addFileShareButton()
}

/**
 * Add file sharing button to the chat interface
 */
function addFileShareButton() {
    const messageForm = document.getElementById('message__form')
    if (messageForm) {
        const fileButton = document.createElement('button')
        fileButton.type = 'button'
        fileButton.id = 'file-share-btn'
        fileButton.innerHTML = `
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
            </svg>
        `
        fileButton.title = 'Share File'
        fileButton.addEventListener('click', () => fileInput.click())
        
        // Style the button
        fileButton.style.cssText = `
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        `
        
        messageForm.style.position = 'relative'
        messageForm.appendChild(fileButton)
    }
}

/**
 * Handle file selection for sharing
 */
function handleFileSelection(event) {
    const file = event.target.files[0]
    if (!file) return

    // Check file size
    if (file.size > maxFileSize) {
        alert(`File size too large. Maximum size is ${maxFileSize / (1024 * 1024)}MB`)
        return
    }

    // Convert file to base64 and send via RTM
    const reader = new FileReader()
    reader.onload = function(e) {
        const base64Data = e.target.result.split(',')[1] // Remove data:type;base64, prefix
        
        // Split large files into chunks
        sendFileInChunks(file.name, file.type, file.size, base64Data)
    }
    reader.readAsDataURL(file)
    
    // Clear the input
    event.target.value = ''
}

/**
 * Send file in chunks via RTM
 */
async function sendFileInChunks(fileName, fileType, fileSize, base64Data) {
    const chunkSize = 30000 // RTM message size limit consideration
    const totalChunks = Math.ceil(base64Data.length / chunkSize)
    const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9)
    
    // Send file header
    await channel.sendMessage({
        text: JSON.stringify({
            'type': 'file_share_start',
            'file_id': fileId,
            'file_name': fileName,
            'file_type': fileType,
            'file_size': fileSize,
            'total_chunks': totalChunks,
            'sender_name': displayName,
            'sender_uid': uid
        })
    })

    // Send file chunks
    for (let i = 0; i < totalChunks; i++) {
        const start = i * chunkSize
        const end = Math.min(start + chunkSize, base64Data.length)
        const chunk = base64Data.slice(start, end)
        
        await channel.sendMessage({
            text: JSON.stringify({
                'type': 'file_chunk',
                'file_id': fileId,
                'chunk_index': i,
                'chunk_data': chunk
            })
        })
        
        // Small delay to prevent rate limiting
        await new Promise(resolve => setTimeout(resolve, 50))
    }

    // Send file complete signal
    await channel.sendMessage({
        text: JSON.stringify({
            'type': 'file_share_complete',
            'file_id': fileId,
            'sender_name': displayName
        })
    })

    // Add to local chat
    addFileMessageToDom(displayName, fileName, fileType, fileSize, base64Data, true)
}

let joinStream = async () => {
    document.getElementById('join-btn').style.display = 'none'
    document.getElementsByClassName('stream__actions')[0].style.display = 'flex'

    localTracks = await AgoraRTC.createMicrophoneAndCameraTracks({}, {encoderConfig:{
        width:{min:640, ideal:1920, max:1920},
        height:{min:480, ideal:1080, max:1080}
    }})


    let player = `<div class="video__container" id="user-container-${uid}">
                    <div class="video-player" id="user-${uid}"></div>
                 </div>`

    document.getElementById('streams__container').insertAdjacentHTML('beforeend', player)
    document.getElementById(`user-container-${uid}`).addEventListener('click', expandVideoFrame)

    localTracks[1].play(`user-${uid}`)
    await client.publish([localTracks[0], localTracks[1]])
}

let switchToCamera = async () => {
    let player = `<div class="video__container" id="user-container-${uid}">
                    <div class="video-player" id="user-${uid}"></div>
                 </div>`
    displayFrame.insertAdjacentHTML('beforeend', player)

    await localTracks[0].setMuted(true)
    await localTracks[1].setMuted(true)

    document.getElementById('mic-btn').classList.remove('active')
    document.getElementById('screen-btn').classList.remove('active')

    localTracks[1].play(`user-${uid}`)
    await client.publish([localTracks[1]])
}

let handleUserPublished = async (user, mediaType) => {
    remoteUsers[user.uid] = user

    await client.subscribe(user, mediaType)

    let player = document.getElementById(`user-container-${user.uid}`)
    if(player === null){
        player = `<div class="video__container" id="user-container-${user.uid}">
                <div class="video-player" id="user-${user.uid}"></div>
            </div>`

        document.getElementById('streams__container').insertAdjacentHTML('beforeend', player)
        document.getElementById(`user-container-${user.uid}`).addEventListener('click', expandVideoFrame)
   
    }

    if(displayFrame.style.display){
        let videoFrame = document.getElementById(`user-container-${user.uid}`)
        videoFrame.style.height = '100px'
        videoFrame.style.width = '100px'
    }

    if(mediaType === 'video'){
        user.videoTrack.play(`user-${user.uid}`)
    }

    if(mediaType === 'audio'){
        user.audioTrack.play()
    }

}

let handleUserLeft = async (user) => {
    delete remoteUsers[user.uid]
    let item = document.getElementById(`user-container-${user.uid}`)
    if(item){
        item.remove()
    }

    if(userIdInDisplayFrame === `user-container-${user.uid}`){
        displayFrame.style.display = null
        
        let videoFrames = document.getElementsByClassName('video__container')

        for(let i = 0; videoFrames.length > i; i++){
            videoFrames[i].style.height = '300px'
            videoFrames[i].style.width = '300px'
        }
    }
}

let toggleMic = async (e) => {
    let button = e.currentTarget

    if(localTracks[0].muted){
        await localTracks[0].setMuted(false)
        button.classList.add('active')
    }else{
        await localTracks[0].setMuted(true)
        button.classList.remove('active')
    }
}

let toggleCamera = async (e) => {
    let button = e.currentTarget

    if(localTracks[1].muted){
        await localTracks[1].setMuted(false)
        button.classList.add('active')
    }else{
        await localTracks[1].setMuted(true)
        button.classList.remove('active')
    }
}

let toggleScreen = async (e) => {
    let screenButton = e.currentTarget
    let cameraButton = document.getElementById('camera-btn')

    if(!sharingScreen){
        sharingScreen = true

        screenButton.classList.add('active')
        cameraButton.classList.remove('active')
        cameraButton.style.display = 'none'

        try {
            localScreenTracks = await AgoraRTC.createScreenVideoTrack({
                // Enhanced screen sharing options
                encoderConfig: "1080p_1",
                optimizationMode: "detail"
            })

            document.getElementById(`user-container-${uid}`).remove()
            displayFrame.style.display = 'block'

            let player = `<div class="video__container screen-share" id="user-container-${uid}">
                    <div class="video-player" id="user-${uid}"></div>
                    <div class="screen-share-indicator">
                        <span>🖥️ Screen Sharing</span>
                    </div>
                </div>`

            displayFrame.insertAdjacentHTML('beforeend', player)
            document.getElementById(`user-container-${uid}`).addEventListener('click', expandVideoFrame)

            userIdInDisplayFrame = `user-container-${uid}`
            localScreenTracks.play(`user-${uid}`)

            await client.unpublish([localTracks[1]])
            await client.publish([localScreenTracks])

            // Notify other participants via RTM
            if (channel) {
                channel.sendMessage({
                    text: JSON.stringify({
                        'type': 'screen_share_started',
                        'uid': uid,
                        'displayName': displayName
                    })
                })
            }

            // Log screen share start
            logAuditAction('screen_share_started')

            let videoFrames = document.getElementsByClassName('video__container')
            for(let i = 0; videoFrames.length > i; i++){
                if(videoFrames[i].id != userIdInDisplayFrame){
                  videoFrames[i].style.height = '100px'
                  videoFrames[i].style.width = '100px'
                }
              }

        } catch (error) {
            console.error('Failed to start screen sharing:', error)
            sharingScreen = false
            screenButton.classList.remove('active')
            cameraButton.style.display = 'block'
            
            // Show error message
            showToast('Failed to start screen sharing. Please check permissions.', 'error')
        }

    } else {
        sharingScreen = false 
        cameraButton.style.display = 'block'
        document.getElementById(`user-container-${uid}`).remove()
        await client.unpublish([localScreenTracks])

        // Notify other participants via RTM
        if (channel) {
            channel.sendMessage({
                text: JSON.stringify({
                    'type': 'screen_share_stopped',
                    'uid': uid,
                    'displayName': displayName
                })
            })
        }

        // Log screen share stop
        logAuditAction('screen_share_stopped')

        switchToCamera()
    }
}

/**
 * Log audit actions
 */
async function logAuditAction(action) {
    try {
        // This would typically call the backend audit log API
        console.log(`Audit Log: ${action} by ${displayName}`)
    } catch (error) {
        console.error('Failed to log audit action:', error)
    }
}

let leaveStream = async (e) => {
    e.preventDefault()

    document.getElementById('join-btn').style.display = 'block'
    document.getElementsByClassName('stream__actions')[0].style.display = 'none'

    for(let i = 0; localTracks.length > i; i++){
        localTracks[i].stop()
        localTracks[i].close()
    }

    await client.unpublish([localTracks[0], localTracks[1]])

    if(localScreenTracks){
        await client.unpublish([localScreenTracks])
    }

    document.getElementById(`user-container-${uid}`).remove()

    if(userIdInDisplayFrame === `user-container-${uid}`){
        displayFrame.style.display = null

        for(let i = 0; videoFrames.length > i; i++){
            videoFrames[i].style.height = '300px'
            videoFrames[i].style.width = '300px'
        }
    }

    channel.sendMessage({text:JSON.stringify({'type':'user_left', 'uid':uid})})
}



document.getElementById('camera-btn').addEventListener('click', toggleCamera)
document.getElementById('mic-btn').addEventListener('click', toggleMic)
document.getElementById('screen-btn').addEventListener('click', toggleScreen)
document.getElementById('join-btn').addEventListener('click', joinStream)
document.getElementById('leave-btn').addEventListener('click', leaveStream)


joinRoomInit()