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
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 md:h-20">
                    <!-- Logo Section (Original Style) -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center gap-4 group">
                            <img class="h-14 md:h-20 w-auto" src="{{ asset('images/library.png') }}" alt="Logo" onerror="this.style.display='none'">
                        </a>
                    </div>

                    <!-- User Area -->
                    <div class="flex items-center gap-4">
                        @guest
                            <a href="{{ route('login') }}" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-lg text-xs font-bold transition-all">LOGIN</a>
                        @else
                            <div class="relative" x-data="{ userMenuOpen: false }" @click.outside="userMenuOpen = false" @keydown.escape.window="userMenuOpen = false">
                                <button type="button" @click="userMenuOpen = !userMenuOpen"
                                        class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-white/10 transition-all">
                                    <span class="text-sm font-bold leading-tight">{{ Auth::user()->name }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                         class="h-4 w-4 transition-transform"
                                         :class="userMenuOpen ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <div x-show="userMenuOpen" x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-1"
                                     class="absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
                                    <div class="px-4 py-3 border-b border-slate-100">
                                        <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Signed in as</div>
                                        <div class="text-sm font-bold text-lib-navy truncate">{{ Auth::user()->name }}</div>
                                    </div>
                                    <a href="{{ route('profile.edit') }}"
                                       class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-lib-navy transition-colors border-b border-slate-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" onclick="confirmLogout(event)"
                                                class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-red-500 hover:bg-red-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </div>

    </header>

    <!-- Page Nav Pills: Media / Users (admin only, sticky below header — hidden on home) -->
    @auth
        @if(Auth::user()->isAdmin() && !request()->routeIs('home') && !request()->routeIs('media.show') && !request()->routeIs('collections.*'))
            @php
                $mediaActive      = request()->routeIs('media.*') || request()->routeIs('collections.*');
                $usersActive      = request()->routeIs('users.*');
                $slideshowActive  = request()->routeIs('slideshow.*');
                $categoriesActive = request()->routeIs('categories.*');
                $tagsActive       = request()->routeIs('tags.*');
            @endphp
            @php
                $navLinks = [
                    ['label' => 'Media',      'route' => route('media.index'),      'active' => $mediaActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'],
                    ['label' => 'Users',      'route' => route('users.index'),      'active' => $usersActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'],
                    ['label' => 'Slideshow',  'route' => route('slideshow.index'),  'active' => $slideshowActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>'],
                    ['label' => 'Categories', 'route' => route('categories.index'), 'active' => $categoriesActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'],
                    ['label' => 'Tags',       'route' => route('tags.index'),       'active' => $tagsActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>'],
                ];
                $activeNav = collect($navLinks)->firstWhere('active', true);
            @endphp

            <div class="sticky top-16 md:top-20 z-40 bg-slate-50/90 backdrop-blur-md border-b border-slate-200/60">
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                    {{-- Desktop: full pill row --}}
                    <nav class="hidden md:flex justify-start items-center gap-2 h-14">
                        @foreach($navLinks as $link)
                            <a href="{{ $link['route'] }}"
                               class="inline-flex items-center gap-2 px-5 py-2 rounded-full text-sm font-bold transition-colors {{ $link['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : 'bg-white text-slate-600 border border-slate-200 hover:border-lib-navy hover:text-lib-navy' }}">
                                {!! $link['icon'] !!}
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    {{-- Mobile: right-aligned Actions dropdown --}}
                    <div class="md:hidden flex justify-end items-center h-14 relative"
                         x-data="{ actionsOpen: false }"
                         @click.outside="actionsOpen = false"
                         @keydown.escape.window="actionsOpen = false">
                        <button type="button" @click="actionsOpen = !actionsOpen"
                                class="inline-flex items-center gap-1.5 text-sm font-bold text-lib-navy hover:text-lib-sky transition-colors">
                            <span>{{ $activeNav['label'] ?? 'Actions' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 class="h-4 w-4 transition-transform"
                                 :class="actionsOpen ? 'rotate-180' : ''">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="actionsOpen" x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute right-0 top-full mt-1 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden z-50">
                            @foreach($navLinks as $link)
                                <a href="{{ $link['route'] }}"
                                   class="flex items-center gap-3 px-4 py-3 text-sm font-semibold border-b border-slate-100 last:border-b-0 transition-colors {{ $link['active'] ? 'bg-lib-light text-lib-navy' : 'text-slate-600 hover:bg-slate-50 hover:text-lib-navy' }}">
                                    {!! $link['icon'] !!}
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

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
