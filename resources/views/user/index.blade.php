@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    @if(session('status'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded-lg text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-2 rounded-lg text-xs font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div>
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-[11px] font-bold uppercase text-lib-sky mb-3">
            <span class="w-1.5 h-1.5 rounded-full bg-lib-sky"></span>
            Access control
        </div>
        <h1 class="text-2xl font-bold text-lib-navy leading-tight">Users</h1>
        <p class="text-sm text-slate-500 mt-1 max-w-2xl">Manage staff accounts, roles, and library access.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- ============ LEFT: STATS CARD (4 cols) ============ --}}
        <aside class="lg:col-span-4">
            <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6 space-y-5 lg:sticky lg:top-8">
                <div>
                    <h2 class="text-sm font-bold text-lib-navy">Overview</h2>
                    <p class="text-xs text-slate-500 mt-0.5">A snapshot of your user base.</p>
                </div>

                <div class="rounded-lg bg-lib-navy text-white p-5">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-white/70">Total Users</p>
                    <p class="text-3xl font-bold mt-1">{{ $stats['total'] }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Admins</p>
                        <p class="text-2xl font-bold text-lib-navy mt-1">{{ $stats['admins'] }}</p>
                    </div>
                    <div class="rounded-lg border border-slate-100 bg-slate-50 p-4">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Members</p>
                        <p class="text-2xl font-bold text-lib-navy mt-1">{{ $stats['users'] }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ============ RIGHT: USER LIST (8 cols) ============ --}}
        <div class="lg:col-span-8 bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">

        <div class="flex flex-col gap-4 p-6 border-b border-slate-100">
            <div class="flex justify-end">
                <a href="{{ route('users.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add User
                </a>
            </div>

            @php
                $clearUrl = route('users.index');
            @endphp
            <form method="GET" action="{{ route('users.index') }}"
                  x-data="{ query: @js((string) ($search ?? '')), hadSearch: @js(!empty($search)) }"
                  x-init="$watch('query', value => {
                      if (hadSearch && value.trim() === '') {
                          window.location.href = @js($clearUrl);
                      }
                  })">
                <div class="relative flex items-center gap-2">
                    <div class="relative flex-grow">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input type="search" name="search" x-model="query" value="{{ $search ?? '' }}"
                               placeholder="Search name, username, email…"
                               autocomplete="off"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-transparent rounded-lg text-sm font-medium text-lib-navy placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-lib-sky focus:bg-white transition-all">
                    </div>
                    @if(!empty($search))
                        <a href="{{ $clearUrl }}"
                           class="px-4 py-2 rounded-lg text-xs font-bold text-slate-500 hover:text-red-500 hover:bg-red-50 transition-colors">Clear</a>
                    @endif
                    <button type="submit"
                            :disabled="!query.trim()"
                            :class="query.trim()
                                ? 'bg-lib-sky hover:bg-lib-navy text-white shadow-md shadow-lib-sky/30 cursor-pointer'
                                : 'bg-slate-100 text-slate-400 cursor-not-allowed shadow-none'"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition-colors whitespace-nowrap">
                        Search
                    </button>
                </div>

                @if(!empty($search))
                    <p class="mt-3 text-xs text-slate-500 px-1">
                        Found <span class="font-bold text-lib-navy">{{ $users->total() }}</span>
                        {{ Str::plural('result', $users->total()) }} for
                        <span class="font-bold text-lib-navy">"{{ $search }}"</span>
                    </p>
                @endif
            </form>
        </div>

        @if(!empty($search))
            <div class="px-4 py-2 bg-lib-light/40 border-b border-slate-100 text-xs text-slate-600">
                Showing results for <span class="font-bold text-lib-navy">"{{ $search }}"</span>
                — {{ $users->total() }} {{ Str::plural('match', $users->total()) }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider">
                    <tr>
                        <th class="text-left px-4 py-2">Name</th>
                        <th class="text-left px-4 py-2">Username</th>
                        <th class="text-left px-4 py-2 hidden md:table-cell">Email</th>
                        <th class="text-left px-4 py-2">Role</th>
                        <th class="text-right px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-lib-light text-lib-navy flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-slate-800">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2 text-slate-600">{{ $user->username }}</td>
                            <td class="px-4 py-2 text-slate-500 hidden md:table-cell">{{ $user->email }}</td>
                            <td class="px-4 py-2">
                                @if($user->isAdmin())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-lib-navy text-white">Admin</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">User</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="px-4 py-2 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100 hover:text-lib-navy transition-colors">Edit</a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-4 py-2 rounded-lg text-xs font-bold text-red-500 hover:bg-red-50 transition-colors">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                                No users yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-slate-100 p-4">
                {{ $users->links() }}
            </div>
        @endif
        </div>
    </div>
</div>
@endsection
