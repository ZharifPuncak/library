@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    @if(session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-lg text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile summary card --}}
        <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-8">
            <h1 class="text-xl font-bold text-lib-navy mb-1">Your Profile</h1>
            <p class="text-sm text-slate-500 mb-6">Account information.</p>

            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-full bg-lib-light text-lib-navy flex items-center justify-center font-bold text-xl flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <div class="text-base font-bold text-slate-800 truncate">{{ $user->name }}</div>
                    <div class="text-xs text-slate-500 truncate">{{ $user->email }}</div>
                </div>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500 font-medium">Username</dt>
                    <dd class="text-slate-800 font-semibold truncate">{{ $user->username }}</dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500 font-medium">Role</dt>
                    <dd>
                        @if($user->isAdmin())
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-lib-navy text-white">Admin</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">User</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between gap-2">
                    <dt class="text-slate-500 font-medium">Member since</dt>
                    <dd class="text-slate-800 font-semibold">{{ $user->created_at?->format('M j, Y') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Password change card --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-slate-100 p-8">
            <h2 class="text-xl font-bold text-lib-navy mb-1">Change password</h2>
            <p class="text-sm text-slate-500 mb-6">Use a strong, unique password (at least 8 characters).</p>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-5">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @php
                $floatInput = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
                $floatLabel = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
            @endphp

            <form method="POST" action="{{ route('profile.password') }}" class="space-y-6" autocomplete="off">
                @csrf
                @method('PUT')

                <div class="relative">
                    <input id="current_password" type="password" name="current_password" required autocomplete="current-password" placeholder=" "
                           class="{{ $floatInput }}">
                    <label for="current_password" class="{{ $floatLabel }}">Current password</label>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="new-password" placeholder=" "
                               class="{{ $floatInput }}">
                        <label for="password" class="{{ $floatLabel }}">New password</label>
                    </div>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder=" "
                               class="{{ $floatInput }}">
                        <label for="password_confirmation" class="{{ $floatLabel }}">Confirm new password</label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                    <button type="submit"
                            class="px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md">
                        Update password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
