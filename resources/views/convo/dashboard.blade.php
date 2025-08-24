<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | ekiliConvo</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-neutral-50 dark:bg-neutral-900">
    <!-- Navigation -->
    <nav class="bg-white dark:bg-neutral-800 shadow-sm border-b border-neutral-200 dark:border-neutral-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="relative">
                        <x-application-logo class="w-10 h-10 fill-current text-brand-primary transition-transform duration-300 group-hover:scale-110" />
                        <div class="absolute inset-0 bg-brand-primary rounded-full opacity-20 blur-lg group-hover:opacity-30 transition-opacity duration-300"></div>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-neutral-900 dark:text-white tracking-tight">ekiliConvo</h1>
                    </div>
                </a>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4">
                    <a href="https://www.ekilie.com"
                       class="flex items-center gap-2 px-3 py-2 text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20 7.093v-5.093h-3v2.093l3 3zm4 5.907l-12-12-12 12h3v10h7v-5h4v5h7v-10h3zm-5 8h-3v-5h-8v5h-3v-10.26l7-6.912 7 6.99v10.182z"/>
                        </svg>
                        <span class="hidden sm:inline">ekilie</span>
                    </a>

                    <a href="{{ route('home') }}"
                       class="flex items-center gap-2 px-3 py-2 text-neutral-600 dark:text-neutral-300 hover:text-neutral-900 dark:hover:text-white transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/>
                        </svg>
                        <span class="hidden sm:inline">Lobby</span>
                    </a>

                    <!-- User Menu -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-neutral-600 dark:text-neutral-300 hidden sm:inline">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit"
                                    class="flex items-center gap-2 px-3 py-2 text-neutral-600 dark:text-neutral-300 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M16 9v-4l8 7-8 7v-4h-8v-6h8zm-2 0h-6v4h6v2.5l3.5-3.5-3.5-3.5v2.5z"/>
                                </svg>
                                <span class="hidden sm:inline">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-neutral-900 dark:text-white">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="mt-2 text-neutral-600 dark:text-neutral-400">Manage your video conference rooms and join conversations.</p>
        </div>

        <!-- Status Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                <div class="flex">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="ml-3 text-green-800 dark:text-green-200">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="ml-3 text-red-800 dark:text-red-200">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Quick Join -->
            <div class="modern-card p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-brand-primary/10 rounded-lg">
                        <svg class="w-6 h-6 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Quick Join</h3>
                </div>
                <p class="text-neutral-600 dark:text-neutral-400 text-sm mb-4">Join an existing room with a room code</p>
                <a href="{{ route('home') }}" class="btn-primary w-full text-center">Go to Lobby</a>
            </div>

            <!-- Create Room -->
            <div class="modern-card p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-brand-accent/10 rounded-lg">
                        <svg class="w-6 h-6 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Create Room</h3>
                </div>
                <p class="text-neutral-600 dark:text-neutral-400 text-sm mb-4">Start a new video conference room</p>
                <button onclick="document.getElementById('create-form').scrollIntoView({behavior: 'smooth'})"
                        class="btn-primary w-full">Create New Room</button>
            </div>

            <!-- Room Stats -->
            <div class="modern-card p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-3 bg-brand-secondary/10 rounded-lg">
                        <svg class="w-6 h-6 text-brand-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-neutral-900 dark:text-white">Your Stats</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600 dark:text-neutral-400">Created Rooms</span>
                        <span class="font-medium text-neutral-900 dark:text-white">{{ $createdRooms->count() }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-600 dark:text-neutral-400">Joined Rooms</span>
                        <span class="font-medium text-neutral-900 dark:text-white">{{ $joinedRooms->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Room Form -->
        <div id="create-form" class="modern-card p-6 mb-8">
            <h2 class="text-xl font-bold text-neutral-900 dark:text-white mb-6">Create New Room</h2>
            <form method="POST" action="{{ route('rooms.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label for="name" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Room Name</label>
                        <input type="text"
                               id="name"
                               name="name"
                               required
                               placeholder="Enter room name"
                               class="form-input w-full" />
                    </div>
                    <div>
                        <label for="visibility" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Visibility</label>
                        <select id="visibility" name="visibility" class="form-input w-full">
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                    <div>
                        <label for="waiting_room_enabled" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300 mb-2">Waiting Room</label>
                        <select id="waiting_room_enabled" name="waiting_room_enabled" class="form-input w-full">
                            <option value="0">Disabled</option>
                            <option value="1">Enabled</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Room
                    </button>
                </div>
            </form>
        </div>

        <!-- Created Rooms -->
        <section class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">Your Created Rooms</h2>
                <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ $createdRooms->count() }} rooms</span>
            </div>

            @if($createdRooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($createdRooms as $room)
                        <div class="modern-card p-6 group hover:shadow-xl transition-all duration-300">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white group-hover:text-brand-primary transition-colors duration-200">
                                    {{ $room->name }}
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $room->visibility === 'public' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400' }}">
                                    {{ ucfirst($room->visibility) }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm text-neutral-600 dark:text-neutral-400 mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Created {{ $room->created_at->format('M j, Y') }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    {{ $room->users->count() }} participants
                                </div>
                                @if($room->waiting_room_enabled)
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Waiting room enabled
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('room', $room->uuid) }}"
                                   class="flex-1 btn-primary text-center text-sm py-2">
                                    Join Room
                                </a>
                                <button onclick="copyRoomLink('{{ route('join-room', $room->uuid) }}')"
                                        class="btn-secondary px-3 py-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                                <form method="POST" action="{{ route('rooms.destroy', $room->uuid) }}" class="inline"
                                      onsubmit="return confirm('Are you sure you want to delete this room?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/20 dark:hover:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-white mb-2">No rooms created yet</h3>
                    <p class="text-neutral-600 dark:text-neutral-400 mb-4">Create your first room to start hosting video conversations</p>
                    <button onclick="document.getElementById('create-form').scrollIntoView({behavior: 'smooth'})"
                            class="btn-primary">
                        Create Your First Room
                    </button>
                </div>
            @endif
        </section>

        <!-- Joined Rooms -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-neutral-900 dark:text-white">Rooms You've Joined</h2>
                <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ $joinedRooms->count() }} rooms</span>
            </div>

            @if($joinedRooms->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($joinedRooms as $room)
                        <div class="modern-card p-6 group hover:shadow-xl transition-all duration-300">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-semibold text-neutral-900 dark:text-white group-hover:text-brand-primary transition-colors duration-200">
                                    {{ $room->name }}
                                </h3>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    {{ ucfirst($room->pivot->role_in_room) }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm text-neutral-600 dark:text-neutral-400 mb-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Created by {{ $room->creator->name }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Joined {{ \Carbon\Carbon::parse($room->pivot->joined_at)->format('M j, Y') }}
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    {{ $room->users->count() }} participants
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('room', $room->uuid) }}"
                                   class="flex-1 btn-primary text-center text-sm py-2">
                                    Join Room
                                </a>
                                <button onclick="copyRoomLink('{{ route('join-room', $room->uuid) }}')"
                                        class="btn-secondary px-3 py-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-neutral-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-white mb-2">No joined rooms yet</h3>
                    <p class="text-neutral-600 dark:text-neutral-400 mb-4">Join rooms through invite links or room codes</p>
                    <a href="{{ route('home') }}" class="btn-primary">
                        Go to Lobby
                    </a>
                </div>
            @endif
        </section>
    </main>

    <!-- Theme Toggle -->
    <x-theme-toggle />

    <!-- Copy Link Toast -->
    <div id="copy-toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform translate-y-full opacity-0 transition-all duration-300 z-50">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Room link copied to clipboard!</span>
        </div>
    </div>

    <script>
        function copyRoomLink(url) {
            navigator.clipboard.writeText(url).then(function() {
                showCopyToast();
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
                    showCopyToast();
                } catch (err) {
                    console.error('Fallback: Oops, unable to copy', err);
                }
                document.body.removeChild(textArea);
            });
        }

        function showCopyToast() {
            const toast = document.getElementById('copy-toast');
            toast.classList.remove('translate-y-full', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.add('translate-y-full', 'opacity-0');
                toast.classList.remove('translate-y-0', 'opacity-100');
            }, 3000);
        }
    </script>
</body>
</html>
