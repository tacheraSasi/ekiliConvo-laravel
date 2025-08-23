<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lobby | ekiliConvo</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <meta name="theme-color" content="#74f5a1">
    <meta name="color-scheme" content="light dark">
</head>
<body class="h-full bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-900 dark:to-slate-800 text-gray-900 dark:text-slate-100 antialiased">

    <!-- Mobile Menu Button -->
    <div class="mobile-menu-button fixed top-4 left-4 z-50 md:hidden">
        <button class="p-3 rounded-xl glass text-gray-700 dark:text-slate-300 hover:text-brand-primary transition-colors duration-200">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-overlay fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden transition-opacity duration-300"></div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <aside class="sidebar-container w-80 h-full fixed left-0 top-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out z-50">
            <div class="h-full p-6 glass border-r border-gray-200/50 dark:border-slate-700/50 backdrop-blur-xl">
                <div class="flex flex-col h-full">
                    <!-- Header Section -->
                    <div class="flex-none">
                        <!-- Logo -->
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <span class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-brand-primary to-brand-accent rounded-xl shadow-lg">
                                        <i class="fas fa-video text-slate-900 text-lg"></i>
                                    </span>
                                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-500 rounded-full status-online"></div>
                                </div>
                                <div>
                                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">ekiliConvo</h1>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Video Conferencing</p>
                                </div>
                            </div>
                            <x-theme-toggle />
                        </div>

                        <!-- Search Bar -->
                        <div class="mb-8">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    placeholder="Search rooms..." 
                                    class="form-input pl-12 pr-4 py-3 w-full rounded-xl bg-white/70 dark:bg-slate-800/70 backdrop-blur-sm border-gray-200 dark:border-slate-600 focus:ring-2 focus:ring-brand-primary focus:border-transparent"
                                >
                                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-slate-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 space-y-2 custom-scrollbar overflow-y-auto">
                        <a href="#" class="nav-item active">
                            <div class="nav-icon-container flex items-center justify-center w-10 h-10 rounded-lg bg-brand-primary/20 dark:bg-brand-primary/30 transition-transform">
                                <i class="fas fa-home text-lg text-brand-dark dark:text-brand-primary"></i>
                            </div>
                            <div>
                                <span class="font-medium">Lobby</span>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Main dashboard</p>
                            </div>
                        </a>
                        
                        <a href="#" class="nav-item hover:bg-brand-primary/10 dark:hover:bg-brand-primary/20">
                            <div class="nav-icon-container flex items-center justify-center w-10 h-10 rounded-lg bg-white/20 dark:bg-slate-700/20 transition-transform">
                                <i class="fas fa-users text-lg text-gray-600 dark:text-slate-400 hover:text-brand-secondary"></i>
                            </div>
                            <div>
                                <span>Explore Rooms</span>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Browse available rooms</p>
                            </div>
                        </a>
                        
                        <a href="#" class="nav-item hover:bg-brand-primary/10 dark:hover:bg-brand-primary/20">
                            <div class="nav-icon-container flex items-center justify-center w-10 h-10 rounded-lg bg-white/20 dark:bg-slate-700/20 transition-transform">
                                <i class="fas fa-history text-lg text-gray-600 dark:text-slate-400 hover:text-brand-secondary"></i>
                            </div>
                            <div>
                                <span>Recent Calls</span>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Call history</p>
                            </div>
                        </a>
                        
                        <a href="#" class="nav-item hover:bg-brand-primary/10 dark:hover:bg-brand-primary/20">
                            <div class="nav-icon-container flex items-center justify-center w-10 h-10 rounded-lg bg-white/20 dark:bg-slate-700/20 transition-transform">
                                <i class="fas fa-cog text-lg text-gray-600 dark:text-slate-400 hover:text-brand-secondary"></i>
                            </div>
                            <div>
                                <span>Settings</span>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Preferences</p>
                            </div>
                        </a>
                    </nav>

                    <!-- CTA Section -->
                    <div class="flex-none mt-8">
                        <div class="modern-card p-6 bg-gradient-to-r from-brand-primary/10 to-brand-accent/10 border-brand-primary/20">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="flex items-center justify-center w-10 h-10 bg-brand-primary rounded-lg">
                                    <i class="fas fa-crown text-slate-900"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white">Try Premium!</h4>
                                    <p class="text-xs text-gray-600 dark:text-slate-400">Unlock advanced features</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-slate-400 mb-4">
                                Upgrade to access exclusive features like recording, unlimited room capacity, and more.
                            </p>
                            <button class="btn-primary w-full animate-pulse-soft">
                                <i class="fas fa-star mr-2"></i>
                                Upgrade Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-80 p-6 custom-scrollbar overflow-y-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome back! 👋</h2>
                        <p class="text-gray-600 dark:text-slate-400">Ready to start your next video conference?</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button class="btn-secondary">
                            <i class="fas fa-calendar mr-2"></i>
                            Schedule
                        </button>
                        <button class="btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            New Room
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Active Calls Card -->
                <div class="modern-card p-6 animate-slide-up">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl">
                                <i class="fas fa-phone-alt text-green-600 dark:text-green-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Active Calls</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Currently running</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-green-600 dark:text-green-400">4</span>
                    </div>
                    <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>2 new since yesterday</span>
                    </div>
                </div>

                <!-- Total Participants Card -->
                <div class="modern-card p-6 animate-slide-up" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                                <i class="fas fa-users text-blue-600 dark:text-blue-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Participants</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Total online</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">24</span>
                    </div>
                    <div class="flex items-center text-sm text-blue-600 dark:text-blue-400">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>8 active sessions</span>
                    </div>
                </div>

                <!-- Today's Sessions Card -->
                <div class="modern-card p-6 animate-slide-up" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                                <i class="fas fa-clock text-purple-600 dark:text-purple-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Today's Sessions</h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">Completed</p>
                            </div>
                        </div>
                        <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">12</span>
                    </div>
                    <div class="flex items-center text-sm text-purple-600 dark:text-purple-400">
                        <i class="fas fa-clock mr-1"></i>
                        <span>3.2 hrs total</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Room Overview -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Quick Start -->
                <div class="modern-card p-6 animate-slide-up" style="animation-delay: 0.3s">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="flex items-center justify-center w-12 h-12 bg-brand-primary rounded-xl">
                            <i class="fas fa-rocket text-slate-900 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Quick Start</h3>
                            <p class="text-gray-600 dark:text-slate-400">Create and join rooms instantly</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Room Name</label>
                            <input 
                                type="text" 
                                placeholder="Enter room name..." 
                                class="form-input w-full"
                                value="Team Standup"
                            >
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <button class="btn-secondary text-center">
                                <i class="fas fa-link mr-2"></i>
                                Join Room
                            </button>
                            <button class="btn-primary text-center">
                                <i class="fas fa-video mr-2"></i>
                                Start Call
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Room Overview -->
                <div class="modern-card p-6 animate-slide-up" style="animation-delay: 0.4s">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                                <i class="fas fa-door-open text-indigo-600 dark:text-indigo-400 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Room Overview</h3>
                                <p class="text-gray-600 dark:text-slate-400">Active conference rooms</p>
                            </div>
                        </div>
                        <button class="text-brand-primary hover:text-brand-secondary transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                            <div class="flex items-center space-x-3">
                                <div class="status-online"></div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Room 101</span>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">5 participants</p>
                                </div>
                            </div>
                            <button class="btn-secondary text-xs py-1 px-3">Join</button>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-200 dark:border-slate-700">
                            <div class="flex items-center space-x-3">
                                <div class="status-offline"></div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Room 202</span>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Empty</p>
                                </div>
                            </div>
                            <button class="btn-secondary text-xs py-1 px-3">Start</button>
                        </div>
                        
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-center space-x-3">
                                <div class="status-away"></div>
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">Room 303</span>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">2 participants</p>
                                </div>
                            </div>
                            <button class="btn-secondary text-xs py-1 px-3">Join</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="modern-card p-6 animate-slide-up" style="animation-delay: 0.5s">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-gray-100 dark:bg-slate-700 rounded-xl">
                            <i class="fas fa-history text-gray-600 dark:text-slate-400 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recent Activity</h3>
                            <p class="text-gray-600 dark:text-slate-400">Latest conference events</p>
                        </div>
                    </div>
                    <a href="#" class="text-brand-primary hover:text-brand-secondary text-sm font-medium transition-colors">
                        View All
                    </a>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg">
                        <div class="flex items-center justify-center w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full">
                            <i class="fas fa-video text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">Meeting "Daily Standup" started</p>
                            <p class="text-sm text-gray-500 dark:text-slate-400">5 minutes ago • Room 101</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg">
                        <div class="flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                            <i class="fas fa-user-plus text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">John Doe joined the meeting</p>
                            <p class="text-sm text-gray-500 dark:text-slate-400">12 minutes ago • Room 101</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg">
                        <div class="flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full">
                            <i class="fas fa-stop-circle text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white">Meeting "Project Review" ended</p>
                            <p class="text-sm text-gray-500 dark:text-slate-400">1 hour ago • Room 202</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Floating Action Button -->
    <button class="fab animate-float">
        <i class="fas fa-plus text-xl"></i>
    </button>

    <!-- Enhanced Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button button');
            const sidebar = document.querySelector('.sidebar-container');
            const overlay = document.querySelector('.mobile-overlay');
            
            function toggleMobileMenu() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
                document.body.classList.toggle('overflow-hidden');
            }
            
            mobileMenuButton?.addEventListener('click', toggleMobileMenu);
            overlay?.addEventListener('click', toggleMobileMenu);
            
            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
                    toggleMobileMenu();
                }
            });
            
            // Auto-close on larger screens
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
    </script>

</body>
</html>
