<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Dashboard | ekiliConvo</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='{{asset("assets/styles/main.css")}}'>
    <link rel='stylesheet' type='text/css' media='screen' href='{{asset("assets/styles/room.css")}}'>
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .room-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .room-card {
            background: #2a2a2a;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #444;
        }
        .room-card h3 {
            color: #ede0e0;
            margin: 0 0 10px 0;
        }
        .room-meta {
            color: #bbb;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .room-actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .section-title {
            color: #ede0e0;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .create-room-form {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            border: 1px solid #444;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            color: #ede0e0;
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #444;
            border-radius: 5px;
            background: #1a1a1a;
            color: #ede0e0;
        }
        .empty-state {
            text-align: center;
            color: #bbb;
            padding: 40px;
        }
    </style>
</head>
<body>
    <header id="nav">
       <div class="nav--list">
            <a href="{{route('dashboard')}}">
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
            <a class="nav__link" href="{{route('lobby')}}">
                Lobby
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ede0e0" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/></svg>
            </a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="nav__link" style="background: none; border: none; color: inherit; cursor: pointer;">
                    Logout
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#ede0e0" viewBox="0 0 24 24"><path d="M16 9v-4l8 7-8 7v-4h-8v-6h8zm-2 0h-6v4h6v2.5l3.5-3.5-3.5-3.5v2.5z"/></svg>
                </button>
            </form>
        </div>
    </header>

    <main class="dashboard-container">
        <h1 style="color: #ede0e0; margin-bottom: 30px;">Dashboard - Welcome, {{Auth::user()->name}}!</h1>

        @if (session('success'))
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Create Room Form -->
        <div class="create-room-form">
            <h2 style="color: #ede0e0; margin-bottom: 20px;">Create New Room</h2>
            <form method="POST" action="{{ route('rooms.store') }}">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 200px auto; gap: 15px; align-items: end;">
                    <div class="form-group">
                        <label for="name">Room Name</label>
                        <input type="text" id="name" name="name" required placeholder="Enter room name">
                    </div>
                    <div class="form-group">
                        <label for="visibility">Visibility</label>
                        <select id="visibility" name="visibility">
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Create Room</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Created Rooms -->
        <section>
            <h2 class="section-title">Your Created Rooms</h2>
            @if($createdRooms->count() > 0)
                <div class="room-grid">
                    @foreach($createdRooms as $room)
                        <div class="room-card">
                            <h3>{{ $room->name }}</h3>
                            <div class="room-meta">
                                <div>Created: {{ $room->created_at->format('M j, Y g:i A') }}</div>
                                <div>Visibility: {{ ucfirst($room->visibility) }}</div>
                                <div>Participants: {{ $room->users->count() }}</div>
                                @if($room->expires_at)
                                    <div>Expires: {{ $room->expires_at->format('M j, Y g:i A') }}</div>
                                @endif
                            </div>
                            <div class="room-actions">
                                <a href="{{ route('room', $room->uuid) }}" class="btn btn-primary">Join Room</a>
                                <button onclick="copyRoomLink('{{ route('join-room', $room->uuid) }}')" class="btn btn-secondary">Share Link</button>
                                <form method="POST" action="{{ route('rooms.destroy', $room->uuid) }}" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this room?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>You haven't created any rooms yet. Create your first room above!</p>
                </div>
            @endif
        </section>

        <!-- Joined Rooms -->
        <section style="margin-top: 50px;">
            <h2 class="section-title">Rooms You've Joined</h2>
            @if($joinedRooms->count() > 0)
                <div class="room-grid">
                    @foreach($joinedRooms as $room)
                        <div class="room-card">
                            <h3>{{ $room->name }}</h3>
                            <div class="room-meta">
                                <div>Created by: {{ $room->creator->name }}</div>
                                <div>Joined: {{ $room->pivot->joined_at }}</div>
                                <div>Your role: {{ ucfirst($room->pivot->role_in_room) }}</div>
                                <div>Participants: {{ $room->users->count() }}</div>
                            </div>
                            <div class="room-actions">
                                <a href="{{ route('room', $room->uuid) }}" class="btn btn-primary">Join Room</a>
                                <button onclick="copyRoomLink('{{ route('join-room', $room->uuid) }}')" class="btn btn-secondary">Share Link</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <p>You haven't joined any rooms yet. Join a room through an invite link!</p>
                </div>
            @endif
        </section>
    </main>

    <script>
        function copyRoomLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                alert('Room link copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
                // Fallback for older browsers
                const textArea = document.createElement("textarea");
                textArea.value = url;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert('Room link copied to clipboard!');
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }
                document.body.removeChild(textArea);
            });
        }
    </script>
</body>
</html>