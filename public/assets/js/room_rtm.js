let handleMemberJoined = async (MemberId) => {
    console.log('A new member has joined the room:', MemberId)
    addMemberToDom(MemberId)

    let members = await channel.getMembers()
    updateMemberTotal(members)

    let {name} = await rtmClient.getUserAttributesByKeys(MemberId, ['name'])
    addBotMessageToDom(`Welcome to the room ${name}! 👋`)
}

let addMemberToDom = async (MemberId) => {
    let {name} = await rtmClient.getUserAttributesByKeys(MemberId, ['name'])

    let membersWrapper = document.getElementById('member__list')
    let memberItem = `<div class="member__wrapper" id="member__${MemberId}__wrapper">
                        <span class="green__icon"></span>
                        <p class="member_name">${name}</p>
                    </div>`

    membersWrapper.insertAdjacentHTML('beforeend', memberItem)
}

let updateMemberTotal = async (members) => {
    let total = document.getElementById('members__count')
    total.innerText = members.length
}
 
let handleMemberLeft = async (MemberId) => {
    removeMemberFromDom(MemberId)

    let members = await channel.getMembers()
    updateMemberTotal(members)
}

let removeMemberFromDom = async (MemberId) => {
    let memberWrapper = document.getElementById(`member__${MemberId}__wrapper`)
    let name = memberWrapper.getElementsByClassName('member_name')[0].textContent
    addBotMessageToDom(`${name} has left the room.`)
        
    memberWrapper.remove()
}

let getMembers = async () => {
    let members = await channel.getMembers()
    updateMemberTotal(members)
    for (let i = 0; members.length > i; i++){
        addMemberToDom(members[i])
    }
}

// File sharing variables
let incomingFiles = new Map(); // Store incoming file data

let handleChannelMessage = async (messageData, MemberId) => {
    console.log('A new message was received')
    let data = JSON.parse(messageData.text)

    if(data.type === 'chat'){
        addMessageToDom(data.displayName, data.message)
    }

    if(data.type === 'user_left'){
        document.getElementById(`user-container-${data.uid}`).remove()

        if(userIdInDisplayFrame === `user-container-${uid}`){
            displayFrame.style.display = null
    
            for(let i = 0; videoFrames.length > i; i++){
                videoFrames[i].style.height = '300px'
                videoFrames[i].style.width = '300px'
            }
        }
    }

    // File sharing message handling
    if(data.type === 'file_share_start'){
        if(data.sender_uid !== uid) { // Don't handle own files
            incomingFiles.set(data.file_id, {
                file_name: data.file_name,
                file_type: data.file_type,
                file_size: data.file_size,
                total_chunks: data.total_chunks,
                sender_name: data.sender_name,
                chunks: [],
                received_chunks: 0
            })
            
            addBotMessageToDom(`${data.sender_name} is sharing a file: ${data.file_name}`)
        }
    }

    if(data.type === 'file_chunk'){
        if(data.sender_uid !== uid && incomingFiles.has(data.file_id)) {
            const fileData = incomingFiles.get(data.file_id)
            fileData.chunks[data.chunk_index] = data.chunk_data
            fileData.received_chunks++
        }
    }

    if(data.type === 'file_share_complete'){
        if(data.sender_uid !== uid && incomingFiles.has(data.file_id)) {
            const fileData = incomingFiles.get(data.file_id)
            
            // Reconstruct the file
            const completeFileData = fileData.chunks.join('')
            
            // Add to chat
            addFileMessageToDom(
                fileData.sender_name, 
                fileData.file_name, 
                fileData.file_type, 
                fileData.file_size, 
                completeFileData, 
                false
            )
            
            // Clean up
            incomingFiles.delete(data.file_id)
        }
    }

    // Enterprise features message handling
    if(data.type === 'hand_status_changed'){
        updateParticipantHandStatus(data.uid, data.hand_raised)
        addBotMessageToDom(`${data.displayName} ${data.hand_raised ? 'raised their hand' : 'lowered their hand'}`)
    }

    if(data.type === 'force_mute'){
        // If this is directed at current user, mute their microphone
        if(data.target_uid == uid && localTracks && localTracks[0]){
            await localTracks[0].setMuted(true)
            let micButton = document.getElementById('mic-btn')
            if(micButton) micButton.classList.remove('active')
            addBotMessageToDom('You have been muted by the host')
        }
    }

    if(data.type === 'participant_kicked'){
        // If this is directed at current user, leave the room
        if(data.target_uid == uid){
            addBotMessageToDom('You have been removed from the room by the host')
            setTimeout(() => {
                window.location.href = '/lobby'
            }, 2000)
        }
    }

    if(data.type === 'room_lock_changed'){
        updateRoomLockStatus(data.is_locked)
        addBotMessageToDom(`Room has been ${data.is_locked ? 'locked' : 'unlocked'}`)
    }

    if(data.type === 'recording_status_changed'){
        updateRecordingStatus(data.is_recording)
        addBotMessageToDom(`Recording has been ${data.is_recording ? 'started' : 'stopped'}`)
    }

    if(data.type === 'reaction'){
        showReactionMessage(data.displayName, data.emoji)
        if(data.uid != uid) { // Don't show own reactions
            showReactionAnimation(data.emoji)
        }
    }

    if(data.type === 'screen_share_started'){
        if(data.uid != uid) {
            addBotMessageToDom(`${data.displayName} started screen sharing`)
        }
    }

    if(data.type === 'screen_share_stopped'){
        if(data.uid != uid) {
            addBotMessageToDom(`${data.displayName} stopped screen sharing`)
        }
    }
}

