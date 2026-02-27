<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Library Portal</title>
    {{-- Google Fonts - Fredoka --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- Tailwind CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'lib-navy': '#003865',
                        'lib-sky': '#0ea5e9',
                        'lib-light': '#f0f9ff',
                    },
                    fontFamily: {
                        'fredoka': ['Fredoka', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Fredoka', sans-serif; }
    </style>
    
    {{-- SweetAlert2 & Validation --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function validateAuthForm(event) {
            const inputs = event.target.querySelectorAll('input[required]');
            let isValid = true;
            inputs.forEach(input => {
                if (!input.value.trim()) isValid = false;
            });
            
            if (!isValid) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please fill out all fields to proceed.',
                    confirmButtonColor: '#003865',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'rounded-[2rem] font-fredoka',
                        confirmButton: 'rounded-xl px-8 py-3 font-bold'
                    }
                });
            }
        }
    </script>
</head>
<body class="bg-[#f8fafc] min-h-screen flex items-center justify-center p-6 antialiased">
    <div class="max-w-md w-full relative">
        {{-- Decorative background blobs --}}
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-lib-sky/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-20 -right-20 w-64 h-64 bg-lib-navy/5 rounded-full blur-3xl animate-pulse"></div>

        <div class="relative bg-white rounded-[3.5rem] p-12 shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center mb-6">
                    <img src="{{ asset('images/library.png') }}" alt="Logo" class="w-48 h-auto object-contain">
                </div>
                <h1 class="text-4xl font-black text-lib-navy tracking-tight mb-3">Welcome <span class="text-lib-sky">Back.</span></h1>
                <p class="text-slate-400 font-medium italic">Please enter your details to login.</p>
            </div>

            {{-- Form --}}
            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-6" novalidate onsubmit="validateAuthForm(event)">
                @csrf
                
                {{-- Username --}}
                <div class="space-y-2">
                    <label for="username" class="text-[10px] font-black text-lib-navy uppercase tracking-widest pl-2">Username</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-lib-sky text-slate-300">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                            class="w-full pl-14 pr-6 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-lib-navy focus:bg-white transition-all outline-none text-lib-navy font-medium @error('username') border-red-200 bg-red-50 @enderror"
                            placeholder="Your username">
                    </div>
                    @error('username')
                        <p class="text-red-500 text-[10px] font-bold pl-2 uppercase tracking-wider">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between px-2">
                        <label for="password" class="text-[10px] font-black text-lib-navy uppercase tracking-widest">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[9px] font-black text-slate-400 hover:text-lib-sky uppercase tracking-widest transition-colors">Forgot?</a>
                        @endif
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none transition-colors group-focus-within:text-lib-sky text-slate-300">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" required
                            class="w-full pl-14 pr-6 py-4 rounded-2xl bg-slate-50 border-2 border-transparent focus:border-lib-navy focus:bg-white transition-all outline-none text-lib-navy font-medium @error('password') border-red-200 bg-red-50 @enderror"
                            placeholder="minimum 8 characters">
                    </div>
                    @error('password')
                        <p class="text-red-500 text-[10px] font-bold pl-2 uppercase tracking-wider">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center px-2">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-200 text-lib-navy focus:ring-lib-navy transition-all">
                        <span class="text-[10px] font-bold text-slate-400 group-hover:text-lib-navy uppercase tracking-widest transition-colors">Remember my access</span>
                    </label>
                </div>

                {{-- Login Button --}}
                <button type="submit" 
                    class="w-full bg-lib-navy hover:bg-lib-sky text-white py-5 rounded-[1.5rem] font-black text-sm uppercase tracking-[0.2em] shadow-xl shadow-lib-navy/20 transition-all hover:-translate-y-1 active:scale-95">
                    Login Now
                </button>
            </form>

            {{-- Footer Support --}}
            <div class="mt-12 pt-10 border-t border-slate-50 flex flex-col items-center gap-6">
                <div class="text-center">
                    <p class="text-[11px] font-bold text-slate-400 mb-4 uppercase tracking-wider">New to our portal?</p>
                    <a href="{{ route('admin.register') }}" 
                        class="inline-flex items-center justify-center px-8 py-3 rounded-full border-2 border-slate-100 text-lib-navy font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 hover:border-lib-navy/20 transition-all transform active:scale-95 leading-none">
                        Create Account
                    </a>
                </div>
                
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-[10px] font-black text-slate-300 hover:text-lib-navy uppercase tracking-[0.2em] transition-all group">
                    <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
        
        <p class="text-center mt-10 text-[10px] font-black text-slate-300 uppercase tracking-widest">
            &copy; {{ date('Y') }} Library Portal System
        </p>
    </div>
</body>
</html>
