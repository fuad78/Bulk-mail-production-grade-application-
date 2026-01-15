<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ \App\Models\Setting::get('app_name') ?? config('app.name', 'Bulk Email System') }}</title>
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="h-full bg-gray-50" x-data="{ 
    sidebarOpen: false, 
    desktopSidebarOpen: localStorage.getItem('desktopSidebarOpen') === 'false' ? false : true 
}" x-init="$watch('desktopSidebarOpen', value => localStorage.setItem('desktopSidebarOpen', value))">

    <!-- Mobile Header -->
    <div class="lg:hidden flex items-center justify-between bg-[#18222d] px-4 py-3 shadow-md fixed w-full top-0 z-40">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span
                class="text-white font-bold text-lg">{{ \App\Models\Setting::get('app_name') ?? config('app.name', 'BulkMail') }}</span>
        </div>
    </div>

    @include('layouts.sidebar')

    <!-- Main Content Wrapper -->
    <div :class="desktopSidebarOpen ? 'lg:ml-64' : 'lg:ml-0'"
        class="min-h-screen pt-16 lg:pt-0 transition-all duration-300">
        @if (isset($header))
            <header class="bg-white shadow-sm border-b border-gray-100">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex items-center">
                    <!-- Desktop Sidebar Toggle -->
                    <button @click="desktopSidebarOpen = true" x-show="!desktopSidebarOpen" x-cloak
                        class="hidden lg:flex items-center gap-2 mr-4 bg-[#111] text-white px-4 py-2 hover:bg-[#444] transition-colors rounded-sm shadow-sm"
                        title="Open Sidebar">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span class="text-sm font-semibold tracking-wide">Open Sidebar</span>
                    </button>

                    <div class="flex-1">
                        {{ $header }}
                    </div>
                </div>
            </header>
        @endif

        <main class="py-8 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>
</body>

</html>