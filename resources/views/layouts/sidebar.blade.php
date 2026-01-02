<div
    class="flex flex-col w-64 h-screen px-0 py-0 bg-[#232f3e] border-r border-[#1a222e] fixed left-0 top-0 overflow-y-auto z-50 sidebar-scroll text-gray-300 shadow-xl">
    <div class="h-16 flex items-center px-5 bg-[#18222d] border-b border-[#0d131a] shadow-sm">
        <div class="flex items-center gap-3">
            <div
                class="w-8 h-8 bg-[#ec7211] rounded flex items-center justify-center text-white font-bold text-lg shadow-lg">
                B</div>
            <div>
                <h2 class="text-sm font-bold text-white tracking-wide uppercase leading-none">
                    {{ \App\Models\Setting::get('app_name') ?? config('app.name', 'BulkMail') }}
                </h2>
                <span class="text-[10px] text-gray-400 font-medium tracking-wider">ENTERPRISE</span>
            </div>
        </div>
    </div>

    <div class="px-0 py-4 flex flex-col justify-between flex-1">
        <nav class="space-y-0.5">
            <div class="px-4 mb-2 mt-2">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest pl-1">Core Services</span>
            </div>

            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-2 text-sm font-medium border-l-[3px] {{ request()->routeIs('dashboard') ? 'bg-[#344050] text-white border-[#ec7211]' : 'border-transparent text-gray-400 hover:text-gray-100 hover:bg-[#2c3a4a]' }} transition-all duration-150 group">
                <svg class="w-4 h-4 mr-3 {{ request()->routeIs('dashboard') ? 'text-[#ec7211]' : 'text-gray-500 group-hover:text-gray-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('campaigns.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium border-l-[3px] {{ request()->routeIs('campaigns*') ? 'bg-[#344050] text-white border-[#ec7211]' : 'border-transparent text-gray-400 hover:text-gray-100 hover:bg-[#2c3a4a]' }} transition-all duration-150 group">
                <svg class="w-4 h-4 mr-3 {{ request()->routeIs('campaigns*') ? 'text-[#ec7211]' : 'text-gray-500 group-hover:text-gray-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Campaigns</span>
            </a>

            <a href="{{ route('lists.index') }}"
                class="flex items-center px-4 py-2 text-sm font-medium border-l-[3px] {{ request()->routeIs('lists*') ? 'bg-[#344050] text-white border-[#ec7211]' : 'border-transparent text-gray-400 hover:text-gray-100 hover:bg-[#2c3a4a]' }} transition-all duration-150 group">
                <svg class="w-4 h-4 mr-3 {{ request()->routeIs('lists*') ? 'text-[#ec7211]' : 'text-gray-500 group-hover:text-gray-300' }} transition-colors"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Address Book</span>
            </a>

            @if(auth()->user()->isAdmin())
                <div class="px-4 mb-2 mt-6">
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest pl-1">Administration</span>
                </div>

                <a href="{{ route('departments.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium border-l-[3px] {{ request()->routeIs('departments*') ? 'bg-[#344050] text-white border-[#ec7211]' : 'border-transparent text-gray-400 hover:text-gray-100 hover:bg-[#2c3a4a]' }} transition-all duration-150 group">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('departments*') ? 'text-[#ec7211]' : 'text-gray-500 group-hover:text-gray-300' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Departments</span>
                </a>

                <a href="{{ route('users.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium border-l-[3px] {{ request()->routeIs('users.index') ? 'bg-[#344050] text-white border-[#ec7211]' : 'border-transparent text-gray-400 hover:text-gray-100 hover:bg-[#2c3a4a]' }} transition-all duration-150 group">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('users.index') ? 'text-[#ec7211]' : 'text-gray-500 group-hover:text-gray-300' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Users</span>
                </a>

                <a href="{{ route('settings.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium border-l-[3px] {{ request()->routeIs('settings.index') ? 'bg-[#344050] text-white border-[#ec7211]' : 'border-transparent text-gray-400 hover:text-gray-100 hover:bg-[#2c3a4a]' }} transition-all duration-150 group">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('settings.index') ? 'text-[#ec7211]' : 'text-gray-500 group-hover:text-gray-300' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Settings</span>
                </a>

                <a href="{{ route('senders.index') }}"
                    class="flex items-center px-4 py-2 text-sm font-medium border-l-[3px] {{ request()->routeIs('senders*') ? 'bg-[#344050] text-white border-[#ec7211]' : 'border-transparent text-gray-400 hover:text-gray-100 hover:bg-[#2c3a4a]' }} transition-all duration-150 group">
                    <svg class="w-4 h-4 mr-3 {{ request()->routeIs('senders*') ? 'text-[#ec7211]' : 'text-gray-500 group-hover:text-gray-300' }} transition-colors"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Senders</span>
                </a>
            @endif
        </nav>

        <div class="mt-8 pt-4 border-t border-[#344050] px-4">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="h-8 w-8 rounded-full bg-[#344050] flex items-center justify-center text-xs font-bold text-white border border-[#ec7211] overflow-hidden">
                    @if(Auth::user()->profile_photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                            class="h-full w-full object-cover">
                    @else
                        {{ substr(Auth::user()->name, 0, 2) }}
                    @endif
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-gray-200 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}"
                class="text-xs text-gray-400 hover:text-[#ec7211] block mb-2 transition-colors">Account Settings</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-gray-400 hover:text-red-400 transition-colors">Sign
                    Out</button>
            </form>
        </div>
    </div>
</div>