<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby | ekiliConvo</title>
     <!-- Fonts -->
     <link rel="preconnect" href="https://fonts.bunny.net">
     <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

     <!-- Scripts -->
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }

        /* Mobile Menu Button */
        .mobile-menu-button {
            display: none;
        }

        /* Sidebar and Main Layout */
        aside {
            height: 100vh;
            width: 240px;
            position: fixed;
            left: 0;
            top: 0;
            padding: 15px;
            background-color: #2c2f33;
            transition: transform 0.3s ease;
        }

        main {
            margin-left: 250px;
        }

        /* Hide sidebar on smaller screens */
        @media (max-width: 768px) {
            aside {
                transform: translateX(-100%);
            }

            aside.open {
                transform: translateX(0);
            }

            main {
                margin-left: 0;
            }

            .mobile-menu-button {
                display: block;
                margin: 1rem;
                cursor: pointer;
                position: absolute;
                top: 1rem;
                left: 1rem;
                z-index: 100;
            }
        }

        /* Styling for interactive elements */
        button {
            transition: background-color 0.2s ease-in-out;
        }

        button:hover {
            background-color: #5ccf87;
        }

        input::placeholder {
            color: #cfcfcf;
        }

        input {
            outline: none;
        }

        a:hover {
            background-color: #646c78 !important;
            transition: background-color 0.2s ease-in-out;
        }

        .shadow-lg {
            box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.1);
        }

        .rounded-lg {
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-neutral-900 text-neutral-300">

    <!-- Mobile Menu Button -->
    <div class="mobile-menu-button text-white">
        <i class="fas fa-bars fa-2x"></i>
    </div>

    <div class="flex flex-col md:flex-row h-screen">
        <!-- Sidebar -->
        <aside class="md:w-64 w-full m-1 rounded-md bg-neutral-800 p-3 flex flex-col justify-between overflow-auto">
            <div>
                <!-- Logo -->
                <div class="text-white text-2xl font-bold flex items-center space-x-2 mb-6">
                    <span class="bg-[#74f5a1] text-white p-2 rounded-md">
                        <i class="fas fa-video"></i>
                    </span>
                    <span>ekiliConvo</span>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <input type="text" placeholder="Search Rooms" class="w-full px-4 py-2 rounded-md bg-neutral-700 text-white focus:border-green-500 focus:outline-none ">
                </div>

                <!-- Navigation -->
                <nav class="space-y-4">
                    <a href="#" class="flex items-center space-x-2 text-white bg-neutral-700 py-2 px-4 rounded-md">
                        <i class="fas fa-home"></i>
                        <span>Lobby</span>
                    </a>
                    <a href="#" class="flex items-center space-x-2 hover:bg-neutral-700 py-2 px-4 rounded-md">
                        <i class="fas fa-users"></i>
                        <span>Explore Rooms</span>
                    </a>
                    
                    <a href="#" class="flex items-center space-x-2 hover:bg-neutral-700 py-2 px-4 rounded-md">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>

            <!-- CTA for Paid Version -->
            <div class="mt-8 p-4 bg-neutral-700 rounded-lg">
                <h4 class="font-bold text-white mb-2">Try Premium!</h4>
                <p class="text-sm text-neutral-400">Upgrade to access exclusive features like recording, unlimited room capacity, and more.</p>
                <button class="bg-[#74f5a1] mt-4 py-2 w-full rounded-md text-black">Upgrade Now</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="bg-neutral-800 p-5 rounded-lg shadow-lg mb-4">
                <h4 class="text-white font-semibold mb-4">Active Calls</h4>
                
            </div>
            <!-- Top Section: Lobby Overview and Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-4">
                <!-- Lobby Overview -->
                <div class="bg-neutral-800 h-[260px] p-5 rounded-lg shadow-lg space-y-4">
                    <h4 class="text-white font-semibold">Lobby Overview</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between text-neutral-400">
                            <span><i class="fas fa-door-open text-[#74f5a1]"></i> Room 101</span>
                            <span class="text-green-500">Active</span>
                        </div>
                        <div class="flex justify-between text-neutral-400">
                            <span><i class="fas fa-door-open text-[#74f5a1]"></i> Room 202</span>
                            <span class="text-red-500">Inactive</span>
                        </div>
                        <div class="flex justify-between text-neutral-400">
                            <span><i class="fas fa-door-open text-[#74f5a1]"></i> Room 303</span>
                            <span class="text-green-500">Active</span>
                        </div>
                        <div class="flex justify-between text-neutral-400">
                            <span><i class="fas fa-door-open text-[#74f5a1]"></i> Room 404</span>
                            <span class="text-red-500">Inactive</span>
                        </div>
                    </div>
                </div>

                <!-- Current Call Stats -->
                <div class="bg-neutral-800 p-5 rounded-lg shadow-lg">
                    <h4 class="text-white font-semibold mb-4">Call Stats</h4>
                    <div class="flex justify-center items-center">
                        <div class="relative">
                            <div class="w-24 h-24 bg-neutral-700 rounded-full flex items-center justify-center">
                                <div class="text-3xl font-semibold text-white">35%</div>
                            </div>
                            <svg class="absolute top-0 left-0 w-full h-full text-[#74f5a1]" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke-width="10" stroke="#3d4451"></circle>
                                <circle cx="50" cy="50" r="45" fill="none" stroke-width="10" stroke="#74f5a1" stroke-dasharray="280" stroke-dashoffset="180"></circle>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-neutral-400">15 Active, 5 Missed Calls</p>
                    </div>
                </div>

                <!-- Quick Start Call -->
                <div class="bg-neutral-800 p-5 rounded-lg shadow-lg">
                    <h4 class="text-white font-semibold mb-4">Quick Start</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-neutral-400">Create Room</span>
                            <input type="text" class="bg-neutral-700 text-white rounded-md py-1 px-2 w-32" placeholder="Room Name">
                        </div>
                        <button class="bg-[#74f5a1] py-2 w-full rounded-md text-black">Start Call</button>
                    </div>
                </div>
            </div>

            <!-- Active Calls Section -->
            <div class="bg-neutral-800 p-5 rounded-lg shadow-lg">
                <h4 class="text-white font-semibold mb-4">Active Calls</h4>
                <div class="h-40 bg-neutral-700 rounded-lg">
                    <!-- Placeholder for active calls (You can replace this with a list of ongoing calls) -->
                    <div class="h-full flex items-center justify-center text-neutral-400">
                        <i class="fas fa-phone-alt fa-3x"></i> No active calls
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.querySelector('.mobile-menu-button').addEventListener('click', function () {
            document.querySelector('aside').classList.toggle('open');
        });
    </script>

</body>
</html>
