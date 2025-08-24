<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Room Password | ekiliConvo</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="images/icons/favicon.jpeg" rel="icon">
    <link href="images/icons/favicon.jpeg" rel="apple-touch-icon">
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-900 text-white">
    <header class="fixed top-0 left-0 w-full bg-slate-800/95 backdrop-blur-sm border-b border-slate-700 z-50">
        <div class="flex justify-between items-center px-6 py-4">
            <a href="{{route('home')}}" class="text-white no-underline">
                <h3 class="text-xl font-semibold text-white m-0">
                    <span>ekiliConvo</span>
                </h3>
            </a>
        </div>
    </header>

    <main class="flex justify-center items-center min-h-screen pt-20">
        <div class="bg-slate-800 rounded-lg shadow-xl p-8 max-w-md w-full mx-4 border border-slate-700">
            <div class="text-center mb-6">
                <p class="text-xl text-gray-300 m-0">🔒 This room is password protected</p>
            </div>

            <div class="space-y-6">
                <h2 class="text-center text-2xl font-bold text-white mb-4">Enter Room Password</h2>
                <p class="text-center text-gray-300 mb-6">Room: <strong class="text-brand-primary">{{ $roomName }}</strong></p>
                
                @if ($errors->any())
                    <div class="bg-red-500/20 border border-red-400/30 rounded-lg p-4 mb-6">
                        @foreach ($errors->all() as $error)
                            <p class="text-red-200 m-0">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form id="password-form" method="POST" action="{{ route('room.validate-password', $roomUuid) }}" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-300">Password</label>
                        <input type="password" name="password" placeholder="Enter room password" required
                               class="w-full px-4 py-3 bg-slate-700 text-white border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary placeholder-gray-400 transition-all duration-200" />
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-brand-primary hover:bg-brand-accent text-slate-900 font-semibold py-3 px-4 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-brand-primary focus:ring-offset-2 focus:ring-offset-slate-800">
                            Join Room
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-brand-primary text-sm transition-colors duration-200">
                        ← Back to Lobby
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>