@extends('layouts.admin')

@section('title', 'All Media')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ filterModalOpen: {{ request('filters') === 'open' ? 'true' : 'false' }} }">
    
    {{-- Error Messages --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl mb-6 font-medium">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Back Button --}}
    <a href="{{ route('admin.dashboard') }}" 
        class="inline-flex items-center gap-3 text-slate-500 hover:text-lib-navy font-black mb-8 transition-all group active:scale-95"
        style="text-decoration: none; font-family: 'Fredoka', sans-serif;">
        <div class="bg-white p-2.5 rounded-xl shadow-sm group-hover:shadow-md transition-all border border-slate-100 flex items-center justify-center">
            <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
        </div>
        <span class="text-xs font-bold uppercase tracking-widest">Back to Dashboard</span>
    </a>

    {{-- Header with Action Buttons --}}
    <div class="mb-10 flex flex-col gap-6">
        <div>
            <h1 class="text-4xl font-black text-lib-navy uppercase tracking-tighter mb-2">All Media</h1>
            <p class="text-slate-500 font-medium text-lg">Access our comprehensive corporate repository. Browse, search, and manage all assets.</p>
        </div>
        
        {{-- Admin Action Buttons --}}
        <div class="flex flex-wrap gap-3 flex-shrink-0">
            <a href="{{ route('admin.categories.categoryManagement') }}"
               class="inline-flex items-center gap-2 bg-lib-navy text-white hover:bg-lib-sky px-6 py-3 rounded-xl font-black transition-all shadow-lg hover:shadow-xl">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                MANAGE CATEGORIES
            </a>
            
            <a href="{{ route('admin.tags.tagManagement') }}"
               class="inline-flex items-center gap-2 bg-lib-navy text-white hover:bg-lib-sky px-6 py-3 rounded-xl font-black transition-all shadow-lg hover:shadow-xl">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                MANAGE TAGS
            </a>
        </div>
    </div>

    {{-- Search Bar with Filter Button (matching user page) --}}
    <div class="flex flex-col md:flex-row gap-4 mb-12">
        <div class="flex-grow relative group">
            <form action="{{ route('admin.assets.all') }}" method="GET" class="relative">
                {{-- Preserve filter parameters --}}
                @foreach(request('categories', []) as $catId)
                    <input type="hidden" name="categories[]" value="{{ $catId }}">
                @endforeach
                @if(request('year'))
                    <input type="hidden" name="year" value="{{ request('year') }}">
                @endif

                <input type="text" name="search" placeholder="Search by name or tagging..." value="{{ request('search') }}"
                       class="w-full pl-14 {{ request('search') ? 'pr-52' : 'pr-32' }} py-5 bg-white border-none rounded-3xl focus:ring-4 focus:ring-lib-sky/20 transition-all font-bold text-lib-navy shadow-sm group-hover:shadow-md">
                
                <div class="absolute left-5 top-1/2 -translate-y-1/2 text-lib-sky opacity-40 group-focus-within:opacity-100 transition-opacity">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                @if(request('search'))
                    <a href="{{ route('admin.assets.all', request()->except('search')) }}" 
                       class="absolute right-36 top-1/2 -translate-y-1/2 text-red-500 hover:text-red-600 font-bold text-xs transition-colors uppercase tracking-wide">
                        CLEAR
                    </a>
                @endif

                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 bg-lib-navy hover:bg-lib-sky text-white px-7 py-3 rounded-2xl font-bold transition-all text-sm">
                    Search
                </button>
            </form>
        </div>

        <button @click="filterModalOpen = true" 
                class="relative flex items-center justify-center gap-3 bg-white hover:bg-lib-light border border-slate-200 text-lib-navy px-7 py-3 rounded-2xl font-bold transition-all shadow-sm hover:shadow-md text-sm">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Refine
            @php
                $filterCount = count(request('categories', [])) + (request('year') ? 1 : 0);
            @endphp
            @if($filterCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-black rounded-full w-6 h-6 flex items-center justify-center shadow-lg">
                    {{ $filterCount }}
                </span>
            @endif
        </button>
    </div>

    {{-- Media Grid (matching user page) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 auto-rows-fr">
        @forelse($assets as $asset)
            <div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col font-fredoka"
                 onmouseenter="this.querySelector('video')?.play();" 
                 onmouseleave="this.querySelector('video')?.load();">
                
                {{-- Media Preview --}}
                <div class="relative h-48 overflow-hidden bg-slate-100">
                    <a href="{{ route('admin.assets.show', $asset->id) }}" class="block h-full w-full">
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

                        {{-- Type Badge --}}
                        <div class="absolute top-3 left-3 z-20">
                            <span class="bg-white/90 backdrop-blur-md text-lib-navy px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg flex items-center gap-1">
                                @if($type === 'photo') 📸 
                                @elseif($type === 'video') 🎬 
                                @else 📚 
                                @endif
                                {{ $asset->type }}
                            </span>
                        </div>

                        {{-- Admin Action Buttons --}}
                        {{-- Overlay Actions --}}
                        <div class="absolute inset-x-0 top-0 p-4 flex justify-end gap-2 transition-all duration-300">
                            <a href="{{ route('admin.assets.edit', $asset->id) }}" 
                               class="w-10 h-10 flex-shrink-0 rounded-full bg-white/90 backdrop-blur text-blue-600 shadow-lg flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all transform hover:scale-110 border-0 p-0"
                               title="Edit Asset">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </a>
                            
                            <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST" class="inline">
                                @csrf 
                                @method('DELETE')
                                <button type="submit" class="delete-confirm w-10 h-10 flex-shrink-0 rounded-full bg-white/90 backdrop-blur text-red-500 shadow-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all transform hover:scale-110 border-0 p-0"
                                        data-message="Are you sure you want to permanently delete this asset?"
                                        title="Delete Asset">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </a>
                </div>

                {{-- Info --}}
                <div class="p-4 flex-grow flex flex-col">
                    <div class="flex-grow">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[9px] font-black text-lib-sky uppercase tracking-[0.2em]">{{ $asset->categories->first()->name ?? 'Asset' }}</span>
                        </div>
                        <a href="{{ route('admin.assets.show', $asset->id) }}" class="block">
                            <h3 class="text-base font-bold text-slate-800 leading-tight mb-2 group-hover:text-lib-sky transition-colors line-clamp-2">{{ $asset->title }}</h3>
                        </a>

                        {{-- Refined Tagging Section --}}
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

                    <div class="mt-4 flex items-center justify-between text-[9px] font-black text-slate-300 border-t border-slate-50 pt-3">
                        <span>{{ $asset->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="bg-slate-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">🔍</div>
                <h3 class="text-2xl font-black text-lib-navy">No results found</h3>
                <p class="text-slate-400 font-medium mt-2">Try adjusting your filters or search terms</p>
                <a href="{{ route('admin.assets.all') }}" class="inline-block mt-8 px-10 py-4 bg-lib-blue text-white rounded-2xl font-black hover:bg-lib-navy transition-all shadow-xl shadow-lib-blue/20">Reset Archive</a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-16">
        {{ $assets->appends(request()->query())->links() }}
    </div>

    {{-- Filter Modal (matching user page) --}}
    <template x-teleport="body">
        <div x-show="filterModalOpen" x-cloak 
             class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-lib-navy/40 backdrop-blur-md"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="filterModalOpen = false" 
                 class="bg-white w-full sm:max-w-md rounded-t-[3.5rem] sm:rounded-[4rem] p-10 pb-12 shadow-2xl relative"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="translate-y-full sm:scale-90 sm:translate-y-0"
                 x-transition:enter-end="translate-y-0 sm:scale-100 sm:translate-y-0">
                
                <div @click="filterModalOpen = false" class="absolute right-8 top-8 p-3 bg-slate-50 text-slate-400 rounded-full hover:bg-red-50 hover:text-red-500 cursor-pointer transition-all">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>

                <div class="mb-10 text-center sm:text-left">
                    <h2 class="text-3xl font-black text-lib-navy">Refine Search</h2>
                    <p class="text-slate-400 font-medium mt-1">Sift through our corporate collection</p>
                </div>

                <form action="{{ route('admin.assets.all') }}" method="GET" class="space-y-10">
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    
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
                        <input type="number" name="year" value="{{ request('year') }}" placeholder="Enter year (YYYY)..."
                               class="w-full bg-slate-50 border-none rounded-2xl py-5 focus:ring-2 focus:ring-lib-sky transition-all font-black text-center text-lg text-lib-navy">
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-lib-blue text-white py-5 rounded-3xl font-black hover:bg-lib-navy transition-all shadow-2xl shadow-lib-blue/30 text-lg">Apply Filters</button>
                        @if(request()->hasAny(['categories', 'year']))
                            <a href="{{ route('admin.assets.all', request()->only('search')) }}" 
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
@endsection