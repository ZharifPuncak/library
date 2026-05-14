@extends('layouts.app')

@section('title', $collectionName . ' - Collection')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ filterMobileOpen: false }">
    
    {{-- Back Button --}}
    <a href="{{ route('collections.index') }}" 
        class="inline-flex items-center gap-3 text-slate-500 hover:text-lib-navy font-black mb-8 transition-all group active:scale-95"
        style="text-decoration: none; font-family: 'Fredoka', sans-serif;">
        <div class="bg-white p-2.5 rounded-xl shadow-sm group-hover:shadow-md transition-all border border-slate-100 flex items-center justify-center">
            <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest">Back to Collections</span>
    </a>

    <!-- Header with Stats -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Event Album</p>
            <h1 class="text-4xl font-black text-lib-navy uppercase tracking-tighter mb-2">{{ $collectionName }}</h1>
            <p class="text-slate-500 font-medium text-lg">Discover all media captured during this event.</p>
        </div>

        {{-- Stats --}}
        <div class="bg-white px-8 py-4 rounded-2xl shadow-sm border border-slate-100 flex flex-wrap items-center gap-x-8 gap-y-4 flex-shrink-0">
            <div class="flex items-center gap-3">
                <p class="text-2xl font-black text-lib-navy leading-none">{{ $counts['total'] }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Media</p>
            </div>
            <div class="w-px h-8 bg-slate-100 hidden md:block"></div>
            <div class="flex items-center gap-3">
                <p class="text-2xl font-black text-lib-sky leading-none">{{ $counts['photo'] }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Photos</p>
            </div>
            <div class="w-px h-8 bg-slate-100 hidden md:block"></div>
            <div class="flex items-center gap-3">
                <p class="text-2xl font-black text-lib-navy leading-none">{{ $counts['video'] }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Videos</p>
            </div>
            <div class="w-px h-8 bg-slate-100 hidden md:block"></div>
            <div class="flex items-center gap-3">
                <p class="text-2xl font-black text-lib-sky leading-none">{{ $counts['ebook'] }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">e-Books</p>
            </div>
        </div>
    </div>

    <!-- Media Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 auto-rows-fr">
        @forelse($assets as $asset)
            <div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col font-fredoka"
                 onmouseenter="this.querySelector('video')?.play()" 
                 onmouseleave="this.querySelector('video')?.load()">
                
                <!-- Media Preview -->
                <div class="relative h-48 overflow-hidden bg-slate-100">
                    <a href="{{ route('media.show', $asset) }}" class="block h-full w-full">
                        @php
                            $type = strtolower($asset->type);
                            $isThumbnail = !empty($asset->thumbnail_path);
                        @endphp

                        @if($type === 'video')
                            <video src="{{ asset('storage/' . $asset->file_path) }}" 
                                   @if($isThumbnail) poster="{{ asset('storage/' . $asset->thumbnail_path) }}" @endif
                                   class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                   muted playsinline></video>
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-100 group-hover:opacity-0 transition-opacity pointer-events-none">
                                <div class="w-12 h-12 bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/20">
                                    <svg class="h-6 w-6 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        @else
                            @if($type === 'photo')
                                <img src="{{ asset('storage/' . $asset->file_path) }}" 
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                     alt="{{ $asset->title }}">
                            @else
                                {{-- E-book Support --}}
                                @if($isThumbnail)
                                    <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" 
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" 
                                         alt="{{ $asset->title }}">
                                @else
                                    <div class="w-full h-full bg-slate-50 flex items-center justify-center text-4xl">📚</div>
                                @endif
                            @endif
                        @endif

                        <!-- Type Badge -->
                        <div class="absolute top-3 left-3 z-20">
                            <span class="bg-white/90 backdrop-blur-md text-lib-navy px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg flex items-center gap-1">
                                {{ $asset->type }}
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Info -->
                <div class="p-4 flex-grow flex flex-col">
                    <div class="flex-grow">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[9px] font-black text-lib-sky uppercase tracking-[0.2em]">{{ $asset->categories->first()->name ?? 'Asset' }}</span>
                        </div>
                        <a href="{{ route('media.show', $asset) }}" class="block">
                            <h3 class="text-base font-bold text-slate-800 leading-tight mb-2 group-hover:text-lib-sky transition-colors line-clamp-2">
                                {{ $asset->title }}
                            </h3>
                        </a>

                        <!-- Tagging Section -->
                        <div class="mt-3 flex items-center gap-1.5 overflow-hidden">
                            <svg class="h-3 w-3 text-slate-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <div class="flex flex-wrap gap-1.5 overflow-hidden">
                                @foreach($asset->tags->take(2) as $tag)
                                    <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-lg border border-slate-100/50 whitespace-nowrap">#{{ $tag->name }}</span>
                                @endforeach
                                @if($asset->tags->count() > 2)
                                    <span class="text-[10px] font-black text-lib-sky bg-lib-light px-2 py-0.5 rounded-lg">+{{ $asset->tags->count() - 2 }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between text-[10px] text-slate-300 border-t border-slate-50 pt-3">
                        <span class="font-bold uppercase tracking-wider">
                            {{ $asset->created_at->format('M d, Y') }}
                        </span>
                        <div class="w-6 h-6 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-lib-sky group-hover:text-white transition-colors">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10 text-slate-500">
                <p class="text-lg font-semibold">No assets found in this collection.</p>
                <p class="text-sm">It looks like there are no media items here yet.</p>
            </div>
        @endforelse
    </div>

        <div class="mt-20">
            {{ $assets->links() }}
        </div>
    </div>
</div>
@endsection
