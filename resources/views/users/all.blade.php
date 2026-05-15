@extends('layouts.app')

@section('title', 'Discover')

@section('content')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Entrance animations */
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

    /* Stagger children */
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
    $defaultThumb = asset('images/logo.png');
    $typeLabels = [
        'photo' => 'Photo',
        'video' => 'Video',
        'ebook' => 'Book',
    ];

    $activeCategoryIds = collect(request('categories', []))->map(fn ($id) => (int) $id)->all();
    $baseQuery        = request()->except(['categories', 'page']);
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-5rem)]">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-12 gap-6">

            {{-- ============ LEFT SIDEBAR (mobile: horizontal pills) ============ --}}
            @php
                $currentType = strtolower((string) request('type'));
                $navItems = [
                    ['label' => 'Discover',   'icon' => 'home',       'route' => route('media.index'),                      'active' => $currentType === '',                  'count' => $typeCounts['total']],
                    ['label' => 'Photos',     'icon' => 'photo',      'route' => route('media.index', ['type' => 'photo']), 'active' => $currentType === 'photo',             'count' => $typeCounts['photo']],
                    ['label' => 'Videos',     'icon' => 'video',      'route' => route('media.index', ['type' => 'video']), 'active' => $currentType === 'video',             'count' => $typeCounts['video']],
                    ['label' => 'Books',      'icon' => 'ebook',      'route' => route('media.index', ['type' => 'ebook']), 'active' => $currentType === 'ebook',             'count' => $typeCounts['ebook']],
                    ['divider' => true],
                    ['label' => 'Collection', 'icon' => 'collection', 'route' => route('collections.index'),                'active' => request()->routeIs('collections.*'),  'count' => $collectionCount ?? 0],
                    ['label' => 'My List',    'icon' => 'list',       'route' => route('mylist.index'),                     'active' => request()->routeIs('mylist.*'),       'count' => $myListCount ?? 0],
                    ['label' => 'VR Tour',    'icon' => 'vr',         'route' => route('vr'),                               'active' => false,                                'count' => null],
                ];
            @endphp
            @php
                $iconSvg = function ($icon) {
                    return match ($icon) {
                        'home'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>',
                        'photo'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
                        'video'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>',
                        'ebook'      => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>',
                        'collection' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>',
                        'list'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>',
                        'vr'         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>',
                        default      => '',
                    };
                };
            @endphp

            {{-- Small screens: horizontal scrollable pill bar (sticky below header) --}}
            <div class="col-span-12 lg:hidden sticky top-32 md:top-36 z-30 -mx-4 sm:mx-0 px-4 sm:px-0 pt-2 pb-2 bg-slate-50">
                <nav class="bg-white rounded-3xl p-2 shadow-md border border-slate-100 relative"
                     x-data="{
                        canScrollLeft: false,
                        canScrollRight: false,
                        update() {
                            const el = this.$refs.scroller;
                            this.canScrollLeft  = el.scrollLeft > 4;
                            this.canScrollRight = el.scrollLeft + el.clientWidth < el.scrollWidth - 4;
                        },
                        scrollBy(dir) {
                            this.$refs.scroller.scrollBy({ left: dir * 160, behavior: 'smooth' });
                        }
                     }"
                     x-init="update(); $nextTick(update); window.addEventListener('resize', update)">

                    {{-- Left fade + arrow --}}
                    <button type="button" @click="scrollBy(-1)" x-show="canScrollLeft" x-cloak
                            class="absolute left-0 top-0 bottom-0 z-10 flex items-center pl-1 pr-3 bg-gradient-to-r from-white via-white to-transparent rounded-l-3xl"
                            aria-label="Scroll left">
                        <span class="w-7 h-7 rounded-full bg-white shadow-md border border-slate-100 flex items-center justify-center text-lib-navy">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        </span>
                    </button>

                    {{-- Right fade + arrow --}}
                    <button type="button" @click="scrollBy(1)" x-show="canScrollRight" x-cloak
                            class="absolute right-0 top-0 bottom-0 z-10 flex items-center pr-1 pl-3 bg-gradient-to-l from-white via-white to-transparent rounded-r-3xl"
                            aria-label="Scroll right">
                        <span class="w-7 h-7 rounded-full bg-white shadow-md border border-slate-100 flex items-center justify-center text-lib-navy">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </button>

                    <div x-ref="scroller" @scroll.passive="update"
                         class="flex items-center gap-1 overflow-x-auto no-scrollbar scroll-smooth">
                        @foreach($navItems as $item)
                            @if(!empty($item['divider']))
                                <span class="h-6 w-px bg-slate-200 mx-1 flex-shrink-0" aria-hidden="true"></span>
                            @else
                                <a href="{{ $item['route'] }}"
                                   class="flex items-center gap-2 px-3 py-2 rounded-2xl text-xs font-semibold whitespace-nowrap transition-all flex-shrink-0 {{ $item['active'] ? 'bg-lib-sky text-white shadow-md shadow-lib-sky/30' : 'text-slate-500 hover:bg-slate-50 hover:text-lib-navy' }}">
                                    <span class="w-5 h-5 flex items-center justify-center">{!! $iconSvg($item['icon']) !!}</span>
                                    <span>{{ $item['label'] }}</span>
                                    @if(!empty($item['count']))
                                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $item['active'] ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $item['count'] }}</span>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </nav>
            </div>

            {{-- Desktop: vertical sidebar + tag filter --}}
            <aside class="hidden lg:block lg:col-span-2">
                @php
                    // Support both ?tag=N (legacy) and ?tags[]=N&tags[]=M (multi).
                    $activeTagIds = array_map('intval', (array) request('tags', request('tag') ? [request('tag')] : []));
                    $tagBaseQuery = request()->except(['tag', 'tags', 'page']);
                @endphp

                <div class="space-y-4">
                    @php
                        // Split nav items into separate cards at every `divider` entry.
                        $navSections = [];
                        $section     = [];
                        foreach ($navItems as $entry) {
                            if (!empty($entry['divider'])) {
                                if (!empty($section)) {
                                    $navSections[] = $section;
                                    $section = [];
                                }
                            } else {
                                $section[] = $entry;
                            }
                        }
                        if (!empty($section)) {
                            $navSections[] = $section;
                        }
                    @endphp

                    @foreach($navSections as $section)
                        <nav class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100">
                            <ul class="space-y-1">
                                @foreach($section as $item)
                                    @php
                                        $isVr = ($item['icon'] ?? null) === 'vr';
                                        if ($item['active']) {
                                            $itemClasses = $isVr
                                                ? 'bg-amber-500 text-white shadow-md shadow-amber-500/30'
                                                : 'bg-lib-sky text-white shadow-md shadow-lib-sky/30';
                                        } else {
                                            $itemClasses = $isVr
                                                ? 'bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold'
                                                : 'text-slate-500 hover:bg-slate-50 hover:text-lib-navy';
                                        }
                                    @endphp
                                    <li>
                                        <a href="{{ $item['route'] }}"
                                           @if($isVr) target="_blank" rel="noopener noreferrer" @endif
                                           class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ $itemClasses }}">
                                            <span class="w-5 h-5 flex items-center justify-center">{!! $iconSvg($item['icon']) !!}</span>
                                            <span class="hidden xl:inline">{{ $item['label'] }}</span>
                                            @if($isVr)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="hidden xl:inline-block ml-auto h-3.5 w-3.5 opacity-70"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            @elseif(!empty($item['count']))
                                                <span class="hidden xl:inline-block ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold {{ $item['active'] ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $item['count'] }}</span>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </nav>
                    @endforeach

                {{-- Tag filter (multi-select) --}}
                @if($tags->isNotEmpty())
                    <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100">
                        <div class="flex items-center justify-between mb-3 px-1">
                            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
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
                                    // Toggle: remove if active, add if not.
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
                @endif
                </div>
            </aside>

            {{-- ============ MAIN COLUMN ============ --}}
            <section class="col-span-12 lg:col-span-10 space-y-6">

                <div id="categories" class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
                    @php
                        $clearUrl = route('media.index', request()->except(['search', 'page']));

                        $headerSubtitle = match (strtolower((string) request('type'))) {
                            'photo' => 'Browse high-resolution photos from events, locations, and archives.',
                            'video' => 'Watch documentaries, interviews, and archival footage.',
                            'ebook' => 'Read reports, journals, and publications online.',
                            default => 'Browse the digital library — photos, videos, and books from the archive.',
                        };
                        $headerTitle = match (strtolower((string) request('type'))) {
                            'photo' => 'Photos',
                            'video' => 'Videos',
                            'ebook' => 'Books',
                            default => 'Media',
                        };
                    @endphp

                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
                        <div>
                            <h1 class="text-xl font-bold text-lib-navy">{{ $headerTitle }}</h1>
                            <p class="text-sm text-slate-500">{{ $headerSubtitle }}</p>
                        </div>

                        @auth
                            @if(Auth::user()->isAdmin())
                                <a href="{{ route('media.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                    Add Media
                                </a>
                            @endif
                        @endauth
                    </div>

                    <form action="{{ route('media.index') }}" method="GET" class="mb-5"
                          x-data="{ query: @js((string) request('search')), hadSearch: @js((bool) request('search')) }"
                          x-init="$watch('query', value => {
                              if (hadSearch && value.trim() === '') {
                                  window.location.href = @js($clearUrl);
                              }
                          })">
                        {{-- Preserve existing filter state (type, categories, year) on submit --}}
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
                                       placeholder="Search media…"
                                       autocomplete="off"
                                       autocorrect="off"
                                       autocapitalize="off"
                                       spellcheck="false"
                                       class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-transparent rounded-2xl text-sm font-medium text-lib-navy placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-lib-sky focus:bg-white transition-all">
                            </div>
                            @if(request('search'))
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

                        @if(request('search'))
                            <p class="mt-3 text-xs text-slate-500 px-1">
                                Found <span class="font-bold text-lib-navy">{{ $assets->total() }}</span>
                                {{ Str::plural('result', $assets->total()) }} for
                                <span class="font-bold text-lib-navy">"{{ request('search') }}"</span>
                            </p>
                        @endif
                    </form>

                    {{-- Status tabs (admin only) --}}
                    @auth
                        @if(Auth::user()->isAdmin() && isset($statusCounts))
                            @php
                                $statusBaseQuery = request()->except(['status', 'page']);
                                $statusTabs = [
                                    'all'       => 'All',
                                    'draft'     => 'Draft',
                                    'published' => 'Published',
                                    'archived'  => 'Archived',
                                ];
                            @endphp
                            <nav class="-mx-6 px-6 mb-5 border-b border-slate-200 overflow-x-auto no-scrollbar">
                                <ul class="flex items-center gap-1 min-w-max">
                                    @foreach($statusTabs as $key => $label)
                                        @php
                                            $isActive = ($activeStatus ?? 'all') === $key;
                                            $href     = $key === 'all'
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

                    {{-- Category pills (server-side filter) --}}
                    @php
                        // Show first 4 categories ("All" + 4 = 5 visible pills).
                        $visibleLimit      = 4;
                        $visibleCategories = $categories->take($visibleLimit);
                        $hiddenCategories  = $categories->slice($visibleLimit);
                        // If an active filter lives in the hidden bucket, expand by default.
                        $hiddenHasActive   = $hiddenCategories->pluck('id')->intersect($activeCategoryIds)->isNotEmpty();

                        $currentSort = strtolower((string) request('sort')) === 'oldest' ? 'oldest' : 'newest';
                        $nextSort    = $currentSort === 'newest' ? 'oldest' : 'newest';
                        $sortQuery   = array_merge(request()->except(['page']), ['sort' => $nextSort]);

                        // $view is set by the controller from the query string or the persisted cookie.
                        $currentView = $view ?? 'grid';
                        $nextView    = $currentView === 'grid' ? 'list' : 'grid';
                        $viewQuery   = array_merge(request()->except(['page']), ['view' => $nextView]);
                    @endphp

                    @php
                        // Toggle helper: clicking a category adds/removes it from the active set.
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
                                <a href="{{ $categoryHref($cat->id) }}"
                                   x-show="expanded" x-cloak
                                   class="inline-flex items-center gap-1 px-4 py-1.5 rounded-full text-xs font-bold transition-colors {{ $isActive ? 'bg-lib-sky text-white' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                                    @if($isActive)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    @endif
                                    {{ $cat->name }}
                                </a>
                            @endforeach

                            <button type="button" @click="expanded = !expanded"
                                    class="w-8 h-7 inline-flex items-center justify-center rounded-full bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-lib-navy transition-colors"
                                    :aria-label="expanded ? 'Show fewer categories' : 'Show all categories'"
                                    :title="expanded ? 'Show less' : 'Show all'">
                                <svg x-show="!expanded" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><circle cx="5" cy="12" r="1.8"/><circle cx="12" cy="12" r="1.8"/><circle cx="19" cy="12" r="1.8"/></svg>
                                <svg x-show="expanded" x-cloak xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                            </button>
                        @endif

                        <div class="ml-auto flex items-center gap-2">
                            {{-- View toggle (grid ↔ list) --}}
                            <a href="{{ route('media.index', $viewQuery) }}"
                               title="Switch to {{ $nextView }} view"
                               aria-label="Toggle view mode"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-slate-50 hover:bg-lib-light text-slate-600 hover:text-lib-navy transition-colors">
                                @if($currentView === 'grid')
                                    {{-- Currently grid — show list icon for next state --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                                @else
                                    {{-- Currently list — show grid icon for next state --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                @endif
                            </a>

                            {{-- Sort toggle (newest ↔ oldest by created_at) --}}
                            <a href="{{ route('media.index', $sortQuery) }}"
                               title="Sort by date added — currently {{ $currentSort }} first. Click for {{ $nextSort }}."
                               aria-label="Toggle sort order"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-50 hover:bg-lib-light text-slate-600 hover:text-lib-navy text-xs font-bold transition-colors">
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

                    {{-- Results (server-rendered, paginated) — grid or list view --}}
                    @if($currentView === 'grid')
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 stagger">
                            @forelse($assets as $asset)
                                @php
                                    $typeKey   = strtolower((string) $asset->type);
                                    $typeLabel = $typeLabels[$typeKey] ?? ucfirst((string) $asset->type);

                                    // Thumbnail in priority: explicit thumb → photo's own source (link or upload) → default logo.
                                    if (!empty($asset->thumbnail_path)) {
                                        $thumbUrl = asset('storage/' . $asset->thumbnail_path);
                                    } elseif ($typeKey === 'photo' && !empty($asset->file_url)) {
                                        $thumbUrl = $asset->file_url;
                                    } elseif ($typeKey === 'photo' && !empty($asset->file_path)) {
                                        $thumbUrl = asset('storage/' . $asset->file_path);
                                    } else {
                                        $thumbUrl = $defaultThumb;
                                    }
                                    $hasRealThumb = $thumbUrl !== $defaultThumb;
                                @endphp
                                <a href="{{ route('media.show', $asset) }}" class="group block">
                                    <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 shadow-md group-hover:shadow-xl transition-shadow relative">
                                        <img src="{{ $thumbUrl }}" alt="{{ $asset->title }}"
                                             class="w-full h-full {{ $hasRealThumb ? 'object-cover' : 'object-contain p-6 bg-white' }} group-hover:scale-105 transition-transform duration-500"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ $defaultThumb }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-6','bg-white');">
                                        @auth
                                            @if(Auth::user()->isAdmin() && $asset->status !== 'published')
                                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $asset->status === 'archived' ? 'bg-slate-700 text-white' : 'bg-amber-500 text-white' }} shadow">
                                                    {{ $asset->status }}
                                                </span>
                                            @endif
                                        @endauth
                                    </div>
                                    <div class="mt-3 px-1">
                                        <h3 class="text-sm font-bold text-slate-800 line-clamp-1 group-hover:text-lib-sky transition-colors">{{ $asset->title }}</h3>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $typeLabel }}</p>
                                    </div>
                                </a>
                            @empty
                                <div class="col-span-full py-12 text-center text-slate-400 text-sm">
                                    No items match your filters.
                                </div>
                            @endforelse
                        </div>
                    @else
                        <div class="flex flex-col divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden stagger">
                            @forelse($assets as $asset)
                                @php
                                    $typeKey   = strtolower((string) $asset->type);
                                    $typeLabel = $typeLabels[$typeKey] ?? ucfirst((string) $asset->type);

                                    if (!empty($asset->thumbnail_path)) {
                                        $thumbUrl = asset('storage/' . $asset->thumbnail_path);
                                    } elseif ($typeKey === 'photo' && !empty($asset->file_url)) {
                                        $thumbUrl = $asset->file_url;
                                    } elseif ($typeKey === 'photo' && !empty($asset->file_path)) {
                                        $thumbUrl = asset('storage/' . $asset->file_path);
                                    } else {
                                        $thumbUrl = $defaultThumb;
                                    }
                                    $hasRealThumb = $thumbUrl !== $defaultThumb;
                                @endphp
                                <a href="{{ route('media.show', $asset) }}"
                                   class="group flex items-center gap-4 p-3 hover:bg-slate-50 transition-colors">
                                    <div class="w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100">
                                        <img src="{{ $thumbUrl }}" alt="{{ $asset->title }}"
                                             class="w-full h-full {{ $hasRealThumb ? 'object-cover' : 'object-contain p-1 bg-white' }}"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ $defaultThumb }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-1','bg-white');">
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-800 truncate group-hover:text-lib-sky transition-colors">{{ $asset->title }}</h3>
                                            @auth
                                                @if(Auth::user()->isAdmin() && $asset->status !== 'published')
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $asset->status === 'archived' ? 'bg-slate-700 text-white' : 'bg-amber-500 text-white' }} flex-shrink-0">
                                                        {{ $asset->status }}
                                                    </span>
                                                @endif
                                            @endauth
                                        </div>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $typeLabel }} &middot; {{ $asset->created_at?->format('M j, Y') }}</p>
                                    </div>
                                    @if($asset->categories->isNotEmpty())
                                        <span class="hidden sm:inline-block text-[10px] font-bold text-lib-sky bg-lib-light px-2 py-1 rounded-full whitespace-nowrap">
                                            {{ $asset->categories->first()->name }}
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
                @if($assets->total() > 0)
                    <div class="bg-white rounded-3xl px-6 py-4 shadow-sm border border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="text-sm text-slate-500 font-medium">
                            Showing
                            <span class="font-bold">{{ $assets->firstItem() }}</span>
                            &ndash;
                            <span class="font-bold">{{ $assets->lastItem() }}</span>
                            of
                            <span class="font-bold">{{ $assets->total() }}</span>
                            results
                        </p>
                        @if($assets->hasPages())
                            <div class="flex items-center">
                                {{ $assets->onEachSide(1)->links('vendor.pagination.library') }}
                            </div>
                        @endif
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
