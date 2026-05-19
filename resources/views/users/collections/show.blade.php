@extends('layouts.app')

@section('title', $collectionName . ' - Collection')

@section('content')
@php
    $defaultThumb = asset('images/logo.png');
    $typeLabels   = ['photo' => 'Photo', 'video' => 'Video', 'ebook' => 'Book'];

    // Build a JSON payload for the inline previewer.
    $previewItems = $assets->getCollection()->map(function ($asset) use ($defaultThumb, $collection) {
        $type   = strtolower($asset->type);
        $src    = $asset->file_url ?: ($asset->file_path ? asset('storage/' . $asset->file_path) : null);
        $thumb  = $asset->thumbnail_path
            ? asset('storage/' . $asset->thumbnail_path)
            : ($type === 'photo' ? $src : null);
        return [
            'id'    => $asset->id,
            'title' => $asset->title,
            'type'  => $type,
            'src'   => $src,
            'thumb' => $thumb ?: $defaultThumb,
            'date'  => $asset->created_at?->format('M j, Y'),
            'url'   => route('collections.media.show', [$collection, $asset]),
        ];
    })->values();
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-5rem)]">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-8">

        {{-- Back --}}
        <a href="{{ route('collections.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Back to collections
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ===== LEFT: TITLE + MEDIA LIST ===== --}}
            <div class="lg:col-span-8 space-y-4">

                {{-- Title card --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 flex items-center gap-5">
                    <div class="w-16 h-20 sm:w-20 sm:h-24 flex-shrink-0 rounded-2xl overflow-hidden bg-lib-light flex items-center justify-center text-lib-sky">
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
                    </div>
                </div>

                {{-- Previewer --}}
                @if($previewItems->isNotEmpty())
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden"
                         x-data="{
                             items: @js($previewItems),
                             current: 0,
                             get item() { return this.items[this.current]; },
                             prev() { this.current = (this.current - 1 + this.items.length) % this.items.length; },
                             next() { this.current = (this.current + 1) % this.items.length; },
                             jump(i) { this.current = i; }
                         }"
                         @keydown.left.window="prev()"
                         @keydown.right.window="next()">

                        <div class="relative">
                            {{-- Prev --}}
                            <button type="button" @click="prev()" x-show="items.length > 1"
                                    class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white shadow-md border border-slate-200 text-slate-600 hover:bg-lib-navy hover:text-white hover:border-lib-navy flex items-center justify-center transition-colors"
                                    aria-label="Previous">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            {{-- Next --}}
                            <button type="button" @click="next()" x-show="items.length > 1"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 rounded-full bg-white shadow-md border border-slate-200 text-slate-600 hover:bg-lib-navy hover:text-white hover:border-lib-navy flex items-center justify-center transition-colors"
                                    aria-label="Next">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>

                            {{-- Counter --}}
                            <span x-show="items.length > 1" x-text="(current + 1) + ' / ' + items.length"
                                  class="absolute top-4 right-4 z-20 px-3 py-1 rounded-full text-xs font-bold bg-white/95 text-slate-700 shadow border border-slate-200"></span>

                            {{-- Photo — full-bleed --}}
                            <template x-if="item && item.type === 'photo'">
                                <img :src="item.src || item.thumb" :alt="item.title"
                                     class="block w-full h-auto object-cover">
                            </template>
                            {{-- Video --}}
                            <template x-if="item && item.type === 'video'">
                                <div class="bg-slate-50 flex items-center justify-center p-4 min-h-[480px]">
                                    <video :src="item.src" controls
                                           class="max-w-full max-h-[480px] rounded-xl"></video>
                                </div>
                            </template>
                            {{-- Book --}}
                            <template x-if="item && item.type === 'ebook'">
                                <div class="bg-slate-50 p-4">
                                    <iframe :src="item.src ? item.src + '#toolbar=1&navpanes=1&scrollbar=1&view=FitH' : ''"
                                            class="w-full h-[600px] rounded-xl border border-slate-200 bg-white"></iframe>
                                </div>
                            </template>
                        </div>

                        <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-lib-sky mb-0.5" x-text="item?.date"></p>
                                <h3 class="text-base font-bold text-lib-navy truncate" x-text="item?.title"></h3>
                            </div>
                            <a :href="item?.url"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-lib-sky hover:bg-lib-navy text-white text-xs font-bold transition-colors whitespace-nowrap">
                                Open
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-3.5 w-3.5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        {{-- Thumbnail strip --}}
                        <div class="px-4 py-3 border-t border-slate-100 overflow-x-auto" x-show="items.length > 1">
                            <div class="flex items-center gap-2 min-w-max">
                                <template x-for="(it, i) in items" :key="it.id">
                                    <button type="button" @click="jump(i)"
                                            class="w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100 border-2 transition-colors"
                                            :class="i === current ? 'border-lib-sky' : 'border-transparent hover:border-slate-300'">
                                        <img :src="it.thumb" :alt="it.title" class="w-full h-full object-cover">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Media list --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-base font-bold text-lib-navy">Media in this collection</h2>
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">{{ $assets->total() }} {{ Str::plural('item', $assets->total()) }}</span>
                    </div>

                    @if($assets->isEmpty())
                        <div class="py-12 text-center text-slate-400 text-sm">No media in this collection yet.</div>
                    @else
                        <div class="flex flex-col divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                            @foreach($assets as $asset)
                                @php
                                    $type        = strtolower($asset->type);
                                    $typeLabel   = $typeLabels[$type] ?? ucfirst($asset->type);
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
                                <a href="{{ route('collections.media.show', [$collection, $asset]) }}"
                                   class="group flex items-center gap-4 p-3 hover:bg-slate-50 transition-colors">
                                    <div class="w-14 h-14 flex-shrink-0 rounded-xl overflow-hidden bg-slate-100">
                                        <img src="{{ $thumbUrl }}" alt="{{ $asset->title }}"
                                             class="w-full h-full {{ $hasRealThumb ? 'object-cover' : 'object-contain p-1 bg-white' }}"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ $defaultThumb }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-1','bg-white');">
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <h3 class="text-sm font-bold text-slate-800 truncate group-hover:text-lib-sky transition-colors">{{ $asset->title }}</h3>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $typeLabel }} &middot; {{ $asset->created_at?->format('M j, Y') }}</p>
                                    </div>
                                    @if($asset->categories->isNotEmpty())
                                        <span class="hidden sm:inline-block text-[10px] font-bold text-lib-sky bg-lib-light px-2 py-1 rounded-full whitespace-nowrap">
                                            {{ $asset->categories->first()->name }}
                                        </span>
                                    @endif
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4 text-slate-300 flex-shrink-0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                @if($assets->total() > 0 && $assets->hasPages())
                    <div class="bg-white rounded-3xl px-6 py-4 shadow-sm border border-slate-100 flex items-center justify-between gap-4">
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
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
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
                    $hasDownloadable = \App\Models\Media::whereHas('details', fn($q) =>
                            $q->where('key', 'collection')->where('value', $collection->name))
                        ->whereNotNull('file_path')
                        ->exists();
                    $isAdmin = auth()->user()?->isAdmin();
                    $inMyList = auth()->check()
                        ? auth()->user()->myListCollections()->whereKey($collection->id)->exists()
                        : false;
                @endphp
                @if($hasDownloadable || $isAdmin || auth()->check())
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Actions</h2>
                        <div class="flex flex-col gap-2">
                            @if($isAdmin)
                                <a href="{{ route('collections.edit', $collection) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit Collection
                                </a>
                            @endif
                            @if($hasDownloadable)
                                <a href="{{ route('collections.download', $collection) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-600 hover:text-lib-navy hover:border-lib-navy text-xs font-bold transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download (.zip)
                                </a>
                            @endif

                            @auth
                                @if($inMyList)
                                    <form method="POST" action="{{ route('mylist.collections.destroy', $collection) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-lib-light text-lib-navy hover:bg-lib-sky hover:text-white text-xs font-bold transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                            Remove from My List
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('mylist.collections.store', $collection) }}">
                                        @csrf
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-600 hover:text-lib-navy hover:border-lib-navy text-xs font-bold transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                            Add to My List
                                        </button>
                                    </form>
                                @endif
                            @endauth
                            @if($isAdmin)
                                <form method="POST" action="{{ route('collections.destroy', $collection) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="confirmDeleteCollection(event)"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-red-50 hover:bg-red-500 text-red-600 hover:text-white text-xs font-bold transition-colors border border-red-100 hover:border-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        Delete Collection
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>

<script>
    function confirmDeleteCollection(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        Swal.fire({
            title: 'Delete this collection?',
            text: 'The collection tag will be removed from all media in it. The media files themselves will remain.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            background: '#fff5f5',
            customClass: { popup: 'rounded-2xl border-2 border-red-200 shadow-2xl' }
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }
</script>
@endsection
