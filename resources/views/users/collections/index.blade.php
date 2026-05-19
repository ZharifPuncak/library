@extends('layouts.app')

@section('title', 'Collections')

@section('content')
@php
    $defaultThumb = asset('images/logo.png');
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-5rem)]">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-8">

        {{-- Back to media --}}
        <a href="{{ route('media.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Back to media
        </a>

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">

            {{-- Header: title + Add button --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
                <div>
                    <h1 class="text-xl font-bold text-lib-navy">Collections</h1>
                    <p class="text-sm text-slate-500">Grouped media albums and events.</p>
                </div>

                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('collections.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md whitespace-nowrap">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Add Collection
                        </a>
                    @endif
                @endauth
            </div>

            {{-- Search --}}
            @php $clearUrl = route('collections.index'); @endphp
            <form method="GET" action="{{ route('collections.index') }}" class="mb-5"
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
                               placeholder="Search collections…"
                               autocomplete="off"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-transparent rounded-2xl text-sm font-medium text-lib-navy placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-lib-sky focus:bg-white transition-all">
                    </div>
                    @if(!empty($search))
                        <a href="{{ $clearUrl }}"
                           class="px-4 py-3 rounded-2xl text-xs font-bold text-slate-500 hover:text-red-500 hover:bg-red-50 transition-colors">Clear</a>
                    @endif
                    <button type="submit"
                            :disabled="!query.trim()"
                            :class="query.trim()
                                ? 'bg-lib-sky hover:bg-lib-navy text-white shadow-md shadow-lib-sky/30 cursor-pointer'
                                : 'bg-slate-100 text-slate-400 cursor-not-allowed shadow-none'"
                            class="px-6 py-3 rounded-2xl text-sm font-bold transition-colors whitespace-nowrap">
                        Search
                    </button>
                </div>

                @if(!empty($search))
                    <p class="mt-3 text-xs text-slate-500 px-1">
                        Found <span class="font-bold text-lib-navy">{{ $collections->count() }}</span>
                        {{ Str::plural('result', $collections->count()) }} for
                        <span class="font-bold text-lib-navy">"{{ $search }}"</span>
                    </p>
                @endif
            </form>

            {{-- Collections list --}}
            @if($collections->isEmpty())
                <div class="py-12 text-center text-slate-400 text-sm">
                    No collections yet.
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('collections.create') }}" class="text-lib-sky font-semibold hover:underline">Create the first one</a>.
                        @endif
                    @endauth
                </div>
            @else
                <div class="flex flex-col divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                    @foreach($collections as $col)
                        <a href="{{ route('collections.show', $col->model) }}"
                           class="group flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex-grow min-w-0">
                                <h3 class="text-sm font-bold text-slate-800 truncate group-hover:text-lib-sky transition-colors">{{ $col->name }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Collection &middot; {{ \Carbon\Carbon::parse($col->date)->format('M j, Y') }}
                                </p>
                            </div>
                            <span class="hidden sm:inline-block text-[10px] font-bold text-lib-sky bg-lib-light px-2 py-1 rounded-full whitespace-nowrap">
                                {{ $col->count }} {{ Str::plural('item', $col->count) }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4 text-slate-300 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
