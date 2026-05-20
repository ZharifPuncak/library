<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - E-Library</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'lib-navy': '#003865',
                        'lib-blue': '#1d6a99',
                        'lib-sky':  '#0ea5e9',
                        'lib-light': '#f0f7ff',
                    },
                    fontFamily: { 'sans': ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-6 bg-lib-light">

    <div class="w-full max-w-sm">

        {{-- Logo above card --}}
        <div class="flex justify-center -mb-12 relative z-10">
            <div class="bg-white rounded-full p-3 shadow-sm border border-slate-100">
                <img src="{{ asset('images/logo.png') }}" alt="Puncak Niaga"
                     class="h-20 w-20 object-contain"
                     onerror="this.style.display='none'">
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 pt-16 pb-8 px-8">

            <h1 class="text-center text-xl font-semibold text-slate-800 mb-6">E-Library Login</h1>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                {{-- Username --}}
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                           autocomplete="off"
                           autocorrect="off"
                           autocapitalize="off"
                           spellcheck="false"
                           class="w-full px-3 py-2.5 rounded-md border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-lib-blue focus:border-lib-blue transition-colors @error('username') border-red-400 @enderror">
                    @error('username')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <div class="relative">
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required
                               autocomplete="current-password"
                               class="w-full pr-10 px-3 py-2.5 rounded-md border border-slate-300 text-slate-800 focus:outline-none focus:ring-2 focus:ring-lib-blue focus:border-lib-blue transition-colors @error('password') border-red-400 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 px-3 text-slate-400 hover:text-lib-blue transition-colors"
                                :aria-label="show ? 'Hide password' : 'Show password'">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Login button --}}
                <button type="submit"
                        class="w-full px-4 py-2 bg-lib-blue hover:bg-lib-navy text-white text-xs font-bold rounded-md transition-colors mt-2">
                    Login
                </button>

                {{-- Forgot --}}
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                       class="block w-full px-4 py-2 text-center border border-slate-300 hover:border-lib-blue hover:text-lib-blue text-slate-600 text-xs font-bold rounded-md transition-colors">
                        Forgot Password
                    </a>
                @endif
            </form>
        </div>

        <div class="mt-5 text-center">
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-1.5 text-slate-500 hover:text-lib-navy text-xs font-semibold transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                Return to home
            </a>
        </div>

        <p class="text-center text-slate-400 text-xs mt-6">
            &copy; {{ date('Y') }} ICTD
        </p>
    </div>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
