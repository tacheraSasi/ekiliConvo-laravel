<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>ekiliConvo</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='{{asset("assets/styles/main.css")}}'>
    <link rel='stylesheet' type='text/css' media='screen' href='{{asset("assets/styles/room.css")}}'>
</head>
<body>
    <header id="nav">
       <div class="nav--list">
            <a href="{{route('home')}}">>
                <h3 id="logo">
                    <span>ekiliConvo</span>
                </h3>
            </a>
       </div>

        <div id="nav__links">
            <a class="nav__link" href="https://www.ekilie.com">
                ekilie
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ede0e0" viewBox="0 0 24 24"><path d="M20 7.093v-5.093h-3v2.093l3 3zm4 5.907l-12-12-12 12h3v10h7v-5h4v5h7v-10h3zm-5 8h-3v-5h-8v5h-3v-10.26l7-6.912 7 6.99v10.182z"/></svg>
            </a>
            @auth
                <a class="nav__link" href="{{route('dashboard')}}">
                    Dashboard
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ede0e0" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/></svg>
                </a>
            @endauth
        </div>
    </header>

    <main id="room__lobby__container">
        
        <div id="form__container">
             <div id="form__container__header">
                 <p>Create or Join a Convo</p>
             </div>

             @if (session('info'))
                 <div style="background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                     {{ session('info') }}
                 </div>
             @endif

             @if (session('error'))
                 <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                     {{ session('error') }}
                 </div>
             @endif

             @auth
                 <!-- Quick Join Form for authenticated users -->
                 <form id="lobby__form">
                     <div class="form__field__wrapper">
                         <label>Your Name</label>
                         <input type="text" name="name"  
                         value="{{Auth::user()->name}}"
                         required placeholder="Enter your display name..." />
                     </div>

                     <div class="form__field__wrapper">
                         <label>Room Code (optional)</label>
                         <input type="text" name="room" 
                         value="{{session('joining_room') ?? ''}}"   
                         placeholder="Enter room code to join existing room..." />
                     </div>

                     <div class="form__field__wrapper">
                         <button type="submit">Join Room 
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/></svg>
                        </button>
                     </div>
                </form>

                <div style="text-align: center; margin: 20px 0; color: #bbb;">
                    <span>OR</span>
                </div>

                <div style="text-align: center;">
                    <a href="{{route('dashboard')}}" style="background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                        Go to Dashboard to Create Room
                    </a>
                </div>
             @else
                 <!-- Guest Form -->
                 <form id="guest__form">
                     <div class="form__field__wrapper">
                         <label>Your Name</label>
                         <input type="text" name="guest_name" required placeholder="Enter your display name..." />
                     </div>

                     <div class="form__field__wrapper">
                         <label>Room Code</label>
                         <input type="text" name="room_code" 
                         value="{{session('joining_room') ?? ''}}"   
                         required placeholder="Enter room code to join..." />
                     </div>

                     <div class="form__field__wrapper">
                         <button type="submit">Join as Guest
                             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/></svg>
                        </button>
                     </div>
                </form>

                <div style="text-align: center; margin: 20px 0; color: #bbb;">
                    <span>OR</span>
                </div>

                <div style="text-align: center;">
                    <a href="{{route('register')}}" style="background: #28a745; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px;">
                        Sign Up
                    </a>
                    <a href="{{route('login')}}" style="background: #6c757d; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;">
                        Login
                    </a>
                </div>
             @endauth
        </div>
     </main>
    
</body>
<script>
    @auth
        const user_id = "{{Auth::user()->email}}";
    @else
        const user_id = null;
    @endauth
</script>
<script src="{{asset('assets/js/lobby.js')}}"></script>
<script>
    @auth
        // Authenticated user form handler
        let form = document.getElementById('lobby__form')
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault()
                
                sessionStorage.setItem('display_name', e.target.name.value)
                
                let roomCode = e.target.room.value
                if (roomCode) {
                    // Join existing room
                    window.location = `/room/${roomCode}`
                } else {
                    // Create new room with default name
                    window.location = `/dashboard`
                }
            })
        }
    @else
        // Guest form handler
        let guestForm = document.getElementById('guest__form')
        if (guestForm) {
            guestForm.addEventListener('submit', (e) => {
                e.preventDefault()
                
                sessionStorage.setItem('display_name', e.target.guest_name.value)
                
                let roomCode = e.target.room_code.value
                if (roomCode) {
                    window.location = `/room/${roomCode}`
                } else {
                    alert('Please enter a room code to join.')
                }
            })
        }
    @endauth
</script>
</html>