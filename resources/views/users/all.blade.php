@extends('layouts.app')

@section('title', 'Collection')

@section('content')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    @keyframes fade-up {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fade-in {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .anim-fade-up { opacity: 0; animation: fade-up 0.55s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
    .anim-fade-in { opacity: 0; animation: fade-in 0.5s ease-out forwards; }

    .stagger > * { opacity: 0; animation: fade-up 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
    .stagger > *:nth-child(1)  { animation-delay: 0.03s; }
    .stagger > *:nth-child(2)  { animation-delay: 0.06s; }
    .stagger > *:nth-child(3)  { animation-delay: 0.09s; }
    .stagger > *:nth-child(4)  { animation-delay: 0.12s; }
    .stagger > *:nth-child(5)  { animation-delay: 0.15s; }
    .stagger > *:nth-child(6)  { animation-delay: 0.18s; }
    .stagger > *:nth-child(7)  { animation-delay: 0.21s; }
    .stagger > *:nth-child(8)  { animation-delay: 0.24s; }
    .stagger > *:nth-child(9)  { animation-delay: 0.27s; }
    .stagger > *:nth-child(10) { animation-delay: 0.30s; }
    .stagger > *:nth-child(11) { animation-delay: 0.33s; }
    .stagger > *:nth-child(12) { animation-delay: 0.36s; }
    .stagger > *:nth-child(n+13) { animation-delay: 0.40s; }

    @media (prefers-reduced-motion: reduce) {
        .anim-fade-up, .anim-fade-in, .stagger > * {
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
    }
</style>
@php
    $defaultThumb      = asset('images/logo.png');
    $activeCategoryIds = collect(request('categories', []))->map(fn ($id) => (int) $id)->all();
    $baseQuery         = request()->except(['categories', 'page']);
    $clearUrl          = route('media.index', request()->except(['search', 'page']));

    $statusBadge = [
        'draft'     => 'bg-amber-500 text-white',
        'archived'  => 'bg-slate-700 text-white',
        'published' => 'bg-emerald-500 text-white',
    ];
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-5rem)]">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-12 gap-6">

            {{-- ============ LEFT SIDEBAR: TAG FILTER ============ --}}
            @if($tags->isNotEmpty())
            <aside class="hidden lg:block lg:col-span-2">
                @php
                    $activeTagIds = array_map('intval', (array) request('tags', request('tag') ? [request('tag')] : []));
                    $tagBaseQuery = request()->except(['tag', 'tags', 'page']);
                @endphp
                <div class="space-y-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-3 px-1">
                            <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                Tags
                                @if(count($activeTagIds))
                                    <span class="ml-1 text-lib-sky">({{ count($activeTagIds) }})</span>
                                @endif
                            </h3>
                            @if(count($activeTagIds))
                                <a href="{{ route('media.index', $tagBaseQuery) }}"
                                   class="text-[10px] font-bold text-red-500 hover:text-red-600 transition-colors">Clear</a>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($tags as $tag)
                                @php
                                    $isActive = in_array($tag->id, $activeTagIds, true);
                                    $nextIds  = $isActive
                                        ? array_values(array_diff($activeTagIds, [$tag->id]))
                                        : array_values(array_merge($activeTagIds, [$tag->id]));
                                    $tagHref  = empty($nextIds)
                                        ? route('media.index', $tagBaseQuery)
                                        : route('media.index', array_merge($tagBaseQuery, ['tags' => $nextIds]));
                                @endphp
                                <a href="{{ $tagHref }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors {{ $isActive ? 'bg-lib-sky text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 hover:text-lib-navy' }}">
                                    @if($isActive)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>
            @endif

            {{-- ============ MAIN COLUMN ============ --}}
            <section class="col-span-12 {{ $tags->isNotEmpty() ? 'lg:col-span-10' : 'lg:col-span-12' }} space-y-6">

                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-[11px] font-bold uppercase text-lib-sky mb-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-lib-sky"></span>
                        Digital archive
                    </div>
                    <h1 class="text-2xl font-bold text-lib-navy leading-tight">Collection</h1>
                    <p class="text-sm text-slate-500 mt-1 max-w-2xl">Browse photos, videos, and books from the library archive.</p>
                </div>

                <div id="categories" class="bg-white rounded-lg p-6 shadow-sm border border-slate-100">

                    {{-- Header --}}
                    @auth
                        @if(Auth::user()->isAdmin())
                            <div class="flex justify-end mb-5">
                                <a href="{{ route('collections.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Add Collection
                                </a>
                            </div>
                        @endif
                    @endauth

                    {{-- Search --}}
                    <form action="{{ route('media.index') }}" method="GET" class="mb-5"
                          x-data="{ query: @js((string) request('search')), hadSearch: @js((bool) request('search')) }"
                          x-init="$watch('query', value => {
                              if (hadSearch && value.trim() === '') {
                                  window.location.href = @js($clearUrl);
                              }
                          })">
                        @foreach(request()->except(['search', 'page']) as $key => $value)
                            @if(is_array($value))
                                @foreach($value as $v)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <div class="relative flex items-center gap-2">
                            <div class="relative flex-grow">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="search" name="search" x-model="query" value="{{ request('search') }}"
                                       placeholder="Search collections…"
                                       autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                       class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-transparent rounded-lg text-sm font-medium text-lib-navy placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-lib-sky focus:bg-white transition-all">
                            </div>
                            @if(request('search'))
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

                        @if(request('search'))
                            <p class="mt-3 text-xs text-slate-500 px-1">
                                Found <span class="font-bold text-lib-navy">{{ $collections->total() }}</span>
                                {{ Str::plural('result', $collections->total()) }} for
                                <span class="font-bold text-lib-navy">"{{ request('search') }}"</span>
                            </p>
                        @endif
                    </form>

                    {{-- Status tabs (admin only) --}}
                    @auth
                        @if(Auth::user()->isAdmin() && isset($statusCounts))
                            @php
                                $statusBaseQuery = request()->except(['status', 'page']);
                                $statusTabs = ['all' => 'All', 'draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'];
                            @endphp
                            <nav class="-mx-6 px-6 mb-5 border-b border-slate-200 overflow-x-auto no-scrollbar">
                                <ul class="flex items-center gap-1 min-w-max">
                                    @foreach($statusTabs as $key => $label)
                                        @php
                                            $isActive = ($activeStatus ?? 'all') === $key;
                                            $href = $key === 'all'
                                                ? route('media.index', $statusBaseQuery)
                                                : route('media.index', array_merge($statusBaseQuery, ['status' => $key]));
                                        @endphp
                                        <li>
                                            <a href="{{ $href }}"
                                               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-bold border-b-2 -mb-px transition-colors {{ $isActive ? 'border-lib-sky text-lib-navy' : 'border-transparent text-slate-500 hover:text-lib-navy hover:border-slate-300' }}">
                                                {{ $label }}
                                                @if(!empty($statusCounts[$key]))
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-lib-sky text-white' : 'bg-slate-100 text-slate-500' }}">{{ $statusCounts[$key] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </nav>
                        @endif
                    @endauth

                    {{-- Category pills + controls --}}
                    @php
                        $visibleLimit      = 4;
                        $visibleCategories = $categories->take($visibleLimit);
                        $hiddenCategories  = $categories->slice($visibleLimit);
                        $hiddenHasActive   = $hiddenCategories->pluck('id')->intersect($activeCategoryIds)->isNotEmpty();

                        $currentSort = strtolower((string) request('sort')) === 'oldest' ? 'oldest' : 'newest';
                        $nextSort    = $currentSort === 'newest' ? 'oldest' : 'newest';
                        $sortQuery   = array_merge(request()->except(['page']), ['sort' => $nextSort]);

                        $currentView = $view ?? 'grid';
                        $nextView    = $currentView === 'grid' ? 'list' : 'grid';
                        $viewQuery   = array_merge(request()->except(['page']), ['view' => $nextView]);

                        $categoryHref = function ($catId) use ($activeCategoryIds, $baseQuery) {
                            $isActive = in_array($catId, $activeCategoryIds, true);
                            $nextIds  = $isActive
                                ? array_values(array_diff($activeCategoryIds, [$catId]))
                                : array_values(array_merge($activeCategoryIds, [$catId]));
                            return empty($nextIds)
                                ? route('media.index', $baseQuery)
                                : route('media.index', array_merge($baseQuery, ['categories' => $nextIds]));
                        };
                    @endphp

                    <div x-data="{ expanded: @js($hiddenHasActive) }" class="flex flex-wrap items-center gap-2 mb-6">
                        <a href="{{ route('media.index', $baseQuery) }}"
                           class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ empty($activeCategoryIds) ? 'bg-lib-sky text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">All</a>

                        @foreach($visibleCategories as $cat)
                            @php $isActive = in_array($cat->id, $activeCategoryIds, true); @endphp
                            <a href="{{ $categoryHref($cat->id) }}"
                               class="inline-flex items-center gap-1 px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ $isActive ? 'bg-lib-sky text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                @if($isActive)
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                                {{ $cat->name }}
                            </a>
                        @endforeach

                        @if($hiddenCategories->isNotEmpty())
                            @foreach($hiddenCategories as $cat)
                                @php $isActive = in_array($cat->id, $activeCategoryIds, true); @endphp
                                <a href="{{ $categoryHref($cat->id) }}" x-show="expanded" x-cloak
                                   class="inline-flex items-center gap-1 px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ $isActive ? 'bg-lib-sky text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                    @if($isActive)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                            <button type="button" @click="expanded = !expanded"
                                    class="w-8 h-7 inline-flex items-center justify-center rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-lib-navy transition-colors"
                                    :aria-label="expanded ? 'Show fewer' : 'Show all'">
                                <svg x-show="!expanded" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
                                <svg x-show="expanded" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                            </button>
                        @endif

                        <div class="ml-auto flex items-center gap-2">
                            <a href="{{ route('media.index', $viewQuery) }}" title="Switch to {{ $nextView }} view"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-50 hover:bg-lib-light text-slate-600 hover:text-lib-navy transition-colors">
                                @if($currentView === 'grid')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                @endif
                            </a>
                            <a href="{{ route('media.index', $sortQuery) }}" title="Currently {{ $currentSort }} first. Click for {{ $nextSort }}."
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-slate-50 hover:bg-lib-light text-slate-600 hover:text-lib-navy text-xs font-bold transition-colors">
                                @if($currentSort === 'newest')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h5m12 8l-4-4m0 0l4-4m-4 4h12"/></svg>
                                    <span>Newest</span>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9M3 12h9m4 0l4 4m0 0l-4 4m4-4H8"/></svg>
                                    <span>Oldest</span>
                                @endif
                            </a>
                        </div>
                    </div>

                    {{-- Grid view --}}
                    @if($currentView === 'grid')
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @forelse($collections as $collection)
                                @php
                                    $thumbUrl     = $collection->thumbnail_path
                                        ? asset('storage/' . $collection->thumbnail_path)
                                        : $defaultThumb;
                                    $hasRealThumb = (bool) $collection->thumbnail_path;
                                @endphp
                                <a href="{{ route('collections.show', $collection) }}" class="group block">
                                    <div class="aspect-[3/4] rounded-lg overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 shadow-md group-hover:shadow-lg transition-shadow relative">
                                        <img src="{{ $thumbUrl }}" alt="{{ $collection->name }}"
                                             class="w-full h-full {{ $hasRealThumb ? 'object-cover' : 'object-contain p-6 bg-white' }} group-hover:scale-105 transition-transform duration-500"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ $defaultThumb }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-6','bg-white');">
                                        @auth
                                            @if(Auth::user()->isAdmin() && $collection->status !== 'published')
                                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $statusBadge[$collection->status] ?? 'bg-slate-500 text-white' }} shadow">
                                                    {{ $collection->status }}
                                                </span>
                                            @endif
                                        @endauth
                                    </div>
                                    <div class="mt-3 px-1">
                                        <h3 class="text-sm font-bold text-slate-800 line-clamp-1 group-hover:text-lib-sky transition-colors">{{ $collection->name }}</h3>
                                        @if(filled($collection->description))
                                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $collection->description }}</p>
                                        @endif
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            @if($collection->categories->isNotEmpty())
                                                {{ $collection->categories->first()->name }} &middot;
                                            @endif
                                            {{ $mediaCounts[$collection->name] ?? 0 }} {{ Str::plural('item', $mediaCounts[$collection->name] ?? 0) }}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="col-span-full py-12 text-center text-slate-400 text-sm">
                                    No items match your filters.
                                </div>
                            @endforelse
                        </div>

                    {{-- List view --}}
                    @else
                        <div class="flex flex-col divide-y divide-slate-100 border border-slate-100 rounded-lg overflow-hidden">
                            @forelse($collections as $collection)
                                @php
                                    $thumbUrl     = $collection->thumbnail_path
                                        ? asset('storage/' . $collection->thumbnail_path)
                                        : $defaultThumb;
                                    $hasRealThumb = (bool) $collection->thumbnail_path;
                                @endphp
                                <a href="{{ route('collections.show', $collection) }}"
                                   class="group flex items-center gap-4 p-3 hover:bg-slate-50 transition-colors">
                                    <div class="w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100">
                                        <img src="{{ $thumbUrl }}" alt="{{ $collection->name }}"
                                             class="w-full h-full {{ $hasRealThumb ? 'object-cover' : 'object-contain p-1 bg-white' }}"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ $defaultThumb }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-1','bg-white');">
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-800 truncate group-hover:text-lib-sky transition-colors">{{ $collection->name }}</h3>
                                            @auth
                                                @if(Auth::user()->isAdmin() && $collection->status !== 'published')
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $statusBadge[$collection->status] ?? 'bg-slate-500 text-white' }} flex-shrink-0">
                                                        {{ $collection->status }}
                                                    </span>
                                                @endif
                                            @endauth
                                        </div>
                                        @if(filled($collection->description))
                                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $collection->description }}</p>
                                        @endif
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $collection->created_at?->format('M j, Y') }}
                                            &middot;
                                            {{ $mediaCounts[$collection->name] ?? 0 }} {{ Str::plural('item', $mediaCounts[$collection->name] ?? 0) }}
                                        </p>
                                    </div>
                                    @if($collection->categories->isNotEmpty())
                                        <span class="hidden sm:inline-block text-[10px] font-bold text-lib-sky bg-lib-light px-2 py-1 rounded-full whitespace-nowrap">
                                            {{ $collection->categories->first()->name }}
                                        </span>
                                    @endif
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4 text-slate-300 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @empty
                                <div class="py-12 text-center text-slate-400 text-sm">
                                    No items match your filters.
                                </div>
                            @endforelse
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                @if($collections->total() > 0)
                    <div class="bg-white rounded-lg px-6 py-4 shadow-sm border border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-sm text-slate-500 font-medium">
                            Showing
                            <span class="font-bold">{{ $collections->firstItem() }}</span>
                            &ndash;
                            <span class="font-bold">{{ $collections->lastItem() }}</span>
                            of
                            <span class="font-bold">{{ $collections->total() }}</span>
                            {{ Str::plural('collection', $collections->total()) }}
                        </p>
                        @if($collections->hasPages())
                            <div class="flex items-center">
                                {{ $collections->onEachSide(1)->links('vendor.pagination.library') }}
                            </div>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
