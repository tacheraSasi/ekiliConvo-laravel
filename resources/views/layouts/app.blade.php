@props(['insight'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (isset($insight))
        <!-- Standard Meta Tags -->
        <meta name="description" content="{{ $insight->title }} - {!! Str::limit(strip_tags($insight->content), 300) !!} {{ $insight->created_at }} by {{ $insight->user->name }} on insights.ekilie.com">
        <meta name="keywords" content="{{ $insight->title }}, insights, ekilie, tachera sasi, ekiliSense, ekiliConvo, {{ $insight->slug }}, {{ $insight->user->name }}, insights.ekilie.com">
        <meta name="author" content="{{ $insight->user->name }}, INSIGHTS, EKILIE">

        <!-- Open Graph / Facebook -->
        <meta property="og:title" content="{{ $insight->title }} - Insights | insights.ekilie.com" />
        <meta property="og:description" content="{{ Str::limit(strip_tags($insight->content), 300) }}" />
        <meta property="og:image" content="{{ asset('assets/img/icon.png') }}" />
        <meta property="og:url" content="https://insights.ekilie.com/insights/{{ $insight->id }}" />
        <meta property="og:type" content="article" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $insight->title }} - Insights | insights.ekilie.com" />
        <meta name="twitter:description" content="{{ Str::limit(strip_tags($insight->content), 300) }}" />
        <meta name="twitter:image" content="{{ asset('assets/img/icon.png') }}" />
        <meta name="twitter:url" content="https://insights.ekilie.com/insights/{{ $insight->id }}" />

        <!-- Canonical URL -->
        <link rel="canonical" href="https://insights.ekilie.com/insights/{{ $insight->id }}" />
        
        <!-- Schema.org markup for Google -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Article",
            "headline": "{{ $insight->title }}",
            "author": {
                "@type": "Person",
                "name": "{{ $insight->user->name }}"
            },
            "datePublished": "{{ $insight->created_at->toIso8601String() }}",
            "description": "{{ Str::limit(strip_tags($insight->content), 300) }}",
            "publisher": {
                "@type": "Organization",
                "name": "Ekilie",
                "url": "https://insights.ekilie.com"
            },
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "https://insights.ekilie.com/insights/{{ $insight->id }}"
            }
        }
        </script>
    @endif

    <title>
        @if (isset($insight))
            {{ $insight->title }} - INSIGHTS | insights.ekilie.com
        @else
            Insights
        @endif
    </title>

    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('assets/img/icon.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script type="module" src="https://md-block.verou.me/md-block.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script> --}}
</head>

<body class="font-sans antialiased bg-gray-100 dark:bg-neutral-950">
    <div class="min-h-screen">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-neutral-900 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
</body>
<x-tinymce-config />

<script src="{{asset('assets/js/main.js')}}"></script>

</html>
