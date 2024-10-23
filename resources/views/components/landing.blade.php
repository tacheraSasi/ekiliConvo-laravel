<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- General SEO for the Insights blog --}}
        <meta name="description" content="Insights - Your go-to blog for exploring diverse topics, expert opinions, and valuable insights on various aspects of life.">
        <meta name="keywords" content="Insights, blog, lifestyle, technology, health, finance, travel, education, articles, tutorials, Tachera Sasi">
        <meta name="author" content="Tachera Sasi">

        {{-- Open Graph for social media sharing --}}
        <meta property="og:title" content="Insights - Your Comprehensive Blog for Life's Essentials" />
        <meta property="og:description" content="Discover a wide range of topics and expert insights on lifestyle, technology, health, finance, and more at Insights." />
        <meta property="og:image" content="{{ asset('assets/img/insights-share-image.png') }}" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:type" content="website" />

        {{-- Twitter Meta Tags --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Insights - Your Comprehensive Blog for Life's Essentials">
        <meta name="twitter:description" content="Explore diverse topics, expert opinions, and valuable insights on lifestyle, technology, health, finance, and more at Insights." />
        <meta name="twitter:image" content="{{ asset('assets/img/insights-share-image.png') }}">

        {{-- Title for the Insights blog landing page --}}
        <title>{{ config('app.name', 'Insights') }} - Your Comprehensive Blog for Life's Essentials</title>

        {{-- Icons --}}
        <link rel="shortcut icon" href="{{ asset('assets/img/img.png') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{asset('assets/styles/bg-gradient.css')}}">
        
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-neutral-950">
        <div class="min-h-screen">
            <x-landing-header />

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
