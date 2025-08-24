<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'ekiliConvo') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- Modern gradient background with animated particles -->
        <div class="min-h-screen relative overflow-hidden bg-gradient-to-br from-slate-900 via-brand-darker to-slate-800">
            <!-- Animated background elements -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-1/4 w-72 h-72 bg-brand-primary rounded-full mix-blend-multiply filter blur-xl animate-float"></div>
                <div class="absolute top-0 right-1/4 w-72 h-72 bg-brand-accent rounded-full mix-blend-multiply filter blur-xl animate-float" style="animation-delay: -1s;"></div>
                <div class="absolute bottom-1/4 left-1/3 w-72 h-72 bg-brand-secondary rounded-full mix-blend-multiply filter blur-xl animate-float" style="animation-delay: -2s;"></div>
            </div>

            <!-- Main content -->
            <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
                <!-- Logo and brand -->
                <div class="mb-8 animate-fade-in">
                    <a href="/" class="flex items-center gap-3 group">
                        <div class="relative">
                            <x-application-logo class="w-16 h-16 fill-current text-brand-primary transition-transform duration-300 group-hover:scale-110" />
                            <div class="absolute inset-0 bg-brand-primary rounded-full opacity-20 blur-lg group-hover:opacity-30 transition-opacity duration-300"></div>
                        </div>
                        <div class="text-left">
                            <h1 class="text-3xl font-bold text-white tracking-tight">ekiliConvo</h1>
                            <p class="text-brand-light text-sm">Connect. Collaborate. Create.</p>
                        </div>
                    </a>
                </div>

                <!-- Auth card with glass morphism -->
                <div class="w-full sm:max-w-md animate-slide-up">
                    <div class="glass bg-white/10 dark:bg-black/20 backdrop-blur-xl border border-white/20 dark:border-white/10 rounded-2xl shadow-2xl p-8">
                        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-xl p-6 shadow-inner">
                            {{ $slot }}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center animate-fade-in" style="animation-delay: 0.3s;">
                    <p class="text-brand-light/80 text-sm">
                        © 2024 ekiliConvo. Secure video conversations made simple.
                    </p>
                    <div class="flex justify-center gap-4 mt-2">
                        <a href="https://www.ekilie.com" class="text-brand-light hover:text-brand-primary transition-colors duration-200 text-sm">
                            About ekilie
                        </a>
                        <span class="text-brand-light/50">•</span>
                        <a href="#" class="text-brand-light hover:text-brand-primary transition-colors duration-200 text-sm">
                            Privacy Policy
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Theme toggle button -->
        <x-theme-toggle />
    </body>
</html>
