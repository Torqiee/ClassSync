<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ClassSync') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 bg-[#FAFAFA]">
    
    <div x-data="{ sidebarOpen: window.innerWidth >= 768 }" class="flex min-h-screen relative overflow-x-hidden">

        <div class="md:hidden fixed top-0 left-0 right-0 h-16 bg-white/80 backdrop-blur-md border-b border-gray-200 z-30 flex items-center justify-between px-4 shadow-sm">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-900 focus:outline-none p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <span class="text-lg font-bold tracking-tight text-gray-900">ClassSync</span>
            </div>
            <a href="{{ route('profile.edit') }}" class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-100 to-blue-50 flex items-center justify-center text-indigo-700 font-bold text-xs shadow-sm ring-2 ring-white">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </a>
        </div>

        <button 
            x-show="!sidebarOpen"
            x-transition:enter="transition ease-out duration-300 delay-150"
            x-transition:enter-start="opacity-0 -translate-x-8"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 -translate-x-8"
            @click="sidebarOpen = true" 
            class="hidden md:flex fixed top-8 left-6 z-40 w-11 h-11 bg-white border border-gray-200 text-gray-500 rounded-xl items-center justify-center shadow-sm hover:bg-gray-50 hover:text-gray-900 hover:shadow transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500"
            style="display: none;"
            title="Buka Menu"
        >
            <i class="fa-solid fa-bars text-lg"></i>
        </button>

        <div 
            x-show="sidebarOpen" 
            x-transition.opacity.duration.300ms
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm z-40 md:hidden"
            style="display: none;"
        ></div>

        <aside 
            class="fixed inset-y-0 left-0 bg-white border-r border-gray-200 w-64 flex flex-col z-50 shadow-[4px_0_24px_rgba(0,0,0,0.02)] transition-transform duration-300 ease-in-out"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex items-center justify-between h-20 px-6 border-b border-gray-100/80">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-calendar text-md"></i>
                    <span class="text-xl font-bold tracking-tight text-gray-900">ClassSync</span>
                </div>
                
                <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-gray-900 p-2 rounded-xl hover:bg-gray-50 transition-colors focus:outline-none">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-4 px-3">Menu Utama</div>
                
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50/80 text-indigo-700 ring-1 ring-inset ring-indigo-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all group">
                    <i class="fa-solid fa-house w-5 text-center mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }} transition-colors"></i>
                    Dashboard
                </a>
                
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.index') ? 'bg-indigo-50/80 text-indigo-700 ring-1 ring-inset ring-indigo-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all group mt-1">
                    <i class="fa-solid fa-sliders w-5 text-center mr-3 {{ request()->routeIs('settings.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }} transition-colors"></i>
                    Preferensi AI
                </a>

                <a href="{{ route('activities.history') }}" class="{{ request()->routeIs('activities.history') ? 'bg-indigo-50/80 text-indigo-700 ring-1 ring-inset ring-indigo-100/50' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }} flex items-center px-3.5 py-2.5 text-sm font-medium rounded-xl transition-all group mt-1">
                    <i class="fa-solid fa-clock-rotate-left w-5 text-center mr-3 {{ request()->routeIs('activities.history') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-600' }} transition-colors"></i>
                    Riwayat Kegiatan
                </a>
            </div>

            <div class="p-4 border-t border-gray-100/80 bg-gray-50/50">
                <div class="flex items-center justify-between p-2 hover:bg-white transition-colors rounded-xl group border border-transparent hover:border-gray-200 hover:shadow-sm">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 overflow-hidden flex-1 cursor-pointer">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-100 to-blue-50 flex items-center justify-center text-indigo-700 font-bold text-sm shrink-0 ring-2 ring-white shadow-sm">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition-colors">{{ Auth::user()->name ?? 'Toriq Learning' }}</p>
                            <p class="text-[11px] font-medium text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@classsync.app' }}</p>
                        </div>
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0 ml-2">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-50 transition-colors focus:outline-none" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main 
            class="flex-1 min-w-0 min-h-screen transition-all duration-300 ease-in-out pt-16 md:pt-0"
            :class="sidebarOpen ? 'md:ml-64 ml-0' : 'ml-0'"
        >
            <div :class="!sidebarOpen ? 'md:pl-24' : ''" class="transition-all duration-300 min-w-0">
                {{ $slot }}
            </div>
        </main>

    </div>
</body>
</html>