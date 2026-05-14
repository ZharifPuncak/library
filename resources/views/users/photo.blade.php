@extends('layouts.app')

@section('title', 'Photos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ filterMobileOpen: false }">
    <!-- Header -->
    <div class="mb-10">
        <h1 class="text-4xl font-black text-lib-navy uppercase tracking-tighter mb-2">Project Gallery</h1>
        <p class="text-slate-500 font-medium text-lg">Discover high-resolution operational site captures and historical project photography from our corporate archive.</p>
    </div>

    <!-- Controls -->
    <div class="flex flex-col md:flex-row gap-4 mb-12">
        <div class="flex-grow relative group">
            <form action="{{ route('media.photos') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Search operational capture..." value="{{ request('search') }}"
                       class="w-full pl-14 {{ request('search') ? 'pr-52' : 'pr-32' }} py-5 bg-white border-none rounded-3xl focus:ring-4 focus:ring-lib-sky/20 transition-all font-bold text-lib-navy shadow-sm group-hover:shadow-md">
                
                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-lib-sky opacity-40 group-focus-within:opacity-100 transition-opacity">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                @if(request('search'))
                    <a href="{{ route('media.photos', request()->except('search')) }}" 
                       class="absolute right-36 top-1/2 -translate-y-1/2 text-red-500 hover:text-red-600 font-bold text-xs transition-colors uppercase tracking-wide">
                        CLEAR
                    </a>
                @endif
                
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-lib-navy hover:bg-lib-sky text-white px-7 py-3 rounded-2xl font-bold transition-all text-sm">
                    Search
                </button>
            </form>
        </div>
        <button @click="filterMobileOpen = true" 
                class="relative flex items-center justify-center gap-3 bg-white hover:bg-lib-light border border-slate-200 text-lib-navy px-7 py-3 rounded-2xl font-bold transition-all shadow-sm hover:shadow-md text-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            @php
                $filterCount = count(request('categories', [])) + (request('year') ? 1 : 0);
            @endphp
            Refine
            @if($filterCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-black rounded-full w-6 h-6 flex items-center justify-center shadow-lg">
                    {{ $filterCount }}
                </span>
            @endif
        </button>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 auto-rows-fr">
        @forelse($assets as $asset)
            <div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col font-fredoka">
                {{-- Thumbnail --}}
                <div class="relative h-48 overflow-hidden bg-slate-100">
                    <a href="{{ route('media.show', $asset) }}" class="block h-full w-full">
                        <img src="{{ asset('storage/' . $asset->file_path) }}" 
                             alt="{{ $asset->title }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        
                        <!-- Type Badge -->
                        <div class="absolute top-3 left-3 z-20">
                            <span class="bg-white/90 backdrop-blur-md text-lib-navy px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg flex items-center gap-1">
                                📸 PHOTO
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
                            <h3 class="text-base font-bold text-slate-800 leading-tight mb-2 group-hover:text-lib-sky transition-colors line-clamp-2">{{ $asset->title }}</h3>
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
            <div class="col-span-full py-20 text-center">
                <div class="text-9xl mb-6 grayscale opacity-20">🖼️</div>
                <h3 class="text-2xl font-black text-lib-navy mb-2">No photos found</h3>
                <p class="text-slate-400 font-medium">Try broadening your search or filtering options</p>
                <a href="{{ route('media.photos') }}" class="inline-block mt-8 px-10 py-4 bg-lib-blue text-white rounded-2xl font-black hover:bg-lib-navy transition-all shadow-xl shadow-lib-blue/20">Back to Archive</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-16">
        {{ $assets->appends(request()->query())->links() }}
    </div>

    <!-- Filter Modal -->
    <template x-teleport="body">
        <div x-show="filterMobileOpen" x-cloak 
             class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-lib-navy/40 backdrop-blur-md"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="filterMobileOpen = false" 
                 class="bg-white w-full sm:max-w-md rounded-t-[3.5rem] sm:rounded-[4rem] p-10 pb-12 shadow-2xl relative"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="translate-y-full sm:scale-90 sm:translate-y-0"
                 x-transition:enter-end="translate-y-0 sm:scale-100 sm:translate-y-0">
                
                <div @click="filterMobileOpen = false" class="absolute right-8 top-8 p-3 bg-slate-50 text-slate-400 rounded-full hover:bg-red-50 hover:text-red-500 cursor-pointer transition-all">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>

                <div class="mb-10 text-center sm:text-left">
                    <h2 class="text-3xl font-black text-lib-navy">Refine Search</h2>
                    <p class="text-slate-400 font-medium mt-1">Sift through our visual records</p>
                </div>

                <form action="{{ route('media.photos') }}" method="GET" class="space-y-10">
                    <div>
                        <h3 class="text-xs font-black text-slate-300 uppercase tracking-widest mb-6">By Category</h3>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($categories as $category)
                                <label class="cursor-pointer group">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                           {{ in_array($category->id, request('categories', [])) ? 'checked' : '' }}
                                           class="hidden peer">
                                    <div class="px-4 py-3 text-center rounded-2xl border-2 border-slate-50 text-xs font-bold text-slate-400 peer-checked:bg-lib-sky peer-checked:border-lib-sky peer-checked:text-white transition-all group-hover:border-slate-200">
                                        {{ $category->name }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-black text-slate-300 uppercase tracking-widest mb-4">By Timeline</h3>
                        <input type="number" name="year" value="{{ request('year') }}" placeholder="Enter year..."
                               class="w-full bg-slate-50 border-none rounded-2xl py-5 focus:ring-2 focus:ring-lib-sky transition-all font-black text-center text-lg text-lib-navy">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-lib-blue text-white py-5 rounded-3xl font-black hover:bg-lib-navy transition-all shadow-2xl shadow-lib-blue/30 text-lg">Update Results</button>
                        @if(request()->hasAny(['categories', 'year']))
                            <a href="{{ route('media.photos', request()->only('search')) }}" 
                               class="flex-1 bg-red-500 hover:bg-red-600 text-white py-5 rounded-3xl font-black text-center transition-all shadow-2xl shadow-red-500/30 text-lg">
                                Clear Filters
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </template>
    </div>
</div>
@endsection
