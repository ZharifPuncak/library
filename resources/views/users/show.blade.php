@extends('layouts.app')

@section('title', $asset->title)

@section('content')
@php
    $type        = strtolower($asset->type);
    $typeLabels  = ['photo' => 'Photo', 'video' => 'Video', 'ebook' => 'Book'];
    $typeLabel   = $typeLabels[$type] ?? ucfirst($asset->type);
    $defaultThumb = asset('images/logo.png');

    // Resolve the actual file URL (external link OR stored upload)
    $fileUrl = $asset->resource_url ?? ($asset->file_path ? asset('storage/' . $asset->file_path) : null);

    $typeBackLabels = ['photo' => 'photos', 'video' => 'videos', 'ebook' => 'books'];

    if (isset($isCollectionContext) && $isCollectionContext) {
        $backRoute = route('collections.show', $collection ?? $collectionName);
        $backLabel = 'Back to collections';
    } elseif (in_array($type, ['photo', 'video', 'ebook'], true)) {
        $backRoute = route('media.index', ['type' => $type]);
        $backLabel = 'Back to ' . ($typeBackLabels[$type] ?? 'media');
    } else {
        $backRoute = route('media.index');
        $backLabel = 'Back to media';
    }
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-5rem)]">
    <div class="w-full px-4 sm:px-6 lg:px-8 py-8">

        {{-- Top bar: Back + Title --}}
        <div class="flex flex-wrap items-center gap-4 mb-6">
            <a href="{{ $backRoute }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                {{ $backLabel }}
            </a>

            @if(isset($collectionName) && $collectionName)
                <a href="{{ route('collections.show', $collection ?? $collectionName) }}"
                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold text-lib-sky bg-lib-light hover:bg-lib-sky hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    {{ $collectionName }}
                </a>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ===== LEFT: VIEWER ===== --}}
            <div class="lg:col-span-8">
                {{-- Title block (moved above viewer) --}}
                @php
                    $thumbUrl = $asset->thumbnail_path
                        ? asset('storage/' . $asset->thumbnail_path)
                        : ($type === 'photo' && $fileUrl ? $fileUrl : null);
                @endphp
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-4 flex items-center gap-5">
                    <div class="w-16 h-20 sm:w-20 sm:h-24 flex-shrink-0 rounded-2xl overflow-hidden bg-slate-100 border border-slate-100">
                        @if($thumbUrl)
                            <img id="mainThumb" src="{{ $thumbUrl }}" alt="{{ $asset->title }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-2','bg-white');">
                        @else
                            <img id="mainThumb" src="{{ asset('images/logo.png') }}" alt="{{ $asset->title }}"
                                 class="w-full h-full object-contain p-2 bg-white">
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-lib-sky mb-1" id="mainDate">
                            {{ \Carbon\Carbon::parse($asset->date ?? $asset->created_at)->format('d F Y') }}
                        </p>
                        <h1 id="mainTitle" class="text-2xl md:text-3xl font-bold text-lib-navy leading-tight truncate">{{ $asset->title }}</h1>
                        <span class="inline-flex items-center px-2.5 py-1 mt-2 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $typeLabel }}
                        </span>
                    </div>
                </div>

                <div id="fullscreenContainer" class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">

                    <style>
                        #fullscreenContainer:fullscreen { background: #000; padding: 1rem; }
                        #fullscreenContainer:fullscreen .viewer { height: 100vh; max-height: 100vh; padding: 0; background: transparent; }
                        #fullscreenContainer:fullscreen #mainImage { max-height: 90vh; max-width: 95vw; }
                        #fullscreenContainer:fullscreen #ebookFrame { height: 100vh; }
                    </style>

                    <div class="viewer relative">
                        {{-- Fullscreen button (photos + ebooks only) --}}
                        @if(in_array($type, ['photo', 'image', 'ebook'], true))
                            <button id="fullscreenBtn" onclick="toggleFullScreen()" type="button"
                                    class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/95 shadow-md border border-slate-200 text-slate-600 hover:bg-lib-navy hover:text-white hover:border-lib-navy flex items-center justify-center transition-colors"
                                    title="Fullscreen">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            </button>
                        @endif

                        {{-- Photo — full-bleed --}}
                        <img id="mainImage" src="{{ $fileUrl ?? $defaultThumb }}" alt="{{ $asset->title }}"
                             class="w-full h-auto object-cover {{ in_array($type, ['photo', 'image'], true) ? 'block' : 'hidden' }}">

                        {{-- Video --}}
                        <div class="{{ $type === 'video' ? 'block' : 'hidden' }} bg-slate-50 flex items-center justify-center p-4 min-h-[480px]">
                            <video id="mainVideo" src="{{ $type === 'video' ? $fileUrl : '' }}"
                                   controls
                                   class="max-w-full max-h-[480px] rounded-xl"></video>
                        </div>

                        {{-- Ebook (PDF) --}}
                        <div id="mainEbook" class="{{ $type === 'ebook' ? 'block' : 'hidden' }} bg-slate-50 p-4">
                            <iframe id="ebookFrame"
                                    src="{{ $type === 'ebook' && $fileUrl ? $fileUrl . '#toolbar=1&navpanes=1&scrollbar=1&view=FitH' : '' }}"
                                    class="w-full h-[600px] rounded-xl border border-slate-200 bg-white"
                                    allow="fullscreen"></iframe>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ===== RIGHT: DETAILS ===== --}}
            <aside class="lg:col-span-4 space-y-4">

                {{-- Metadata card --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Details</h2>

                    @php
                        $statusClasses = match($asset->status) {
                            'published' => 'bg-emerald-100 text-emerald-700',
                            'archived'  => 'bg-slate-200 text-slate-700',
                            default     => 'bg-amber-100 text-amber-700', // draft
                        };
                    @endphp
                    <dl class="space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Type</dt>
                            <dd class="font-semibold text-slate-800 capitalize">{{ $typeLabel }}</dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Status</dt>
                            <dd>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $statusClasses }}">
                                    {{ ucfirst($asset->status ?? 'draft') }}
                                </span>
                            </dd>
                        </div>
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Date created</dt>
                            <dd class="font-semibold text-slate-800">{{ $asset->created_at?->format('d M Y') }}</dd>
                        </div>
                        @if(!empty($asset->date))
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500 font-medium">Date event</dt>
                                <dd class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($asset->date)->format('d M Y') }}</dd>
                            </div>
                        @endif
                        <div class="flex items-start justify-between gap-3">
                            <dt class="text-slate-500 font-medium">Views</dt>
                            <dd class="font-semibold text-slate-800">{{ number_format((int) $asset->getDetail('views', 0)) }}</dd>
                        </div>
                        @if($type === 'ebook' && $asset->getDetail('location'))
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500 font-medium">Location</dt>
                                <dd class="font-semibold text-slate-800 text-right">{{ $asset->getDetail('location') }}</dd>
                            </div>
                        @endif
                        @if($asset->getDetail('author'))
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-slate-500 font-medium">Author</dt>
                                <dd class="font-semibold text-slate-800 text-right">{{ $asset->getDetail('author') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Categories --}}
                @if($asset->categories && $asset->categories->isNotEmpty())
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Categories</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($asset->categories as $category)
                                <a href="{{ route('media.index', ['categories' => [$category->id]]) }}"
                                   class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200 hover:bg-lib-navy hover:text-white hover:border-lib-navy transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Tags --}}
                @if($asset->tags && $asset->tags->isNotEmpty())
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Tags</h2>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($asset->tags as $tag)
                                <a href="{{ route('media.index', ['tags' => [$tag->id]]) }}"
                                   class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-50 text-slate-600 border border-slate-200 hover:bg-lib-sky hover:text-white hover:border-lib-sky transition-colors">
                                    #{{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Collections --}}
                @if(isset($collectionName) && $collectionName)
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Collection</h2>
                        <a href="{{ route('collections.show', $collection ?? $collectionName) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl bg-lib-light text-lib-navy hover:bg-lib-sky hover:text-white text-sm font-bold transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            {{ $collectionName }}
                        </a>
                    </div>
                @endif

                {{-- Actions --}}
                @php
                    // Download only when the file was uploaded to local storage (not an external link).
                    $canDownload  = $type === 'ebook' && !empty($asset->file_path);
                    $downloadUrl  = $canDownload ? asset('storage/' . $asset->file_path) : null;
                    $isAdmin      = auth()->user()?->isAdmin();
                    $inMyList     = auth()->check()
                        ? auth()->user()->myList()->whereKey($asset->id)->exists()
                        : false;
                    $showActions  = $canDownload || $isAdmin || auth()->check();
                @endphp
                @if($showActions)
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Actions</h2>
                        <div class="flex flex-col gap-2">
                            @if($isAdmin)
                                <a href="{{ route('media.edit', $asset) }}"
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit Media
                                </a>
                            @endif
                            @if($canDownload)
                                <a id="ebookLink" href="{{ $downloadUrl }}" download
                                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-slate-600 hover:text-lib-navy hover:border-lib-navy text-xs font-bold transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                            @endif

                            @auth
                                @if($inMyList)
                                    <form method="POST" action="{{ route('mylist.destroy', $asset) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-lib-light text-lib-navy hover:bg-lib-sky hover:text-white text-xs font-bold transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4"><path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                            Remove from My List
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('mylist.store', $asset) }}">
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
                                <form method="POST" action="{{ route('media.destroy', $asset) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="confirmDeleteMedia(event)"
                                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-red-50 hover:bg-red-500 text-red-600 hover:text-white text-xs font-bold transition-colors border border-red-100 hover:border-red-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endif

                <script>
                    function confirmDeleteMedia(event) {
                        event.preventDefault();
                        const form = event.target.closest('form');
                        Swal.fire({
                            title: 'Delete this media?',
                            text: 'This cannot be undone. The file and its metadata will be removed.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true,
                            background: '#fff5f5',
                            customClass: {
                                popup: 'rounded-2xl border-2 border-red-200 shadow-2xl'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) form.submit();
                        });
                    }
                </script>
            </aside>
        </div>

        {{-- ===== RELATED ITEMS ===== --}}
        @if(($relatedAssets ?? collect())->isNotEmpty())
            <div class="mt-8 bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                <div class="flex items-start justify-between gap-3 mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-lib-navy">
                            @if(isset($isCollectionContext) && $isCollectionContext)
                                More in this collection
                            @else
                                Related items
                            @endif
                        </h2>
                        @if(!(isset($isCollectionContext) && $isCollectionContext))
                            <p class="text-xs text-slate-500 mt-1">
                                Matched by shared
                                @if(($sharedCategories ?? collect())->isNotEmpty())
                                    categories:
                                    @foreach($sharedCategories as $c)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-50 text-slate-600 border border-slate-200">{{ $c->name }}</span>
                                    @endforeach
                                @endif
                                @if(($sharedTags ?? collect())->isNotEmpty())
                                    and tags:
                                    @foreach($sharedTags as $t)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-lib-light text-lib-sky border border-lib-sky/20">#{{ $t->name }}</span>
                                    @endforeach
                                @endif
                            </p>
                        @endif
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full whitespace-nowrap">{{ $relatedAssets->count() }} items</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                    @foreach($relatedAssets as $index => $rel)
                        @php
                            $relType  = strtolower($rel->type);
                            $relThumb = $rel->thumbnail_path
                                ? asset('storage/' . $rel->thumbnail_path)
                                : ($relType === 'photo' && $rel->file_path ? asset('storage/' . $rel->file_path) : ($relType === 'photo' && $rel->file_url ? $rel->file_url : $defaultThumb));
                            $isFallback = $relThumb === $defaultThumb;
                        @endphp
                        <button type="button" onclick="jumpTo({{ $index + 1 }})"
                                class="gallery-item group text-left">
                            <div class="aspect-[3/4] rounded-xl overflow-hidden bg-slate-100 border-2 border-transparent group-hover:border-lib-sky transition-all relative">
                                <img src="{{ $relThumb }}" alt="{{ $rel->title }}"
                                     class="w-full h-full {{ $isFallback ? 'object-contain p-4 bg-white' : 'object-cover' }} group-hover:scale-105 transition-transform">
                                <span class="now-viewing hidden absolute top-2 left-2 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-lib-sky text-white shadow">Viewing</span>
                            </div>
                            <div class="mt-2 px-1">
                                <p class="text-xs font-bold text-slate-800 line-clamp-1 group-hover:text-lib-sky transition-colors">{{ $rel->title }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $typeLabels[$relType] ?? ucfirst($rel->type) }}</p>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // ======= Gallery state (current + related) =======
    const gallery = [
        {
            id: {{ $asset->id }},
            title: @json($asset->title),
            type:  @json(strtolower($asset->type)),
            date:  "{{ \Carbon\Carbon::parse($asset->date ?? $asset->created_at)->format('d F Y') }}",
            src:   @json($fileUrl ?? ''),
        },
        @foreach($relatedAssets ?? [] as $rel)
            @php
                $relType = strtolower($rel->type);
                $relSrc  = $rel->resource_url ?? ($rel->file_path ? asset('storage/' . $rel->file_path) : '');
            @endphp
            {
                id: {{ $rel->id }},
                title: @json($rel->title),
                type:  @json($relType),
                date:  "{{ \Carbon\Carbon::parse($rel->date ?? $rel->created_at)->format('d F Y') }}",
                src:   @json($relSrc),
            },
        @endforeach
    ];

    let currentIndex = 0;

    function updateMain() {
        const item   = gallery[currentIndex];
        const img    = document.getElementById('mainImage');
        const video  = document.getElementById('mainVideo');
        const ebook  = document.getElementById('mainEbook');
        const frame  = document.getElementById('ebookFrame');
        const fsBtn  = document.getElementById('fullscreenBtn');

        // Reset
        img.classList.add('hidden');
        video.classList.add('hidden');
        video.pause();
        ebook.classList.add('hidden');
        if (fsBtn) fsBtn.classList.add('hidden');

        if (item.type === 'video') {
            video.classList.remove('hidden');
            video.src = item.src;
            video.load();
        } else if (item.type === 'ebook') {
            ebook.classList.remove('hidden');
            frame.src = item.src + '#toolbar=1&navpanes=1&scrollbar=1&view=FitH';
            if (fsBtn) fsBtn.classList.remove('hidden');
        } else {
            img.classList.remove('hidden');
            img.src = item.src;
            if (fsBtn) fsBtn.classList.remove('hidden');
        }

        document.getElementById('mainTitle').innerText = item.title;
        document.getElementById('mainDate').innerText  = item.date;

        // Active indicator on related row
        document.querySelectorAll('.gallery-item').forEach((box, i) => {
            const tile  = box.querySelector('.aspect-\\[3\\/4\\]');
            const badge = box.querySelector('.now-viewing');
            if (i + 1 === currentIndex) {
                tile.classList.add('border-lib-sky', 'ring-2', 'ring-lib-sky/30');
                badge?.classList.remove('hidden');
            } else {
                tile.classList.remove('border-lib-sky', 'ring-2', 'ring-lib-sky/30');
                badge?.classList.add('hidden');
            }
        });
    }

    function jumpTo(i) { currentIndex = i; updateMain(); }

    function toggleFullScreen() {
        const c = document.getElementById('fullscreenContainer');
        if (!document.fullscreenElement) {
            c.requestFullscreen?.() || c.webkitRequestFullscreen?.() || c.msRequestFullscreen?.();
        } else {
            document.exitFullscreen?.();
        }
    }
</script>
@endsection
