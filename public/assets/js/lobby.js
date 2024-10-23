let form = document.getElementById('lobby__form')
console.log(form)
console.log(user_id)
let displayName = sessionStorage.getItem('display_name')
if(displayName){
    form.name.value = displayName
}

form.addEventListener('submit', (e) => {
    e.preventDefault()

    sessionStorage.setItem('display_name', e.target.name.value)

    let inviteCode = e.target.room.value
    if(!inviteCode){
        inviteCode = String(user_id)
    }
    window.location = `/room/${inviteCode}`
})



  document.addEventListener('DOMContentLoaded', function () {
    // Check if the browser supports notifications
    if ('Notification' in window) {
      // Request permission to show notifications
      Notification.requestPermission().then(function (permission) {
        if (permission === 'granted') {
          // Display the welcome notification
          showWelcomeNotification();
        }
      });
    }
  });

  function showWelcomeNotification() {
    // Notification content
    const notificationOptions = {
      body: 'Welcome to ekiliConvo! Explore real-time live conference video calls and messaging.',
      icon: 'https://www.ekilie.com/assets/img/favicon.jpeg',
      vibrate: [200, 100, 200], // Vibration pattern (milliseconds)
      tag: 'welcome-notification', // Unique identifier for this notification
      actions: [
        { action: 'explore', title: 'Explore Now', icon: 'images/icons/explore.png' },
        { action: 'dismiss', title: 'Dismiss', icon: 'images/icons/dismiss.png' }
      ]
    };

    // Create and show the notification
    const welcomeNotification = new Notification('Welcome to ekiliConvo', notificationOptions);

    // Handle user interaction with the notification
    welcomeNotification.addEventListener('click', function (event) {
      // Check the action selected by the user
      if (event.action === 'explore') {
        // Perform action when "Explore Now" is clicked
        window.location.href = 'https://convo.ekilie.com';
      } else if (event.action === 'dismiss') {
        // Perform action when "Dismiss" is clicked
        console.log('User dismissed the welcome notification.');
      }

      // Close the notification
      welcomeNotification.close();
    });

    // Close the notification after a certain time (optional)
    setTimeout(() => {
      welcomeNotification.close();
    }, 10000); // 10000 milliseconds (10 seconds)
  }

