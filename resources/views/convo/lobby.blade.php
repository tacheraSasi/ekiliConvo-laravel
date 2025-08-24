<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ekiliConvo - Video Conversations Made Simple</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Modern gradient background with animated particles -->
    <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-slate-900 via-brand-darker to-slate-800">
        <!-- Animated background elements -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-brand-primary rounded-full mix-blend-multiply filter blur-xl animate-float"></div>
            <div class="absolute top-1/3 right-1/4 w-80 h-80 bg-brand-accent rounded-full mix-blend-multiply filter blur-xl animate-float" style="animation-delay: -1s;"></div>
            <div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-brand-secondary rounded-full mix-blend-multiply filter blur-xl animate-float" style="animation-delay: -2s;"></div>
        </div>

        <!-- Navigation -->
        <nav class="relative z-20 bg-white/10 backdrop-blur-md border-b border-white/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="relative">
                            <x-application-logo class="w-10 h-10 fill-current text-brand-primary transition-transform duration-300 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-brand-primary rounded-full opacity-20 blur-lg group-hover:opacity-30 transition-opacity duration-300"></div>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-white tracking-tight">ekiliConvo</h1>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <div class="flex items-center space-x-4">
                        <a href="https://www.ekilie.com" 
                           class="flex items-center gap-2 px-4 py-2 text-brand-light hover:text-white transition-colors duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20 7.093v-5.093h-3v2.093l3 3zm4 5.907l-12-12-12 12h3v10h7v-5h4v5h7v-10h3zm-5 8h-3v-5h-8v5h-3v-10.26l7-6.912 7 6.99v10.182z"/>
                            </svg>
                            <span class="hidden sm:inline">ekilie</span>
                        </a>
                        
                        @auth
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center gap-2 px-4 py-2 bg-brand-secondary hover:bg-brand-dark text-white rounded-lg transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/>
                                </svg>
                                <span class="hidden sm:inline">Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="px-4 py-2 text-brand-light hover:text-white transition-colors duration-200">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" 
                               class="px-4 py-2 bg-brand-primary hover:bg-brand-secondary text-brand-darker rounded-lg transition-colors duration-200 font-medium">
                                Sign Up
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="relative z-10 min-h-screen flex items-center justify-center px-4 py-16">
            <div class="max-w-2xl mx-auto text-center">
                <!-- Hero Section -->
                <div class="mb-12 animate-fade-in">
                    <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 tracking-tight">
                        Connect.
                        <span class="text-brand-primary">Collaborate.</span>
                        <span class="text-brand-accent">Create.</span>
                    </h1>
                    <p class="text-xl text-brand-light max-w-2xl mx-auto leading-relaxed">
                        Join video conversations instantly. No downloads required. Built for modern teams and individuals who value simplicity and privacy.
                    </p>
                </div>

                <!-- Status Messages -->
                @if (session('info'))
                    <div class="mb-6 p-4 bg-blue-500/20 border border-blue-400/30 rounded-lg backdrop-blur-sm animate-slide-up">
                        <p class="text-blue-200">{{ session('info') }}</p>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 p-4 bg-red-500/20 border border-red-400/30 rounded-lg backdrop-blur-sm animate-slide-up">
                        <p class="text-red-200">{{ session('error') }}</p>
                    </div>
                @endif

                <!-- Main Form Card -->
                <div class="glass bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8 max-w-md mx-auto animate-slide-up" style="animation-delay: 0.2s;">
                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl p-6">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                @auth
                                    Welcome back, {{ Auth::user()->name }}!
                                @else
                                    Join the Conversation
                                @endif
                            </h2>
                            <p class="text-gray-600 dark:text-gray-400">
                                @auth
                                    Create a new room or join an existing one
                                @else
                                    Enter a room code to join as a guest
                                @endif
                            </p>
                        </div>

                        @auth
                            <!-- Authenticated User Form -->
                            <form id="lobby__form" class="space-y-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Display Name</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="name"
                                               value="{{ Auth::user()->name }}"
                                               required 
                                               placeholder="Enter your display name..."
                                               class="form-input pl-10 w-full" />
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Code (Optional)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="room"
                                               value="{{ session('joining_room') ?? '' }}"
                                               placeholder="Enter room code to join existing room..."
                                               class="form-input pl-10 w-full" />
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Leave empty to create a new room</p>
                                </div>

                                <button type="submit" 
                                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-brand-secondary hover:bg-brand-dark text-white rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2">
                                    <span>Join Room</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </button>
                            </form>

                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                <a href="{{ route('dashboard') }}" 
                                   class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-brand-primary hover:bg-brand-accent text-brand-darker rounded-lg font-medium transition-all duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                                    </svg>
                                    <span>Go to Dashboard</span>
                                </a>
                            </div>
                        @else
                            <!-- Guest Form -->
                            <form id="guest__form" class="space-y-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your Name</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="guest_name"
                                               required 
                                               placeholder="Enter your display name..."
                                               class="form-input pl-10 w-full" />
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Code</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               name="room_code"
                                               value="{{ session('joining_room') ?? '' }}"
                                               required 
                                               placeholder="Enter room code to join..."
                                               class="form-input pl-10 w-full" />
                                    </div>
                                </div>

                                <button type="submit" 
                                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-brand-secondary hover:bg-brand-dark text-white rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Join as Guest</span>
                                </button>
                            </form>

                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600 space-y-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                                    Want to create your own rooms?
                                </p>
                                <div class="flex gap-3">
                                    <a href="{{ route('register') }}" 
                                       class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-brand-primary hover:bg-brand-accent text-brand-darker rounded-lg font-medium transition-colors duration-200">
                                        <span>Sign Up</span>
                                    </a>
                                    <a href="{{ route('login') }}" 
                                       class="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors duration-200">
                                        <span>Sign In</span>
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Features -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 animate-fade-in" style="animation-delay: 0.4s;">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-brand-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">HD Video Quality</h3>
                        <p class="text-brand-light text-sm">Crystal clear video calls with optimized streaming technology</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-brand-accent/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-brand-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Secure & Private</h3>
                        <p class="text-brand-light text-sm">End-to-end encryption ensures your conversations stay private</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-brand-secondary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-brand-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Instant Access</h3>
                        <p class="text-brand-light text-sm">No downloads required. Join conversations directly from your browser</p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Theme toggle -->
    <x-theme-toggle />

    <script>
        @auth
            const user_id = "{{ Auth::user()->email }}";
        @else
            const user_id = null;
        @endauth

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
                        // Go to dashboard to create new room
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
</body>
</html>