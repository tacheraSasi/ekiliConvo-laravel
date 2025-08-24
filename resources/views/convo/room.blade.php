<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>{{$room}} | ekiliConvo</title>
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
<body class="font-sans antialiased bg-neutral-900 text-white text-sm p-0 m-0 custom-scrollbar">

    <header class="fixed top-0 left-0 w-full bg-neutral-800/95 backdrop-blur-sm border-b border-neutral-700 z-50 h-18">
        <div class="flex justify-between items-center px-6 py-4 h-full">
            <!-- Left section: Logo and members button -->
            <div class="flex items-center gap-4">
                <button id="members__button" class="hidden cursor-pointer bg-transparent border-none p-2 hover:bg-neutral-700 rounded-md transition-colors sm:block">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" class="fill-gray-300 hover:fill-brand-primary transition-colors">
                        <path d="M24 18v1h-24v-1h24zm0-6v1h-24v-1h24zm0-6v1h-24v-1h24z" />
                    </svg>
                </button>
                <a href="{{route('home')}}" class="text-white no-underline">
                    <h3 class="text-xl font-semibold text-white m-0">
                        <span>ekiliConvo</span>
                    </h3>
                </a>
            </div>

            <!-- Right section: Navigation links -->
            <div class="flex items-center gap-6">
                <button id="chat__button" class="hidden cursor-pointer bg-transparent border-none p-2 hover:bg-neutral-700 rounded-md transition-colors md:block">
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill-rule="evenodd" clip-rule="evenodd" class="fill-gray-300 hover:fill-brand-primary transition-colors">
                        <path d="M24 20h-3v4l-5.333-4h-7.667v-4h2v2h6.333l2.667 2v-2h3v-8.001h-2v-2h4v12.001zm-15.667-6l-5.333 4v-4h-3v-14.001l18 .001v14h-9.667zm-6.333-2h3v2l2.667-2h8.333v-10l-14-.001v10.001z"/>
                    </svg>
                </button>
                <a id="create__room__btn" href="{{route('dashboard')}}" class="bg-brand-primary hover:bg-brand-accent text-neutral-900 px-6 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center gap-2 sm:px-3 sm:text-xs">
                    <span class="hidden sm:inline">Dashboard</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24" class="sm:w-6 sm:h-6">
                        <path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <main class="mt-18 h-[calc(100vh-4.5rem)] w-full relative">
        <div class="relative flex h-full">

            <!-- Members container - left sidebar -->
            <section id="members__container" class="bg-neutral-800 border-r border-neutral-600 overflow-y-auto w-full max-w-[250px] fixed h-full z-[999] custom-scrollbar">

                <div id="members__header" class="flex justify-around items-center px-4 py-3 fixed text-lg bg-neutral-700 w-[218px] border-b border-neutral-600">
                    <p class="text-white font-medium">Participants</p>
                    <strong id="members__count" class="bg-neutral-900 px-4 py-2 text-sm font-semibold rounded-md text-brand-primary">0</strong>
                </div>

                <div id="member__list" class="flex flex-col gap-4 pt-20 pb-28 px-4">
                    <!-- Members will be dynamically added here -->
                </div>

            </section>

            <!-- Stream container - main video area -->
            <section id="stream__container" class="flex-1 ml-0 xl:ml-[250px] w-full xl:w-[calc(100%-400px)] bg-neutral-900 relative overflow-hidden">

                <div id="stream__box" class="w-full h-full bg-neutral-900 flex items-center justify-center">
                    <!-- Main stream content -->
                </div>

                <div id="streams__container" class="absolute top-4 right-4 flex flex-wrap gap-3 max-w-sm">
                    <!-- Video streams will be added here -->
                </div>

                <!-- Stream controls - positioned at bottom -->
                <div class="stream__actions absolute bottom-6 left-1/2 transform -tranneutral-x-1/2 flex items-center gap-4 bg-neutral-800/90 backdrop-blur-sm px-6 py-3 rounded-full border border-neutral-600">
                    <button id="camera-btn" class="active w-12 h-12 rounded-full bg-neutral-700 hover:bg-brand-primary border-none cursor-pointer transition-all duration-200 flex items-center justify-center group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="fill-gray-300 group-hover:fill-neutral-900 group-[.active]:fill-white">
                            <path d="M5 4h-3v-1h3v1zm10.93 0l.812 1.219c.743 1.115 1.987 1.781 3.328 1.781h1.93v13h-20v-13h3.93c1.341 0 2.585-.666 3.328-1.781l.812-1.219h5.86zm1.07-2h-8l-1.406 2.109c-.371.557-.995.891-1.664.891h-5.93v17h24v-17h-3.93c-.669 0-1.293-.334-1.664-.891l-1.406-2.109zm-11 8c0-.552-.447-1-1-1s-1 .448-1 1 .447 1 1 1 1-.448 1-1zm7 0c1.654 0 3 1.346 3 3s-1.346 3-3 3-3-1.346-3-3 1.346-3 3-3zm0-2c-2.761 0-5 2.239-5 5s2.239 5 5 5 5-2.239 5-5-2.239-5-5-5z"/>
                        </svg>
                    </button>
                    <button id="mic-btn" class="active w-12 h-12 rounded-full bg-neutral-700 hover:bg-brand-primary border-none cursor-pointer transition-all duration-200 flex items-center justify-center group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="fill-gray-300 group-hover:fill-neutral-900 group-[.active]:fill-white">
                            <path d="M12 2c1.103 0 2 .897 2 2v7c0 1.103-.897 2-2 2s-2-.897-2-2v-7c0-1.103.897-2 2-2zm0-2c-2.209 0-4 1.791-4 4v7c0 2.209 1.791 4 4 4s4-1.791 4-4v-7c0-2.209-1.791-4-4-4zm8 9v2c0 4.418-3.582 8-8 8s-8-3.582-8-8v-2h2v2c0 3.309 2.691 6 6 6s6-2.691 6-6v-2h2zm-7 13v-2h-2v2h-4v2h10v-2h-4z"/>
                        </svg>
                    </button>
                    <button id="screen-btn" class="w-12 h-12 rounded-full bg-neutral-700 hover:bg-brand-primary border-none cursor-pointer transition-all duration-200 flex items-center justify-center group">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="fill-gray-300 group-hover:fill-neutral-900">
                            <path d="M0 1v17h24v-17h-24zm22 15h-20v-13h20v13zm-6.599 4l2.599 3h-12l2.599-3h6.802z"/>
                        </svg>
                    </button>
                    <button id="leave-btn" class="w-12 h-12 rounded-full bg-red-500 hover:bg-red-600 border-none cursor-pointer transition-all duration-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" class="fill-white">
                            <path d="M16 10v-5l8 7-8 7v-5h-8v-4h8zm-16-8v20h14v-2h-12v-16h12v-2h-14z"/>
                        </svg>
                    </button>
                </div>

                <button id="join-btn" class="bg-brand-secondary hover:bg-brand-primary text-white text-lg px-12 py-6 border-none fixed bottom-4 left-1/2 transform -tranneutral-x-1/2 rounded-lg cursor-pointer font-medium transition-all duration-200 shadow-lg">
                    Join Stream
                </button>
            </section>

            <!-- Messages container - right sidebar -->
            <section id="messages__container" class="h-[calc(100vh-85px)] bg-neutral-800 right-0 absolute w-full max-w-md overflow-y-auto border-l border-neutral-600 hidden lg:block custom-scrollbar">

                <div id="messages" class="w-full h-full flex flex-col p-4 pb-20 gap-3">
                    <!-- Messages will be dynamically added here -->
                </div>

                <form id="message__form" class="absolute bottom-0 left-0 right-0 p-4 bg-neutral-800 border-t border-neutral-600">
                    <input type="text" name="message" placeholder="Send a message...."
                           class="w-full px-4 py-3 bg-neutral-700 text-white border border-neutral-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-primary focus:border-brand-primary placeholder-gray-400 transition-all duration-200" />
                </form>

            </section>
        </div>
    </main>

</body>
<script>
    //configs
    const APP_ID = "{{env('AGORA_APP_ID')}}"
    const ROOM_UUID = "{{$roomUuid ?? ''}}"
    const IS_HOST = {{$isHost ? 'true' : 'false'}}
</script>
<script type="text/javascript" src="{{asset('assets/js/AgoraRTC_N-4.11.0.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/agora-rtm-sdk-1.4.4.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/room.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/room_rtm.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/room_rtc.js')}}"></script>
<script type="text/javascript" src="{{asset('assets/js/enterprise-features.js')}}"></script>
</html>
