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
            <div class="w-full px-4 sm:px-6 lg:px-8">
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
                                     class="absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded-lg shadow-lg border border-slate-100 overflow-hidden">
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

    @if(session('status') || session('success') || session('error'))
        @php
            $flashType = session('error') ? 'error' : (session('success') ? 'success' : 'status');
            $flashMessage = session('error') ?? session('success') ?? session('status');
            $flashClass = $flashType === 'error'
                ? 'bg-red-50 border-red-200 text-red-700'
                : 'bg-emerald-50 border-emerald-200 text-emerald-700';
        @endphp
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3500)"
             x-show="show"
             x-transition.opacity
             x-cloak
             class="fixed right-4 top-20 md:top-24 z-[70] w-[min(calc(100vw-2rem),24rem)]">
            <div role="alert" class="rounded-lg border bg-white px-4 py-3 text-sm font-semibold shadow-md {{ $flashClass }}">
                <div class="flex items-start justify-between gap-3">
                    <span>{{ $flashMessage }}</span>
                    <button type="button" @click="show = false"
                            class="-mr-1 rounded p-0.5 text-current opacity-60 hover:opacity-100 transition-opacity"
                            aria-label="Dismiss notification">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Left sidenav for authenticated users (admin items + personal items) -->
    @auth
        @if(!request()->routeIs('home'))
            @php
                $isAdminUser = Auth::user()->isAdmin();
                $mediaActive      = request()->routeIs('media.*');
                $usersActive      = request()->routeIs('users.*');
                $sliderActive     = request()->routeIs('slider.*');
                $categoriesActive = request()->routeIs('categories.*');
                $tagsActive       = request()->routeIs('tags.*');
                $mylistActive     = request()->routeIs('mylist.*');

                $adminLinks = [
                    ['label' => 'Collection', 'route' => route('media.index'),      'active' => $mediaActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'],
                    ['label' => 'Users',      'route' => route('users.index'),      'active' => $usersActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'],
                    ['label' => 'Slider',     'route' => route('slider.index'),     'active' => $sliderActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>'],
                    ['label' => 'Categories', 'route' => route('categories.index'), 'active' => $categoriesActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>'],
                    ['label' => 'Tags',       'route' => route('tags.index'),       'active' => $tagsActive,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>'],
                ];
                $personalLinks = [
                    ['label' => 'My List', 'route' => route('mylist.index'), 'active' => $mylistActive, 'external' => false,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>'],
                    ['label' => 'VR Tour', 'route' => route('vr'),           'active' => false,         'external' => true,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'],
                ];
                $userOnlyLinks = [
                    ['label' => 'Collection', 'route' => route('media.index'), 'active' => $mediaActive, 'external' => false,
                     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>'],
                ];
                $navLinks = $isAdminUser
                    ? array_merge($adminLinks, $personalLinks)
                    : array_merge($userOnlyLinks, $personalLinks);
                $activeNav = collect($navLinks)->firstWhere('active', true);
            @endphp

            <div class="flex-grow flex flex-col"
                 x-data="{
                    collapsed: true,
                    sidebarHovered: false,
                    get sidebarExpanded() {
                        return !this.collapsed || this.sidebarHovered;
                    },
                    mobileOpen: false
                  }">

                {{-- Mobile top strip: hamburger on the left --}}
                <div class="md:hidden sticky top-16 md:top-20 z-40 bg-slate-50/90 backdrop-blur-md border-b border-slate-200/60">
                    <div class="max-w-[1400px] mx-auto px-4 sm:px-6">
                        <div class="flex justify-start items-center h-14">
                            <button type="button" @click="mobileOpen = true"
                                    class="inline-flex items-center gap-1.5 text-sm font-bold text-lib-navy hover:text-lib-sky transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                {{ $activeNav['label'] ?? 'Menu' }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Mobile offcanvas --}}
                <div x-show="mobileOpen" x-cloak class="md:hidden fixed inset-0 z-[60]"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100">
                    <div class="absolute inset-0 bg-slate-900/40" @click="mobileOpen = false"></div>
                    <aside class="absolute left-0 top-0 bottom-0 w-64 bg-white shadow-lg flex flex-col"
                           x-transition:enter="transition ease-out duration-200"
                           x-transition:enter-start="-translate-x-full"
                           x-transition:enter-end="translate-x-0">
                        <div class="flex items-center justify-between px-4 h-16 border-b border-slate-100">
                            <span class="text-sm font-bold text-lib-navy">{{ $isAdminUser ? 'Admin' : 'Menu' }}</span>
                            <button type="button" @click="mobileOpen = false"
                                    class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-lib-navy transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                        <nav class="flex-1 p-3 space-y-1 overflow-y-auto">
                            @if($isAdminUser)
                                @foreach($adminLinks as $link)
                                    <a href="{{ $link['route'] }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $link['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : 'text-slate-600 hover:bg-slate-50 hover:text-lib-navy' }}">
                                        {!! $link['icon'] !!}
                                        <span>{{ $link['label'] }}</span>
                                    </a>
                                @endforeach
                                <hr class="my-3 border-slate-200">
                            @else
                                @foreach($userOnlyLinks as $link)
                                    <a href="{{ $link['route'] }}"
                                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $link['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : 'text-slate-600 hover:bg-slate-50 hover:text-lib-navy' }}">
                                        {!! $link['icon'] !!}
                                        <span>{{ $link['label'] }}</span>
                                    </a>
                                @endforeach
                                <hr class="my-3 border-slate-200">
                            @endif
                            @foreach($personalLinks as $link)
                                <a href="{{ $link['route'] }}"
                                   @if(!empty($link['external'])) target="_blank" rel="noopener noreferrer" @endif
                                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $link['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : (!empty($link['external']) ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-slate-600 hover:bg-slate-50 hover:text-lib-navy') }}">
                                    {!! $link['icon'] !!}
                                    <span>{{ $link['label'] }}</span>
                                    @if(!empty($link['external']))
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="ml-auto h-3.5 w-3.5 opacity-70"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    @endif
                                </a>
                            @endforeach
                        </nav>
                    </aside>
                </div>

                {{-- Desktop fixed sidebar --}}
                <aside class="hidden md:flex flex-col fixed left-0 top-16 md:top-20 bottom-0 z-30 bg-white border-r border-slate-200 transition-all duration-200"
                       @mouseenter="sidebarHovered = true"
                       @mouseleave="sidebarHovered = false"
                       :class="sidebarExpanded ? 'w-56 shadow-xl shadow-slate-900/5' : 'w-16'">
                    <nav class="flex-1 px-2 pt-4 space-y-1.5 overflow-y-auto">
                        @if($isAdminUser)
                            @foreach($adminLinks as $link)
                                <a href="{{ $link['route'] }}"
                                   class="flex items-center gap-3 rounded-xl text-sm font-semibold transition-colors {{ $link['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : 'text-slate-600 hover:bg-slate-50 hover:text-lib-navy' }}"
                                   :class="sidebarExpanded ? 'px-3 py-2.5' : 'justify-center p-2.5'"
                                   :title="@js($link['label'])">
                                    {!! $link['icon'] !!}
                                    <span x-show="sidebarExpanded" x-transition.opacity>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                            <hr class="my-3 border-slate-200" :class="sidebarExpanded ? 'mx-1' : 'mx-2'">
                        @else
                            @foreach($userOnlyLinks as $link)
                                <a href="{{ $link['route'] }}"
                                   class="flex items-center gap-3 rounded-xl text-sm font-semibold transition-colors {{ $link['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : 'text-slate-600 hover:bg-slate-50 hover:text-lib-navy' }}"
                                   :class="sidebarExpanded ? 'px-3 py-2.5' : 'justify-center p-2.5'"
                                   :title="@js($link['label'])">
                                    {!! $link['icon'] !!}
                                    <span x-show="sidebarExpanded" x-transition.opacity>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                            <hr class="my-3 border-slate-200" :class="sidebarExpanded ? 'mx-1' : 'mx-2'">
                        @endif
                        @foreach($personalLinks as $link)
                            <a href="{{ $link['route'] }}"
                               @if(!empty($link['external'])) target="_blank" rel="noopener noreferrer" @endif
                               class="flex items-center gap-3 rounded-xl text-sm font-semibold transition-colors {{ $link['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : (!empty($link['external']) ? 'text-amber-600 bg-amber-50 hover:bg-amber-100' : 'text-slate-600 hover:bg-slate-50 hover:text-lib-navy') }}"
                               :class="sidebarExpanded ? 'px-3 py-2.5' : 'justify-center p-2.5'"
                               :title="@js($link['label'])">
                                {!! $link['icon'] !!}
                                <span x-show="sidebarExpanded" x-transition.opacity>{{ $link['label'] }}</span>
                                @if(!empty($link['external']))
                                    <svg x-show="sidebarExpanded" x-transition.opacity xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="ml-auto h-3.5 w-3.5 opacity-70"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                @endif
                            </a>
                        @endforeach
                    </nav>
                </aside>

                {{-- Main content: shifts right on md+ to make room for the sidebar --}}
                <main class="flex-grow flex flex-col transition-all duration-200"
                      :class="{ 'md:pl-56': !collapsed, 'md:pl-16': collapsed }">
                    <div class="flex-grow">
                        @yield('content')
                    </div>
                </main>
            </div>
        @else
            <main class="flex-grow">@yield('content')</main>
        @endif
    @else
        <main class="flex-grow">@yield('content')</main>
    @endauth

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
                    popup: 'rounded-lg border-2 border-lib-sky shadow-lg'
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
