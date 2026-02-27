<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - @yield('title', 'Dashboard')</title>
    
    <!-- Google Fonts: Fredoka -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'lib-navy': '#003865',
                        'lib-sky': '#75b6df',
                        'lib-blue': '#1d6a99',
                        'lib-light': '#f0f7ff',
                    },
                    fontFamily: {
                        'fredoka': ['Fredoka', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    @livewireStyles
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; }
        
        /* Custom SweetAlert Styles */
        .swal2-popup.swal-premium-popup {
            border-radius: 20px !important;
            padding: 2.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }
        .swal2-title {
            color: #1e4972 !important;
            font-weight: 700 !important;
            font-size: 1.5rem !important;
        }
        .swal2-confirm {
            padding: 0.75rem 2.5rem !important;
            border-radius: 10px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
            transition: all 0.3s !important;
        }
        .swal-premium-confirm {
            background: linear-gradient(135deg, #1d6a99 0%, #138496 100%) !important;
            color: white !important;
        }
        
        /* Main Content */
        .admin-content { 
            padding: 0; 
            min-height: calc(100vh - 100px);
        }
        
        /* Footer */
        .admin-footer {
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Flash Message Popup with SweetAlert2 -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session()->has('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: @json(session('success')),
                    confirmButtonText: 'Great!',
                    showConfirmButton: true,
                    timer: 4000,
                    timerProgressBar: true,
                    background: '#fff',
                    iconColor: '#1d6a99',
                    customClass: {
                        popup: 'swal-premium-popup',
                        confirmButton: 'swal-premium-confirm'
                    },
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown'
                    }
                });
            @endif

            @if (session()->has('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: @json(session('error')),
                    confirmButtonText: 'Try Again',
                    showConfirmButton: true,
                    background: '#fff',
                    iconColor: '#dc3545',
                    customClass: {
                        popup: 'swal-premium-popup'
                    },
                    showClass: {
                        popup: 'animate__animated animate__headShake'
                    }
                });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    html: '<ul class="text-left text-sm space-y-1">@foreach ($errors->all() as $error)<li>⚠️ {{ $error }}</li>@endforeach</ul>',
                    confirmButtonText: 'Okay',
                    showConfirmButton: true,
                    background: '#fff',
                    iconColor: '#f59e0b',
                    customClass: {
                        popup: 'swal-premium-popup rounded-3xl',
                        confirmButton: 'bg-lib-navy text-white px-6 py-2 rounded-xl'
                    },
                    showClass: {
                        popup: 'animate__animated animate__headShake'
                    }
                });
            @endif

            // Global Delete Confirmation
            document.addEventListener('click', function(e) {
                if (e.target && (e.target.classList.contains('delete-confirm') || e.target.closest('.delete-confirm'))) {
                    e.preventDefault();
                    const button = e.target.classList.contains('delete-confirm') ? e.target : e.target.closest('.delete-confirm');
                    const message = button.getAttribute('data-message') || 'Are you sure you want to delete this?';
                    const form = button.closest('form');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, Delete it!',
                        cancelButtonText: 'No, Keep it',
                        reverseButtons: true,
                        customClass: {
                            popup: 'swal-premium-popup'
                        },
                        showClass: {
                            popup: 'animate__animated animate__pulse'
                        }
                    }).then((result) => {
                        if (result.isConfirmed && form) {
                            form.submit();
                        }
                    });
                }
            });
        });
        // Logout Confirmation
        function confirmLogout(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: 'Logout?',
                text: "Are you sure you want to exit the admin panel?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1d6a99',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Logout',
                cancelButtonText: 'Stay here',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    {{-- Modern Admin Top Bar (matching user layout) --}}
    <header class="bg-white shadow-lg sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
        {{-- Top Level: Logo & User Info --}}
        <div class="bg-lib-navy text-white border-b border-white/5">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16 md:h-20">
                    {{-- Logo Section --}}
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 group">
                            <img class="h-14 md:h-20 w-auto" src="{{ asset('images/library.png') }}" alt="Logo" onerror="this.style.display='none'">
                        </a>
                    </div>

                    {{-- User Area (Desktop) --}}
                    <div class="hidden lg:flex items-center gap-6">
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
                    </div>

                    {{-- Mobile Menu Button --}}
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-md text-white hover:bg-white/10 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Second Level: Navigation Links (Desktop) --}}
        <div class="hidden lg:block bg-white border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-8 h-14">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-2 text-sm font-bold {{ request()->routeIs('admin.dashboard') ? 'text-lib-sky border-b-2 border-lib-sky' : 'text-slate-600 hover:text-lib-navy' }} transition-colors h-full">
                        <span>🏠</span> Home
                    </a>
                    <a href="{{ route('admin.assets.all') }}" 
                       class="flex items-center gap-2 text-sm font-bold {{ request()->routeIs('admin.assets.all') ? 'text-lib-sky border-b-2 border-lib-sky' : 'text-slate-600 hover:text-lib-navy' }} transition-colors h-full">
                        <span>📂</span> All Media
                    </a>
                    <a href="{{ route('admin.assets.photos') }}" 
                       class="flex items-center gap-2 text-sm font-bold {{ request()->routeIs('admin.assets.photos') ? 'text-lib-sky border-b-2 border-lib-sky' : 'text-slate-600 hover:text-lib-navy' }} transition-colors h-full">
                        <span>📸</span> Photos
                    </a>
                    <a href="{{ route('admin.assets.videos') }}" 
                       class="flex items-center gap-2 text-sm font-bold {{ request()->routeIs('admin.assets.videos') ? 'text-lib-sky border-b-2 border-lib-sky' : 'text-slate-600 hover:text-lib-navy' }} transition-colors h-full">
                        <span>🎬</span> Videos
                    </a>
                    <a href="{{ route('admin.assets.ebooks') }}" 
                       class="flex items-center gap-2 text-sm font-bold {{ request()->routeIs('admin.assets.ebooks') ? 'text-lib-sky border-b-2 border-lib-sky' : 'text-slate-600 hover:text-lib-navy' }} transition-colors h-full">
                        <span>📚</span> e-books
                    </a>
                    <a href="{{ route('admin.collections.index') }}" 
                       class="flex items-center gap-2 text-sm font-bold {{ request()->routeIs('admin.collections.*') ? 'text-lib-sky border-b-2 border-lib-sky' : 'text-slate-600 hover:text-lib-navy' }} transition-colors h-full">
                        <span>🖼️</span> Collections
                    </a>
                    <a href="{{ route('home') }}" target="_blank"
                       class="flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-lib-navy transition-colors h-full ml-auto">
                        <span>🌐</span> View Site
                    </a>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenuOpen" 
             x-cloak
             @click.away="mobileMenuOpen = false"
             class="lg:hidden bg-white border-b border-slate-200 shadow-lg">
            <div class="px-4 py-6 space-y-4">
                {{-- User Info (Mobile) --}}
                <div class="pb-4 border-b border-slate-200">
                    <div class="text-sm text-slate-500 mb-1">Logged in as</div>
                    <div class="text-lg font-bold text-lib-navy">{{ Auth::user()->name }}</div>
                </div>

                {{-- Navigation Links (Mobile) --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-lib-light text-lib-navy' : 'text-slate-600 hover:bg-slate-50' }} font-bold transition-colors">
                    <span class="text-xl">🏠</span> Home
                </a>
                <a href="{{ route('admin.assets.all') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.assets.all') ? 'bg-lib-light text-lib-navy' : 'text-slate-600 hover:bg-slate-50' }} font-bold transition-colors">
                    <span class="text-xl">📂</span> All Media
                </a>
                <a href="{{ route('admin.assets.photos') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.assets.photos') ? 'bg-lib-light text-lib-navy' : 'text-slate-600 hover:bg-slate-50' }} font-bold transition-colors">
                    <span class="text-xl">📸</span> Photos
                </a>
                <a href="{{ route('admin.assets.videos') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.assets.videos') ? 'bg-lib-light text-lib-navy' : 'text-slate-600 hover:bg-slate-50' }} font-bold transition-colors">
                    <span class="text-xl">🎬</span> Videos
                </a>
                <a href="{{ route('admin.assets.ebooks') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.assets.ebooks') ? 'bg-lib-light text-lib-navy' : 'text-slate-600 hover:bg-slate-50' }} font-bold transition-colors">
                    <span class="text-xl">📚</span> e-books
                </a>
                <a href="{{ route('admin.collections.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.collections.*') ? 'bg-lib-light text-lib-navy' : 'text-slate-600 hover:bg-slate-50' }} font-bold transition-colors">
                    <span class="text-xl">🖼️</span> Collections
                </a>
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-600 hover:bg-slate-50 font-bold transition-colors">
                    <span class="text-xl">🌐</span> View Site
                </a>

                {{-- Logout (Mobile) --}}
                <div class="pt-4 border-t border-slate-200">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 font-bold transition-colors" onclick="confirmLogout(event)">
                            <span class="text-xl">🚪</span> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-content">
        @yield('content')
    </main>

    <footer class="bg-lib-navy text-white py-8 mt-12 admin-footer">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm font-bold opacity-80 uppercase tracking-widest">&copy; {{ date('Y') }} Library Portal. All rights reserved.</p>
        </div>
    </footer>
    
    @livewireScripts
</body>
</html>