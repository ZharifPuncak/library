<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Library Portal - @yield('title', 'Home')</title>
    
    <!-- Google Fonts: Fredoka -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'lib-navy': '#003865',
                        'lib-blue': '#1e4972',
                        'lib-sky': '#387ec0',
                        'lib-light': '#f0f7ff',
                    },
                    fontFamily: {
                        'fredoka': ['Fredoka', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Scrollbar for a premium feel */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #387ec0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #1e4972; }
    </style>
</head>
<body class="bg-lib-light min-h-screen flex flex-col font-sans text-slate-800" x-data="{ mobileMenuOpen: false }">

    <!-- Navigation -->
    <header class="sticky top-0 z-50 shadow-lg">
        <!-- Top Bar: Logo & User Info -->
        <div class="bg-lib-navy text-white border-b border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 md:h-20">
                    <!-- Logo Section (Original Style) -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-4 group">
                            <img class="h-14 md:h-20 w-auto" src="{{ asset('images/library.png') }}" alt="Logo" onerror="this.style.display='none'">
                        </a>
                    </div>

                    <!-- User Area (Desktop) -->
                    <div class="hidden lg:flex items-center gap-6">
                        @guest
                            <a href="{{ route('login') }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-xs font-bold transition-all">LOGIN</a>
                        @else
                            <div class="flex items-center gap-3 pr-4 border-r border-white/20 text-right">
                                <div class="text-[10px] opacity-60 leading-none">Logged in as</div>
                                <div class="text-sm font-bold leading-tight">{{ Auth::user()->name }}</div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-red-300 hover:text-red-400 transition-colors uppercase tracking-wider" onclick="confirmLogout(event)">
                                    Logout
                                </button>
                            </form>
                        @endguest
                    </div>

                    <!-- Mobile menu button -->
                    <div class="lg:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 focus:outline-none transition-all">
                            <svg class="h-6 w-6" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="h-6 w-6" x-show="mobileMenuOpen" x-cloak fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Bar: Navigation Menu -->
        <div class="hidden lg:block bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8 h-14">
                    @php
                        $links = [
                            ['route' => 'home', 'label' => 'Home', 'icon' => '🏠'],
                            ['route' => 'assets.all', 'label' => 'All Media', 'icon' => '📂'],
                            ['route' => 'assets.photos', 'label' => 'Photos', 'icon' => '📸'],
                            ['route' => 'assets.videos', 'label' => 'Videos', 'icon' => '🎬'],
                            ['route' => 'assets.ebooks', 'label' => 'e-books', 'icon' => '📚'],
                            ['route' => 'collections.index', 'label' => 'Collections', 'icon' => '🖼️'],
                            ['route' => 'vr', 'label' => 'VR', 'icon' => '🥽'],
                        ];
                    @endphp
                    @foreach($links as $link)
                        <a href="{{ route($link['route']) }}" 
                           class="flex items-center gap-2 text-sm font-bold {{ request()->routeIs($link['route']) ? 'text-lib-sky border-b-2 border-lib-sky' : 'text-slate-600 hover:text-lib-navy' }} transition-colors h-full">
                            <span>{{ $link['icon'] }}</span> {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Mobile menu dropdown -->
        <div class="lg:hidden bg-lib-navy" x-show="mobileMenuOpen" x-cloak 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @foreach($links as $link)
                    <a href="{{ route($link['route']) }}" 
                       class="flex items-center gap-3 px-4 py-4 rounded-2xl text-base font-black uppercase tracking-widest {{ request()->routeIs($link['route']) ? 'bg-lib-sky text-white' : 'text-sky-100 hover:bg-white/5' }}">
                        <span>{{ $link['icon'] }}</span>
                        {{ $link['label'] }}
                    </a>
                @endforeach
            </div>
            <div class="pt-4 pb-6 border-t border-white/10">
                @guest
                    <div class="px-5">
                        <a href="{{ route('login') }}" class="block text-center bg-white text-lib-navy py-4 rounded-2xl font-black uppercase tracking-widest">Login</a>
                    </div>
                @else
                    <div class="flex items-center px-6 mb-6">
                        <div class="h-12 w-12 rounded-2xl bg-lib-sky flex items-center justify-center font-black text-white shadow-lg">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <div class="text-xs font-black text-sky-400 uppercase tracking-widest leading-none mb-1">User</div>
                            <div class="text-lg font-black text-white leading-none">{{ Auth::user()->name }}</div>
                        </div>
                    </div>
                    <div class="px-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-6 py-4 rounded-2xl text-base font-black text-red-400 hover:bg-red-500 hover:text-white transition-all uppercase tracking-widest" onclick="confirmLogout(event)">
                                term. session (_)
                            </button>
                        </form>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-lib-navy text-white py-6">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="text-sm opacity-60">
                &copy; {{ date('Y') }} Library Portal. All rights reserved.
            </div>
        </div>
    </footer>

    <script shadow-script>
        // Logout Confirmation
        function confirmLogout(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: 'Logout?',
                text: "Are you sure you want to exit?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0e3f70',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Stay',
                reverseButtons: true,
                background: '#f0f7ff',
                customClass: {
                    popup: 'rounded-2xl border-2 border-lib-sky shadow-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</body>
</html>
