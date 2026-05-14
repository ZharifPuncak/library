@extends('layouts.app')

@section('title', 'Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        {{-- Back Button --}}
        @if(isset($isCollectionContext) && $isCollectionContext)
            <a href="{{ route('collections.show', $collectionName) }}"
                class="inline-flex items-center gap-3 text-slate-500 hover:text-lib-navy font-black transition-all group active:scale-95">
                <div class="bg-white p-2.5 rounded-xl shadow-sm group-hover:shadow-md transition-all border border-slate-100">
                    <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
                <span class="tracking-tight uppercase text-xs">Return to Collection</span>
            </a>
        @else
            @php
                $type = strtolower($asset->type);
                $backRoute = in_array($type, ['photo', 'video', 'ebook'], true)
                    ? route('media.index', ['type' => $type])
                    : route('media.index');
            @endphp
            <a href="{{ $backRoute }}"
                class="inline-flex items-center gap-3 text-slate-500 hover:text-lib-navy font-black transition-all group active:scale-95">
                <div class="bg-white p-2.5 rounded-xl shadow-sm group-hover:shadow-md transition-all border border-slate-100">
                    <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
                <span class="tracking-tight uppercase text-xs">Return</span>
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- LEFT: MAIN GALLERY (2 columns on large screens) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] p-6 md:p-10 text-slate-900 border border-slate-100 shadow-2xl relative overflow-hidden">
                {{-- Decorative background elements --}}
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-lib-sky/10 rounded-full blur-[100px]"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-lib-light/50 rounded-full blur-[100px]"></div>

                {{-- Category Title --}}
                <div class="mb-8 relative z-10">
                    <span class="inline-flex items-center gap-2 bg-lib-light px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase tracking-[0.25em] border border-lib-sky/20 text-lib-navy">
                        <span class="w-1.5 h-1.5 bg-lib-sky rounded-full animate-pulse"></span>
                        {{ $asset->categories->first()->name ?? $asset->type }}
                    </span>
                </div>

                {{-- MAIN IMAGE WRAPPER --}}
                <div id="fullscreenContainer" class="relative">

                    <style>
                        /* Fullscreen Mode Styles */
                        #fullscreenContainer:fullscreen {
                            background-color: black;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            width: 100vw;
                            height: 100vh;
                            padding: 20px;
                        }
                        #fullscreenContainer:fullscreen .media-inner-box {
                            background: transparent !important;
                            min-height: 100vh !important;
                            width: 100vw !important;
                            border-radius: 0 !important;
                            padding: 0 !important;
                        }
                        #fullscreenContainer:fullscreen #mainImage {
                            max-height: 90vh !important;
                            max-width: 90vw !important;
                        }
                        #fullscreenContainer:fullscreen #mainEbook {
                            height: 100vh !important;
                            width: 100vw !important;
                            max-width: 100vw !important;
                        }
                        #fullscreenContainer:fullscreen #mainEbook > div {
                            height: 100vh !important;
                            max-height: 100vh !important;
                        }
                        #fullscreenContainer:fullscreen #ebookFrame {
                            height: 100% !important;
                            width: 100% !important;
                        }
                        #fullscreenContainer:fullscreen .nav-arrow {
                            background: rgba(255,255,255,0.2) !important;
                            width: 60px !important;
                            height: 60px !important;
                        }
                        #fullscreenContainer:fullscreen .nav-arrow:hover {
                            background: rgba(255,255,255,0.5) !important;
                        }
                        #fullscreenContainer:fullscreen #fullscreenBtn {
                            top: 20px !important;
                            right: 20px !important;
                        }
                    </style>

                    {{-- MAIN MEDIA CONTAINER --}}
                    <div class="media-inner-box bg-slate-50 backdrop-blur-sm rounded-[2rem] p-4 flex items-center justify-center min-h-[500px] relative border border-slate-200 shadow-inner transition-all duration-500 overflow-hidden">

                        {{-- Navigation Arrows --}}
                        <button onclick="prevImage()" class="nav-arrow absolute top-1/2 left-6 -translate-y-1/2 z-50 bg-lib-navy/80 hover:bg-lib-navy text-white border border-lib-navy rounded-2xl w-14 h-14 flex items-center justify-center transition-all shadow-2xl backdrop-blur-xl group/arrow">
                            <svg class="h-6 w-6 group-hover/arrow:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button onclick="nextImage()" class="nav-arrow absolute top-1/2 right-6 -translate-y-1/2 z-50 bg-lib-navy/80 hover:bg-lib-navy text-white border border-lib-navy rounded-2xl w-14 h-14 flex items-center justify-center transition-all shadow-2xl backdrop-blur-xl group/arrow">
                            <svg class="h-6 w-6 group-hover/arrow:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Photo Element --}}
                        <img id="mainImage"
                             src="{{ asset('storage/' . $asset->file_path) }}"
                             class="max-w-full max-h-[460px] object-contain transition-all duration-700 ease-in-out transform scale-100 hover:scale-[1.02] {{ in_array(strtolower($asset->type), ['photo','image']) ? 'block' : 'hidden' }}">

                        {{-- Full Screen Button (Photos and E-books) --}}
                        <button id="fullscreenBtn" onclick="toggleFullScreen()"
                                class="absolute top-6 right-6 bg-lib-navy/80 hover:bg-lib-navy text-white border border-lib-navy rounded-2xl p-4 transition-all z-50 backdrop-blur-xl shadow-2xl group/fs {{ in_array(strtolower($asset->type), ['photo','image','ebook']) ? 'block' : 'hidden' }}"
                                title="View Full Screen">
                            <svg class="h-6 w-6 group-hover/fs:scale-110 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                            </svg>
                        </button>

                        {{-- Video Element --}}
                        <video id="mainVideo"
                               src="{{ in_array(strtolower($asset->type), ['video']) ? asset('storage/' . $asset->file_path) : '' }}"
                               controls
                               class="max-w-full max-h-[420px] {{ in_array(strtolower($asset->type), ['video']) ? 'block' : 'hidden' }}">
                        </video>

                        {{-- E-book (PDF) Element --}}
                        <div id="mainEbook" class="w-full {{ in_array(strtolower($asset->type), ['ebook']) ? 'block' : 'hidden' }}">
                            <div class="relative w-full h-[700px]">
                                <iframe id="ebookFrame"
                                        src="{{ in_array(strtolower($asset->type), ['ebook']) ? asset('storage/' . $asset->file_path) . '#toolbar=1&navpanes=1&scrollbar=1&view=FitH' : '' }}"
                                        class="w-full h-full border-none rounded-lg shadow-inner"
                                        allow="fullscreen">
                                        <p class="text-center text-slate-500 p-8">Your browser does not support PDFs. <a href="{{ in_array(strtolower($asset->type), ['ebook']) ? asset('storage/' . $asset->file_path) : '' }}" class="text-lib-sky hover:text-lib-navy font-bold">Download the PDF</a>.</p>
                                </iframe>
                            </div>
                            <div class="flex gap-3 justify-center mt-4">
                                <a id="ebookLink" href="{{ in_array(strtolower($asset->type), ['ebook']) ? asset('storage/' . $asset->file_path) : '' }}"
                                   target="_blank"
                                   download
                                   class="inline-flex items-center gap-2 bg-lib-navy/10 hover:bg-lib-navy text-lib-navy hover:text-white px-5 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all border border-lib-navy/20 shadow-xl backdrop-blur-md">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download PDF
                                </a>
                                <a id="ebookOpenLink" href="{{ in_array(strtolower($asset->type), ['ebook']) ? asset('storage/' . $asset->file_path) : '' }}"
                                   target="_blank"
                                   class="inline-flex items-center gap-2 bg-lib-sky/10 hover:bg-lib-sky text-lib-sky hover:text-white px-5 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest transition-all border border-lib-sky/20 shadow-xl backdrop-blur-md">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Open in New Tab
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function toggleFullScreen() {
                        const container = document.getElementById('fullscreenContainer');
                        if (!document.fullscreenElement) {
                            if (container.requestFullscreen) {
                                container.requestFullscreen();
                            } else if (container.webkitRequestFullscreen) {
                                container.webkitRequestFullscreen();
                            } else if (container.msRequestFullscreen) {
                                container.msRequestFullscreen();
                            }
                        } else {
                            if (document.exitFullscreen) {
                                document.exitFullscreen();
                            }
                        }
                    }
                </script>

                {{-- CAPTION --}}
                <div class="mt-10 mb-6 relative z-10 flex flex-col justify-center">
                    <p id="mainDate" class="text-lib-sky text-[10px] font-black uppercase tracking-[0.3em] mb-3 opacity-80 slide-up">
                        {{ \Carbon\Carbon::parse($asset->date ?? $asset->created_at)->format('d F Y') }}
                    </p>
                    <h3 id="mainTitle" class="text-3xl md:text-4xl font-black tracking-tight leading-tight slide-up-delayed">
                        {{ $asset->title }}
                    </h3>
                </div>

                {{-- METADATA SECTION --}}
                <div class="mt-6 pt-6 border-t border-slate-200 relative z-10">
                    <h4 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-4">Asset Details</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Asset Type --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-lib-light rounded-lg flex items-center justify-center flex-shrink-0">
                                @if($asset->type === 'photo')
                                    <span class="text-base">📸</span>
                                @elseif($asset->type === 'video')
                                    <span class="text-base">🎬</span>
                                @elseif($asset->type === 'ebook')
                                    <span class="text-base">📚</span>
                                @else
                                    <span class="text-base">📄</span>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Type</div>
                                <div class="text-sm font-bold text-lib-navy capitalize">{{ $asset->type }}</div>
                            </div>
                        </div>

                        {{-- Upload Date --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-lib-light rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-lib-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Date</div>
                                <div class="text-sm font-bold text-lib-navy">{{ \Carbon\Carbon::parse($asset->date ?? $asset->created_at)->format('d M Y') }}</div>
                            </div>
                        </div>

                        {{-- Views --}}
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-lib-light rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-lib-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Views</div>
                                <div class="text-sm font-bold text-lib-navy">{{ $asset->getDetail('views', 0) }}</div>
                            </div>
                        </div>

                        {{-- Collection --}}
                        @if(isset($collectionName) && $collectionName)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-lib-light rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-lib-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Collection</div>
                                <a href="{{ route('collections.show', $collectionName) }}" class="text-sm font-bold text-lib-sky hover:text-lib-navy transition-colors">
                                    {{ $collectionName }}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Categories --}}
                    @if($asset->categories && $asset->categories->isNotEmpty())
                    <div class="flex items-start gap-3 mt-4">
                        <div class="w-8 h-8 bg-lib-light rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-4 w-4 text-lib-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <div class="flex-grow">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Categories</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($asset->categories as $category)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-lib-navy/5 text-lib-navy text-[10px] font-bold border border-lib-navy/10">
                                    {{ $category->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Tags --}}
                    @if($asset->tags && $asset->tags->isNotEmpty())
                    <div class="flex items-start gap-3 mt-4">
                        <div class="w-8 h-8 bg-lib-light rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="h-4 w-4 text-lib-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                        <div class="flex-grow">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-2">Tags</div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($asset->tags as $tag)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-lib-sky/10 text-lib-sky text-[10px] font-bold border border-lib-sky/20">
                                    #{{ $tag->name }}
                                </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Collection Access --}}
                @if(!isset($isCollectionContext) && isset($collectionName))
                <div class="mt-8 border-t border-slate-200 pt-8">
                    <a href="{{ route('collections.show', $collectionName) }}"
                       class="flex items-center justify-center gap-3 w-full bg-lib-navy hover:bg-lib-sky text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-lib-navy/10 transition-all hover:-translate-y-1 active:scale-95 group">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        View Full Collection
                    </a>
                </div>
                @endif
            <style>
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .slide-up { animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
                .slide-up-delayed { animation: slideUp 0.8s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
            </style>

            </div>
        </div>

        {{-- RIGHT: RELATED ASSETS SIDEBAR --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 p-8 shadow-2xl shadow-slate-200/50 sticky top-36 md:top-40">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-lib-navy flex items-center gap-3">
                        <div class="bg-lib-light p-2 rounded-xl">
                            <svg class="h-5 w-5 text-lib-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        @if(isset($isCollectionContext) && $isCollectionContext)
                            Collection
                        @else
                            Gallery
                        @endif
                    </h3>
                    <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                        {{ count($relatedAssets ?? []) + 1 }} ITEMS
                    </span>
                </div>

                <div class="flex flex-col gap-3 max-h-[600px] overflow-y-auto pr-2">

                    {{-- JSON LIST FOR JS --}}
                    <script>
                        const gallery = [
                            {
                                id: {{ $asset->id }},
                                title: @json($asset->title),
                                type: @json(strtolower($asset->type)),
                                date: "{{ \Carbon\Carbon::parse($asset->date ?? $asset->created_at)->format('d F Y') }}",
                                src: "{{ asset('storage/' . $asset->file_path) }}",
                                thumbnail: "{{ $asset->thumbnail_path ? asset('storage/' . $asset->thumbnail_path) : '' }}"
                            },

                            @foreach($relatedAssets ?? [] as $rel)
                            {
                                id: {{ $rel->id }},
                                title: @json($rel->title),
                                type: @json(strtolower($rel->type)),
                                date: "{{ \Carbon\Carbon::parse($rel->date ?? $rel->created_at)->format('d F Y') }}",
                                src: "{{ asset('storage/' . $rel->file_path) }}",
                                thumbnail: "{{ $rel->thumbnail_path ? asset('storage/' . $rel->thumbnail_path) : '' }}"
                            },
                            @endforeach
                        ];

                        let currentIndex = 0;

                        function updateMain() {
                            const item = gallery[currentIndex];
                            const img = document.getElementById('mainImage');
                            const video = document.getElementById('mainVideo');
                            const ebook = document.getElementById('mainEbook');
                            const ebookFrame = document.getElementById('ebookFrame');
                            const ebookLink = document.getElementById('ebookLink');
                            const fsBtn = document.getElementById('fullscreenBtn');
                            const mainContainer = document.querySelector('.media-inner-box');

                            // Trigger transition
                            mainContainer.classList.add('opacity-0', 'scale-95');

                            setTimeout(() => {
                                // Reset all
                                img.classList.add('hidden');
                                video.classList.add('hidden');
                                video.pause();
                                ebook.classList.add('hidden');
                                if(fsBtn) fsBtn.classList.add('hidden');

                                if (['video'].includes(item.type)) {
                                    video.classList.remove('hidden');
                                    video.src = item.src;
                                    video.load();
                                } else if (['ebook'].includes(item.type)) {
                                    ebook.classList.remove('hidden');
                                    ebookFrame.src = item.src + '#toolbar=1&navpanes=1&scrollbar=1&view=FitH';
                                    ebookLink.href = item.src;
                                    document.getElementById('ebookOpenLink').href = item.src;
                                    if(fsBtn) fsBtn.classList.remove('hidden');
                                } else {
                                    img.classList.remove('hidden');
                                    img.src = item.src;
                                    if(fsBtn) fsBtn.classList.remove('hidden');
                                }

                                const titleEl = document.getElementById('mainTitle');
                                const dateEl = document.getElementById('mainDate');

                                titleEl.innerText = item.title;
                                dateEl.innerText = item.date;

                                // Restart animations
                                titleEl.classList.remove('slide-up-delayed');
                                dateEl.classList.remove('slide-up');
                                void titleEl.offsetWidth; // trigger reflow
                                titleEl.classList.add('slide-up-delayed');
                                dateEl.classList.add('slide-up');

                                mainContainer.classList.remove('opacity-0', 'scale-95');

                                // Update Active Indicator
                                document.querySelectorAll('.gallery-item').forEach((box, i) => {
                                    if(i === currentIndex) {
                                        box.classList.add('border-lib-sky', 'bg-lib-light/50', 'ring-4', 'ring-lib-sky/10');
                                        box.querySelector('.now-viewing').classList.remove('hidden');
                                    } else {
                                        box.classList.remove('border-lib-sky', 'bg-lib-light/50', 'ring-4', 'ring-lib-sky/10');
                                        box.querySelector('.now-viewing').classList.add('hidden');
                                    }
                                });
                            }, 300);
                        }

                        function nextImage() {
                            currentIndex = (currentIndex + 1) % gallery.length;
                            updateMain();
                        }

                        function prevImage() {
                            currentIndex = (currentIndex - 1 + gallery.length) % gallery.length;
                            updateMain();
                        }

                        function jumpTo(index) {
                            currentIndex = index;
                            updateMain();
                        }
                    </script>

                    {{-- CURRENT ASSET indicator box --}}
                    <div onclick="jumpTo(0)"
                         class="gallery-item cursor-pointer flex gap-4 items-center bg-lib-light/50 p-4 rounded-2xl transition-all group border-2 border-lib-sky ring-4 ring-lib-sky/10 font-fredoka">
                        <div class="relative w-24 h-20 flex-shrink-0">
                            @if(strtolower($asset->type) === 'video')
                                <div class="w-full h-full bg-slate-900 rounded-xl flex items-center justify-center text-white overflow-hidden">
                                     @if($asset->thumbnail_path)
                                        <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" class="w-full h-full object-cover opacity-50">
                                     @endif
                                    <svg class="absolute h-8 w-8 text-white drop-shadow-lg" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            @elseif(strtolower($asset->type) === 'ebook')
                                <div class="w-full h-full bg-indigo-600 rounded-xl flex items-center justify-center text-white text-3xl overflow-hidden">
                                    @if($asset->thumbnail_path)
                                        <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" class="w-full h-full object-cover">
                                    @else
                                        📚
                                    @endif
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $asset->file_path) }}" class="w-full h-full object-cover rounded-xl shadow-md group-hover:scale-105 transition-transform">
                            @endif
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-1.5 mb-1.5">
                                <span class="now-viewing flex items-center gap-1.5 bg-lib-sky text-white text-[8px] font-black uppercase px-2 py-0.5 rounded-full tracking-tighter">
                                    <span class="w-1 h-1 bg-white rounded-full animate-ping"></span>
                                    Viewing
                                </span>
                            </div>
                            <p class="font-bold text-sm text-lib-navy group-hover:text-lib-sky transition-colors line-clamp-1 tracking-tight">{{ $asset->title }}</p>
                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ \Carbon\Carbon::parse($asset->date ?? $asset->created_at)->format('d M Y') }}</span>
                        </div>
                    </div>

                    {{-- RELATED ASSETS --}}
                    @foreach($relatedAssets ?? [] as $index => $rel)
                    <div onclick="jumpTo({{ $index + 1 }})"
                         class="gallery-item cursor-pointer flex gap-4 items-center bg-slate-50 hover:bg-slate-100/80 p-4 rounded-2xl transition-all group border-2 border-transparent hover:border-slate-200 font-fredoka">

                        <div class="relative w-24 h-20 flex-shrink-0">
                            @if(strtolower($rel->type) === 'video')
                                <div class="w-full h-full bg-slate-200 rounded-xl flex items-center justify-center text-slate-400 overflow-hidden">
                                    @if($rel->thumbnail_path)
                                        <img src="{{ asset('storage/' . $rel->thumbnail_path) }}" class="w-full h-full object-cover opacity-50 grayscale">
                                    @endif
                                    <svg class="absolute h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            @elseif(strtolower($rel->type) === 'ebook')
                                <div class="w-full h-full bg-slate-200 rounded-xl flex items-center justify-center text-slate-400 text-2xl overflow-hidden">
                                     @if($rel->thumbnail_path)
                                        <img src="{{ asset('storage/' . $rel->thumbnail_path) }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0">
                                     @else
                                        📚
                                     @endif
                                </div>
                            @else
                                <img src="{{ asset('storage/' . $rel->file_path) }}"
                                     class="w-full h-full object-cover rounded-xl shadow-sm grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all">
                            @endif
                        </div>

                        <div class="flex-grow min-w-0">
                            <div class="flex items-center gap-1.5 mb-1.5">
                                <span class="now-viewing hidden flex items-center gap-1.5 bg-lib-sky text-white text-[8px] font-black uppercase px-2 py-0.5 rounded-full tracking-tighter">
                                    <span class="w-1 h-1 bg-white rounded-full animate-ping"></span>
                                    Viewing
                                </span>
                            </div>
                            <p class="font-bold text-sm text-lib-navy group-hover:text-lib-sky transition-colors line-clamp-1 tracking-tight">{{ $rel->title }}</p>
                            <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ \Carbon\Carbon::parse($rel->date ?? $rel->created_at)->format('d M Y') }}</span>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>

    </div>

</div>
@endsection