// Helper functions for enterprise features
function updateParticipantHandStatus(participantUid, handRaised) {
    // Update the participant's hand status in the UI
    loadRoomParticipants() // Refresh the participants list
}

function updateRoomLockStatus(isLocked) {
    roomLocked = isLocked
    // Update any lock indicators in the UI
    const lockStatus = document.getElementById('lock-status')
    if(lockStatus) {
        lockStatus.textContent = isLocked ? 'Unlock Room' : 'Lock Room'
    }
}

function updateRecordingStatus(isRecording) {
    recordingInProgress = isRecording
    const recordingIndicator = document.getElementById('recording-indicator')
    if(recordingIndicator) {
        recordingIndicator.style.display = isRecording ? 'flex' : 'none'
    }
}

function showReactionMessage(displayName, emoji) {
    addBotMessageToDom(`${displayName} reacted with ${emoji}`)
}

let sendMessage = async (e) => {
    e.preventDefault()

    let message = e.target.message.value
    channel.sendMessage({text:JSON.stringify({'type':'chat', 'message':message, 'displayName':displayName})})
    addMessageToDom(displayName, message)
    e.target.reset()
}

let addMessageToDom = (name, message) => {
    let messagesWrapper = document.getElementById('messages')

    let newMessage = `<div class="message__wrapper">
                        <div class="message__body">
                            <strong class="message__author">${name}</strong>
                            <p class="message__text">${message}</p>
                        </div>
                    </div>`

    messagesWrapper.insertAdjacentHTML('beforeend', newMessage)

    let lastMessage = document.querySelector('#messages .message__wrapper:last-child')
    if(lastMessage){
        lastMessage.scrollIntoView()
    }
}

/**
 * Add file message to chat
 */
let addFileMessageToDom = (senderName, fileName, fileType, fileSize, base64Data, isOwn) => {
    let messagesWrapper = document.getElementById('messages')
    
    // Format file size
    const formatFileSize = (bytes) => {
        if (bytes === 0) return '0 Bytes'
        const k = 1024
        const sizes = ['Bytes', 'KB', 'MB', 'GB']
        const i = Math.floor(Math.log(bytes) / Math.log(k))
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }
    
    // Create download URL
    const mimeType = fileType || 'application/octet-stream'
    const blob = new Blob([Uint8Array.from(atob(base64Data), c => c.charCodeAt(0))], { type: mimeType })
    const downloadUrl = URL.createObjectURL(blob)
    
    // Determine file icon based on type
    let fileIcon = '📄'
    if (fileType.startsWith('image/')) fileIcon = '🖼️'
    else if (fileType.startsWith('video/')) fileIcon = '🎥'
    else if (fileType.startsWith('audio/')) fileIcon = '🎵'
    else if (fileType.includes('pdf')) fileIcon = '📕'
    else if (fileType.includes('word') || fileType.includes('document')) fileIcon = '📝'
    else if (fileType.includes('zip') || fileType.includes('rar')) fileIcon = '🗜️'
    
    let fileMessage = `
        <div class="message__wrapper file-message">
            <div class="message__body">
                <strong class="message__author">${senderName}</strong>
                <div class="file-share-content">
                    <div class="file-info">
                        <span class="file-icon">${fileIcon}</span>
                        <div class="file-details">
                            <span class="file-name">${fileName}</span>
                            <span class="file-size">${formatFileSize(fileSize)}</span>
                        </div>
                    </div>
                    <div class="file-actions">
                        <a href="${downloadUrl}" download="${fileName}" class="download-btn">
                            📥 Download
                        </a>
                        ${fileType.startsWith('image/') ? `
                            <button onclick="previewFile('${downloadUrl}', '${fileType}', '${fileName}')" class="preview-btn">
                                👁️ Preview
                            </button>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `
    
    messagesWrapper.insertAdjacentHTML('beforeend', fileMessage)
    messagesWrapper.scrollTop = messagesWrapper.scrollHeight
}

/**
 * Preview file (for images and videos)
 */
function previewFile(url, type, name) {
    const modal = document.createElement('div')
    modal.className = 'file-preview-modal'
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        cursor: pointer;
    `
    
    let content = ''
    if (type.startsWith('image/')) {
        content = `<img src="${url}" style="max-width: 90%; max-height: 90%; object-fit: contain;" alt="${name}">`
    } else if (type.startsWith('video/')) {
        content = `<video src="${url}" controls style="max-width: 90%; max-height: 90%;" autoplay></video>`
    }
    
    modal.innerHTML = `
        <div style="position: relative;">
            ${content}
            <button onclick="this.closest('.file-preview-modal').remove()" 
                    style="position: absolute; top: -40px; right: 0; background: white; border: none; 
                           border-radius: 50%; width: 30px; height: 30px; cursor: pointer; font-size: 16px;">
                ✕
            </button>
        </div>
    `
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove()
        }
    })
    
    document.body.appendChild(modal)
}


let addBotMessageToDom = (botMessage) => {
    let messagesWrapper = document.getElementById('messages')

    let newMessage = `<div class="message__wrapper">
                        <div class="message__body__bot">
                            <strong class="message__author__bot">🤖 ekilie Bot</strong>
                            <p class="message__text__bot">${botMessage}</p>
                        </div>
                    </div>`

    messagesWrapper.insertAdjacentHTML('beforeend', newMessage)

    let lastMessage = document.querySelector('#messages .message__wrapper:last-child')
    if(lastMessage){
        lastMessage.scrollIntoView()
    }
}

let leaveChannel = async () => {
    await channel.leave()
    await rtmClient.logout()
}

window.addEventListener('beforeunload', leaveChannel)
let messageForm = document.getElementById('message__form')
messageForm.addEventListener('submit', sendMessage)