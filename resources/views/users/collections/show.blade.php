@extends('layouts.app')

@section('title', $collectionName . ' - Collection')

@section('content')
@php
    $defaultThumb = asset('images/logo.png');
    $typeLabels   = ['photo' => 'Photo', 'video' => 'Video', 'ebook' => 'Book'];
    $isAdmin      = auth()->user()?->isAdmin();
    $inMyList     = auth()->check()
        ? auth()->user()->myListCollections()->whereKey($collection->id)->exists()
        : false;
    $previewItems = $assets->getCollection()->map(function ($asset) use ($defaultThumb) {
        $type = strtolower($asset->type);
        $src = $asset->resource_url ?? ($asset->file_path ? asset('storage/' . $asset->file_path) : null);
        $thumb = $asset->thumbnail_path
            ? asset('storage/' . $asset->thumbnail_path)
            : ($type === 'photo' ? $src : null);

        return [
            'id' => $asset->id,
            'title' => $asset->title,
            'description' => $asset->description,
            'type' => $type,
            'src' => $src,
            'thumb' => $thumb ?: $defaultThumb,
            'date' => $asset->created_at?->format('M j, Y'),
            'location' => $asset->getDetail('location'),
        ];
    })->values();
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-5rem)]"
     x-data="{
        items: @js($previewItems),
        previewOpen: false,
        current: 0,
        get item() { return this.items[this.current] || null; },
        openPreview(index) {
            this.current = index;
            this.previewOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closePreview() {
            this.previewOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        prevPreview() {
            if (!this.items.length) return;
            this.current = (this.current - 1 + this.items.length) % this.items.length;
        },
        nextPreview() {
            if (!this.items.length) return;
            this.current = (this.current + 1) % this.items.length;
        }
     }"
     @keydown.escape.window="previewOpen && closePreview()"
     @keydown.left.window="previewOpen && prevPreview()"
     @keydown.right.window="previewOpen && nextPreview()"
     @open-preview.window="openPreview($event.detail)">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-8">

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-5">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ===== LEFT: TITLE + MEDIA LIST ===== --}}
            <div class="lg:col-span-8 space-y-4">

                {{-- Title card --}}
                <div class="relative bg-white rounded-lg shadow-sm border border-slate-100 p-6 pr-16 flex items-center gap-5">
                    @auth
                        @if($inMyList)
                            <form method="POST" action="{{ route('mylist.collections.destroy', $collection) }}" class="absolute top-4 right-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Remove from My List"
                                        aria-label="Remove from My List"
                                        class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-orange-500 text-white border border-orange-500 hover:bg-orange-600 hover:border-orange-600 transition-colors shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('mylist.collections.store', $collection) }}" class="absolute top-4 right-4">
                                @csrf
                                <button type="submit"
                                        title="Add to My List"
                                        aria-label="Add to My List"
                                        class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-white text-orange-500 border border-orange-300 hover:bg-orange-50 hover:border-orange-400 transition-colors shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                </button>
                            </form>
                        @endif
                    @endauth
                    <div class="w-16 h-20 sm:w-20 sm:h-24 flex-shrink-0 rounded-lg overflow-hidden bg-lib-light flex items-center justify-center text-lib-sky">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-9 w-9"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-lib-sky mb-1">
                            {{ $counts['total'] }} {{ Str::plural('item', $counts['total']) }}
                        </p>
                        <h1 class="text-2xl md:text-3xl font-bold text-lib-navy leading-tight truncate">{{ $collectionName }}</h1>
                        <span class="inline-flex items-center px-2.5 py-1 mt-2 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                            Collection
                        </span>
                        @if(filled($collection->description))
                            <p class="mt-3 text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $collection->description }}</p>
                        @endif
                    </div>
                </div>

                {{-- Media list --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
                        <div>
                            <h2 class="text-base font-bold text-lib-navy">Media in this collection</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Click the media thumbnail to preview.</p>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">{{ $assets->total() }} {{ Str::plural('item', $assets->total()) }}</span>
                    </div>

                    {{-- Search + type filters --}}
                    @php
                        $typeTabs = [
                            ''      => ['label' => 'All',    'count' => $counts['total'], 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>'],
                            'photo' => ['label' => 'Photos', 'count' => $counts['photo'], 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'],
                            'video' => ['label' => 'Videos', 'count' => $counts['video'], 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>'],
                            'ebook' => ['label' => 'Books',  'count' => $counts['ebook'], 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>'],
                        ];
                    @endphp

                    <form method="GET" action="{{ route('collections.show', $collection) }}" class="mb-4">
                        <input type="hidden" name="type" value="{{ $activeType }}">
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </span>
                            <input type="search" name="q" value="{{ $activeSearch }}" placeholder="Search this collection…"
                                   autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                   @input.debounce.400ms="$el.form.requestSubmit()"
                                   @search="$el.form.requestSubmit()"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/30 transition-colors">
                            @if($activeSearch !== '')
                                <a href="{{ route('collections.show', $collection) }}{{ $activeType ? '?type=' . $activeType : '' }}"
                                   class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition-colors"
                                   aria-label="Clear search">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                    </form>

                    <div class="flex flex-wrap gap-2 mb-5">
                        @foreach($typeTabs as $typeKey => $tab)
                            @php
                                $isActive = $activeType === $typeKey;
                                $href = route('collections.show', $collection)
                                    . ($typeKey || $activeSearch
                                        ? '?' . http_build_query(array_filter(['type' => $typeKey, 'q' => $activeSearch]))
                                        : '');
                            @endphp
                            <a href="{{ $href }}"
                               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-xs font-bold transition-colors border {{ $isActive ? 'bg-lib-sky text-white border-lib-sky shadow-md shadow-lib-sky/30' : 'bg-white text-slate-600 border-slate-200 hover:border-lib-sky hover:text-lib-navy' }}">
                                {!! $tab['icon'] !!}
                                <span>{{ $tab['label'] }}</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $isActive ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $tab['count'] }}</span>
                            </a>
                        @endforeach
                    </div>

                    @if($assets->isEmpty())
                        <div class="py-12 text-center text-slate-400 text-sm">No media in this collection yet.</div>
                    @else
                        <div class="flex flex-col divide-y divide-slate-100 border border-slate-100 rounded-lg overflow-hidden">
                            @foreach($assets as $asset)
                                @php
                                    $type        = strtolower($asset->type);
                                    $typeLabel   = $typeLabels[$type] ?? ucfirst($asset->type);
                                    $location    = $asset->getDetail('location');
                                    if (!empty($asset->thumbnail_path)) {
                                        $thumbUrl = asset('storage/' . $asset->thumbnail_path);
                                    } elseif ($type === 'photo' && !empty($asset->file_url)) {
                                        $thumbUrl = $asset->file_url;
                                    } elseif ($type === 'photo' && !empty($asset->file_path)) {
                                        $thumbUrl = asset('storage/' . $asset->file_path);
                                    } else {
                                        $thumbUrl = $defaultThumb;
                                    }
                                    $hasRealThumb = $thumbUrl !== $defaultThumb;
                                @endphp
                                <div x-data="{ open: false }" class="group">
                                    <div class="flex items-center gap-4 p-3 hover:bg-slate-50 transition-colors">
                                        <button type="button" @click.stop="openPreview({{ $loop->index }})"
                                                class="relative w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100 focus:outline-none focus:ring-2 focus:ring-lib-sky focus:ring-offset-2 group/thumb"
                                                title="Open preview">
                                            <img src="{{ $thumbUrl }}" alt="{{ $asset->title }}"
                                                 class="w-full h-full {{ $hasRealThumb ? 'object-cover' : 'object-contain p-1 bg-white' }}"
                                                 loading="lazy"
                                                 onerror="this.onerror=null; this.src='{{ $defaultThumb }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-1','bg-white');">
                                            <span class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover/thumb:opacity-100 transition-opacity">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5 text-white drop-shadow">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0zM11 8v6M8 11h6"/>
                                                </svg>
                                            </span>
                                        </button>

                                        <button type="button" @click="open = !open"
                                                class="flex items-center gap-4 flex-grow min-w-0 text-left">
                                            <div class="flex-grow min-w-0">
                                                <h3 class="text-sm font-bold text-slate-800 truncate group-hover:text-lib-sky transition-colors">{{ $asset->title }}</h3>
                                                @if(filled($asset->description))
                                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $asset->description }}</p>
                                                @endif
                                                <p class="text-xs text-slate-400 mt-0.5">
                                                    {{ $typeLabel }} &middot; {{ $asset->created_at?->format('M j, Y') }}
                                                    @if(filled($location))
                                                        &middot; {{ $location }}
                                                    @endif
                                                </p>
                                            </div>
                                            @if($asset->categories->isNotEmpty())
                                                <span class="hidden sm:inline-block text-[10px] font-bold text-lib-sky bg-lib-light px-2 py-1 rounded-full whitespace-nowrap">
                                                    {{ $asset->categories->first()->name }}
                                                </span>
                                            @endif
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                 class="h-4 w-4 text-slate-300 flex-shrink-0 transition-transform"
                                                 :class="open ? 'rotate-90 text-lib-sky' : ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </div>

                                    @if($isAdmin)
                                        <div x-show="open" x-transition x-cloak class="bg-slate-50/70 border-t border-slate-100 px-4 py-4">
                                            <form method="POST" action="{{ route('collections.media.update', [$collection, $asset]) }}" class="space-y-4">
                                                @csrf
                                                @method('PUT')
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                    <div class="sm:col-span-2">
                                                        <label for="media-title-{{ $asset->id }}" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Title</label>
                                                        <input id="media-title-{{ $asset->id }}" type="text" name="title" value="{{ old('title', $asset->title) }}" required
                                                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/20">
                                                    </div>

                                                    <div>
                                                        <label for="media-date-{{ $asset->id }}" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date</label>
                                                        <input id="media-date-{{ $asset->id }}" type="date" name="date" value="{{ old('date', $asset->date?->format('Y-m-d')) }}"
                                                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/20">
                                                    </div>

                                                    @if($asset->file_url)
                                                        <div class="sm:col-span-2">
                                                            <label for="media-url-{{ $asset->id }}" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Link URL</label>
                                                            <input id="media-url-{{ $asset->id }}" type="url" name="file_url" value="{{ old('file_url', $asset->file_url) }}"
                                                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/20">
                                                        </div>
                                                    @endif

                                                    <div class="sm:col-span-2">
                                                        <label for="media-location-{{ $asset->id }}" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Location <span class="font-normal normal-case text-slate-400">(optional)</span></label>
                                                        <input id="media-location-{{ $asset->id }}" type="text" name="location" value="{{ old('location', $location) }}" maxlength="255"
                                                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/20">
                                                    </div>

                                                    <div class="sm:col-span-2">
                                                        <label for="media-description-{{ $asset->id }}" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Description</label>
                                                        <textarea id="media-description-{{ $asset->id }}" name="description" rows="3"
                                                                  class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/20 resize-y">{{ old('description', $asset->description) }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="flex flex-wrap items-center justify-end gap-2 pt-1">
                                                    <button type="button" @click="open = false"
                                                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-white transition-colors">
                                                        Cancel
                                                    </button>
                                                    <button type="submit"
                                                            class="px-4 py-2 rounded-xl bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-sm">
                                                        Save Changes
                                                    </button>
                                                </div>
                                            </form>

                                            <div class="mt-3 pt-3 border-t border-slate-200 flex flex-wrap items-center gap-2">
                                                @php
                                                    $mediaUrl = $asset->file_url
                                                        ? $asset->file_url
                                                        : ($asset->file_path ? asset('storage/' . $asset->file_path) : null);
                                                @endphp
                                                <button type="button"
                                                        @click="$dispatch('open-preview', {{ $loop->index }})"
                                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-lib-sky hover:text-lib-navy text-xs font-bold transition-colors">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    View Media
                                                </button>

                                                @if($asset->file_path)
                                                    <a href="{{ asset('storage/' . $asset->file_path) }}"
                                                       download="{{ $asset->title }}"
                                                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-lib-sky hover:text-lib-navy text-xs font-bold transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        Download Media
                                                    </a>
                                                @endif

                                                <form method="POST" action="{{ route('collections.media.destroy', [$collection, $asset]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="confirmDeleteCollectionMedia(event)"
                                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-red-100 bg-white text-red-600 hover:bg-red-50 hover:border-red-200 text-xs font-bold transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                                        Delete Media
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                @if($assets->total() > 0 && $assets->hasPages())
                    <div class="bg-white rounded-lg px-6 py-4 shadow-sm border border-slate-100 flex items-center justify-between gap-4">
                        <p class="text-sm text-slate-500 font-medium">
                            Showing
                            <span class="font-bold">{{ $assets->firstItem() }}</span>
                            &ndash;
                            <span class="font-bold">{{ $assets->lastItem() }}</span>
                            of
                            <span class="font-bold">{{ $assets->total() }}</span>
                            results
                        </p>
                        <div class="flex items-center">
                            {{ $assets->onEachSide(1)->links() }}
                        </div>
                    </div>
                @endif
            </div>

            {{-- ===== RIGHT: DETAILS ===== --}}
            <aside class="lg:col-span-4 space-y-4">

                {{-- Breakdown card --}}
                <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Details</h2>
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Total items</dt>
                            <dd class="font-semibold text-slate-800">{{ $counts['total'] }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Photos</dt>
                            <dd class="font-semibold text-slate-800">{{ $counts['photo'] }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Videos</dt>
                            <dd class="font-semibold text-slate-800">{{ $counts['video'] }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Books</dt>
                            <dd class="font-semibold text-slate-800">{{ $counts['ebook'] }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-3 pt-3 border-t border-slate-100">
                            <dt class="text-slate-500 font-medium">Created</dt>
                            <dd class="font-semibold text-slate-800">{{ $collection->created_at?->format('d M Y') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Actions --}}
                @php
                    $actionBase = 'w-full inline-flex items-center justify-between gap-3 px-4 py-2 rounded-lg text-xs font-bold transition-all border group';
                    $iconBase = 'w-9 h-9 rounded-xl inline-flex items-center justify-center flex-shrink-0 transition-colors';
                @endphp
                @if($isAdmin)
                    <div class="bg-white rounded-lg shadow-sm border border-slate-100 p-6"
                         @if($isAdmin)
                         x-data="{
                            mediaFormOpen: @js($errors->any()),
                            sourceMode: @js(old('source_mode', 'file')),
                            uploadType: @js(old('upload_type', 'photo')),
                            files: [],
                            acceptMap: {
                                photo: 'image/*',
                                video: 'video/*',
                                ebook: '.pdf,.doc,.docx,.xls,.xlsx',
                            },
                            get accept() {
                                return this.acceptMap[this.uploadType] || '';
                            },
                            changeType(type) {
                                this.uploadType = type;
                                this.files = [];
                                if (this.$refs.files) this.$refs.files.value = '';
                            },
                            changeSource(mode) {
                                this.sourceMode = mode;
                                this.files = [];
                                if (this.$refs.files) this.$refs.files.value = '';
                            },
                            addFiles(event) {
                                const incoming = Array.from(event.target.files || []);
                                this.files = incoming.map(f => ({
                                    name: f.name,
                                    size: f.size,
                                    kind: this.kindOf(f.name),
                                }));
                            },
                            kindOf(name) {
                                const ext = (name.split('.').pop() || '').toLowerCase();
                                if (['jpg','jpeg','png','gif','webp','bmp'].includes(ext)) return 'photo';
                                if (['mp4','webm','mov','avi','mkv','m4v'].includes(ext)) return 'video';
                                if (['pdf','doc','docx','xls','xlsx'].includes(ext)) return 'ebook';
                                return 'other';
                            },
                            prettySize(bytes) {
                                if (bytes < 1024) return bytes + ' B';
                                if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                                return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                            }
                         }"
                         @endif>
                        <div class="mb-4">
                            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Actions</h2>
                            <p class="text-xs text-slate-400 mt-1">Manage this collection.</p>
                        </div>
                        <div class="flex flex-col gap-3">
                            @if($isAdmin)
                                <a href="{{ route('collections.edit', $collection) }}"
                                   class="{{ $actionBase }} bg-lib-navy hover:bg-lib-sky text-white border-lib-navy hover:border-lib-sky shadow-md shadow-lib-navy/10">
                                    <span class="inline-flex items-center gap-3 min-w-0">
                                        <span class="{{ $iconBase }} bg-white/15 text-white">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </span>
                                        <span>Edit Collection</span>
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4 opacity-70 group-hover:translate-x-0.5 transition-transform"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endif
                            @if($isAdmin)
                                <div>
                                    <button type="button"
                                            @click="mediaFormOpen = !mediaFormOpen"
                                            class="{{ $actionBase }} bg-white hover:bg-slate-50 text-slate-700 hover:text-lib-navy border-slate-200 hover:border-lib-sky">
                                        <span class="inline-flex items-center gap-3 min-w-0">
                                            <span class="{{ $iconBase }} bg-slate-50 text-slate-500 group-hover:bg-lib-light group-hover:text-lib-navy">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                            </span>
                                            <span>Add Media</span>
                                        </span>
                                        <span class="inline-flex items-center gap-2">
                                            <span x-show="mediaFormOpen && sourceMode === 'file'" x-cloak class="hidden sm:inline-flex text-[10px] font-bold text-slate-400">
                                                Max 25 MB
                                            </span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                 class="h-4 w-4 text-slate-300 group-hover:text-lib-sky transition-all"
                                                 :class="mediaFormOpen ? 'rotate-180' : ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </span>
                                    </button>

                                    <div x-show="mediaFormOpen" x-cloak x-transition.opacity class="mt-3 rounded-lg border border-slate-200 bg-slate-50/60 p-4">
                                        <form method="POST" action="{{ route('collections.media.store', $collection) }}" enctype="multipart/form-data" class="space-y-4">
                                            @csrf

                                            <div class="grid grid-cols-1 gap-4">
                                                <div>
                                                    <label for="source_mode" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                                                        Add method
                                                    </label>
                                                    <select id="source_mode" name="source_mode"
                                                            x-model="sourceMode"
                                                            @change="changeSource($event.target.value)"
                                                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-sm font-semibold text-lib-navy bg-white transition-colors appearance-none">
                                                        <option value="file" {{ old('source_mode', 'file') === 'file' ? 'selected' : '' }}>Upload file</option>
                                                        <option value="link" {{ old('source_mode', 'file') === 'link' ? 'selected' : '' }}>Add links</option>
                                                    </select>
                                                    <p class="text-xs text-slate-400 mt-1.5" x-text="sourceMode === 'file' ? 'Choose files from your device' : 'Paste direct media URLs'"></p>
                                                </div>

                                                <div>
                                                    <label for="upload_type" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                                                        Media type
                                                    </label>
                                                    <select id="upload_type" name="upload_type"
                                                            x-model="uploadType"
                                                            @change="changeType($event.target.value)"
                                                            class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-sm font-semibold text-lib-navy bg-white transition-colors appearance-none">
                                                        <option value="photo" {{ old('upload_type', 'photo') === 'photo' ? 'selected' : '' }}>Photos</option>
                                                        <option value="video" {{ old('upload_type', 'photo') === 'video' ? 'selected' : '' }}>Videos</option>
                                                        <option value="ebook" {{ old('upload_type', 'photo') === 'ebook' ? 'selected' : '' }}>Books</option>
                                                    </select>
                                                    <p class="text-xs text-slate-400 mt-1.5" x-text="{ photo: 'JPG, PNG, GIF, WebP', video: 'MP4, WebM, MOV', ebook: 'PDF, Word, Excel' }[uploadType]"></p>
                                                </div>
                                            </div>

                                            <div class="rounded-lg border border-slate-200 bg-white p-4" x-show="sourceMode === 'file'" x-cloak>
                                                <label for="files" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
                                                    Files
                                                </label>
                                                <input id="files" type="file" name="files[]" multiple x-ref="files"
                                                       :required="sourceMode === 'file'"
                                                       @change="addFiles($event)"
                                                       :accept="accept"
                                                       class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-lib-light file:text-lib-navy hover:file:bg-lib-sky hover:file:text-white transition-colors">
                                                <p class="text-xs text-slate-400 mt-1.5">Titles are automatically derived from filenames.</p>

                                                <template x-if="files.length > 0">
                                                    <ul class="mt-4 divide-y divide-slate-100 rounded-xl border border-slate-200 bg-slate-50/50 overflow-hidden">
                                                        <template x-for="(file, index) in files" :key="index">
                                                            <li class="flex items-center justify-between gap-3 px-3 py-2 text-xs">
                                                                <div class="flex items-center gap-2 min-w-0">
                                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider"
                                                                          :class="{
                                                                            'bg-emerald-100 text-emerald-700': file.kind === 'photo',
                                                                            'bg-purple-100 text-purple-700': file.kind === 'video',
                                                                            'bg-amber-100 text-amber-700': file.kind === 'ebook',
                                                                            'bg-red-100 text-red-700': file.kind === 'other',
                                                                          }" x-text="file.kind"></span>
                                                                    <span class="truncate font-semibold text-slate-700" x-text="file.name"></span>
                                                                </div>
                                                                <span class="text-slate-400 whitespace-nowrap" x-text="prettySize(file.size)"></span>
                                                            </li>
                                                        </template>
                                                    </ul>
                                                </template>
                                            </div>

                                            <div class="rounded-lg border border-slate-200 bg-white p-4" x-show="sourceMode === 'link'" x-cloak>
                                                <label for="links" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
                                                    Links
                                                </label>
                                                <textarea id="links" name="links" rows="4"
                                                          :required="sourceMode === 'link'"
                                                          placeholder="https://example.com/media-file.pdf"
                                                          class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-sm text-slate-800 bg-white transition-colors resize-y">{{ old('links') }}</textarea>
                                                <p class="text-xs text-slate-400 mt-1.5">Add one direct media URL per line. Links must match the selected media type.</p>
                                            </div>

                                            <div class="relative">
                                                <input id="location" type="text" name="location" value="{{ old('location') }}" maxlength="255" placeholder=" "
                                                       class="peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors">
                                                <label for="location" class="absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none">
                                                    Location <span class="font-normal text-slate-400">(optional)</span>
                                                </label>
                                            </div>

                                            <div class="flex justify-end">
                                                <button type="submit"
                                                        class="px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md">
                                                    <span x-text="sourceMode === 'file' ? 'Upload files' : 'Add links'"></span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('collections.status.update', $collection) }}"
                                      class="{{ $actionBase }} bg-white hover:bg-slate-50 text-slate-700 hover:text-lib-navy border-slate-200 hover:border-lib-sky">
                                    @csrf
                                    @method('PUT')
                                    <span class="inline-flex items-center gap-3 min-w-0 flex-grow">
                                        <span class="{{ $iconBase }} bg-slate-50 text-slate-500 group-hover:bg-lib-light group-hover:text-lib-navy">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>
                                        </span>
                                        <span>Status</span>
                                    </span>
                                    <span class="inline-flex items-center gap-2">
                                        <select name="status"
                                                onchange="this.form.submit()"
                                                class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/20">
                                            @foreach(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'] as $statusValue => $statusLabel)
                                                <option value="{{ $statusValue }}" {{ old('status', $collection->status ?? 'draft') === $statusValue ? 'selected' : '' }}>{{ $statusLabel }}</option>
                                            @endforeach
                                        </select>
                                    </span>
                                </form>
                            @endif

                            @if($isAdmin)
                                <div class="pt-3 mt-1 border-t border-slate-100">
                                    <form method="POST" action="{{ route('collections.destroy', $collection) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="confirmDeleteCollection(event)"
                                                class="{{ $actionBase }} bg-white hover:bg-red-50 text-red-600 border-red-100 hover:border-red-200">
                                            <span class="inline-flex items-center gap-3 min-w-0">
                                                <span class="{{ $iconBase }} bg-red-50 text-red-500 group-hover:bg-red-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                                </span>
                                                <span>Delete Collection</span>
                                            </span>
                                            <span class="text-[10px] font-bold text-red-300">Danger</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>

    <div x-show="previewOpen"
         x-cloak
         x-transition.opacity
         @click.self="closePreview()"
         class="fixed inset-0 z-50 bg-slate-950/95 flex flex-col">
        <div class="h-16 flex items-center justify-between gap-4 px-4 sm:px-6 border-b border-white/10">
            <div class="min-w-0">
                <p class="text-[10px] font-bold uppercase tracking-widest text-lib-sky" x-text="item?.date"></p>
                <h2 class="text-base sm:text-lg font-bold text-white truncate" x-text="item?.title"></h2>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="items.length > 1"
                      class="hidden sm:inline-flex px-3 py-1 rounded-full bg-white/10 text-white/70 text-xs font-bold">
                    <span x-text="current + 1"></span>
                    <span class="px-1">/</span>
                    <span x-text="items.length"></span>
                </span>
                <button type="button" @click="closePreview()"
                        class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                        aria-label="Close preview">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <div class="relative flex-1 min-h-0 flex items-center justify-center p-4 sm:p-6">
            <button type="button" @click="prevPreview()" x-show="items.length > 1"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                    aria-label="Previous item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>

            <div class="w-full h-full flex items-center justify-center">
                <template x-if="item && item.type === 'photo'">
                    <img :src="item.src || item.thumb" :alt="item.title"
                         class="max-w-full max-h-full object-contain rounded-xl shadow-lg">
                </template>

                <template x-if="item && item.type === 'video'">
                    <video :src="item.src" controls autoplay
                           class="w-full h-full max-w-6xl max-h-full object-contain rounded-xl bg-black shadow-lg"></video>
                </template>

                <template x-if="item && item.type === 'ebook'">
                    <iframe :src="item.src ? item.src + '#toolbar=1&navpanes=1&scrollbar=1&view=FitH' : ''"
                            class="w-full h-full max-w-6xl rounded-xl border border-white/10 bg-white shadow-lg"
                            allow="fullscreen"></iframe>
                </template>
            </div>

            <button type="button" @click="nextPreview()" x-show="items.length > 1"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors"
                    aria-label="Next item">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-white/10">
            <div class="max-w-5xl mx-auto text-center">
                <p class="text-sm text-white/70 line-clamp-2" x-show="item?.description" x-text="item?.description"></p>
                <p class="text-xs text-white/50" x-show="item?.location" x-text="item?.location"></p>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeleteCollection(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        Swal.fire({
            title: 'Delete this collection?',
            text: 'This will permanently delete the collection and all media attached to it. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            background: '#fff5f5',
            customClass: { popup: 'rounded-lg border-2 border-red-200 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }

    function confirmDeleteCollectionMedia(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        Swal.fire({
            title: 'Remove this file?',
            text: 'This deletes the uploaded file and its media record from the collection.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, remove',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            background: '#fff5f5',
            customClass: { popup: 'rounded-lg border-2 border-red-200 shadow-lg' }
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }
</script>
@endsection
