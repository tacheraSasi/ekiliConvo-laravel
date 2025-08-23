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