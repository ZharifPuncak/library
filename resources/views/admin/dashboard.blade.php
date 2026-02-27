@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    /* Hero Section with Slideshow */
    .hero-section { position: relative; height: 60vh; min-height: 400px; overflow: hidden; }
    .hero-slide { position: absolute; width: 100%; height: 100%; top: 0; left: 0; opacity: 0; transition: opacity 1s ease-in-out; }
    .hero-slide.active { opacity: 1; }
    .hero-slide-bg { display: block; width: 100%; height: 100%; object-fit: cover; object-position: center center; }
    
    .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.4) 100%); display: flex; align-items: center; justify-content: center; }
    .hero-content { text-align: center; color: white; z-index: 10; padding: 0 1rem; }
    .hero-title { font-size: clamp(2rem, 8vw, 4rem); font-weight: 900; margin-bottom: 1rem; text-shadow: 0 4px 12px rgba(0,0,0,0.3); letter-spacing: -0.02em; }
    .hero-subtitle { font-size: clamp(1rem, 3vw, 1.25rem); margin-bottom: 2rem; opacity: 0.9; font-weight: 500; font-family: 'Inter', sans-serif; }
    
    /* Slide Controls */
    .slide-nav { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 20; }
    .slide-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.3); cursor: pointer; transition: all 0.3s; }
    .slide-dot.active { background: white; width: 24px; border-radius: 4px; }
    
    .slide-arrow { position: absolute; top: 50%; transform: translateY(-50%); background: white/10; color: white; border: none; font-size: 2rem; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; cursor: pointer; border-radius: 50%; transition: all 0.3s; z-index: 20; backdrop-filter: blur(8px); border: 1px solid white/20; }
    .slide-arrow:hover { background: white; color: #0e3f70; }
    .slide-arrow.prev { left: 20px; }
    .slide-arrow.next { right: 20px; }

    /* Quick Access Cards */
    .access-card {
        transition: all 0.3s;
        background: linear-gradient(135deg, #0e3f70 0%, #1e4972 100%);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .access-card:hover {
        transform: translateY(-0.5rem);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Admin Edit Button */
    .admin-edit-btn {
        background-color: #3374a8ff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s;
        text-shadow: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        white-space: nowrap; 
        text-decoration: none;
        display: inline-block;
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 30;
    }
    .admin-edit-btn:hover {
        background-color: #2a5a8a; 
    }
</style>

<!-- Hero Slideshow -->
<section class="hero-section">
    @if(isset($slideshows) && $slideshows->count() > 0)
        @foreach($slideshows as $index => $slide)
            <div class="hero-slide {{ $index === 0 ? 'active' : '' }}">
                <img class="hero-slide-bg" src="{{ asset('storage/' . $slide->slideshow_pic) }}" alt="{{ $slide->title }}">
            </div>
        @endforeach
    @else
        <div class="hero-slide active">
            <img class="hero-slide-bg" src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2000&auto=format&fit=crop" alt="Library">
        </div>
        <div class="hero-slide">
            <img class="hero-slide-bg" src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=2000&auto=format&fit=crop" alt="Bookshelf">
        </div>
    @endif

    {{-- Manage Slideshow Button (Admin only) --}}
    @if(Auth::check() && Auth::user()->isAdmin())
        <a href="{{ route('admin.slideshow.manage') }}" class="admin-edit-btn">
            ⚙️ Manage Slideshow
        </a>
    @endif

    <div class="hero-overlay">
        <div class="hero-content">
            <h1 class="hero-title uppercase">DIGITAL REPOSITORY</h1>
            <p class="hero-subtitle">Discover, Explore, and Learn</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('admin.assets.all') }}" class="bg-white text-lib-navy px-8 py-3 rounded-full font-bold hover:bg-lib-sky hover:text-white transition-all shadow-lg">Browse Media</a>
                <a href="#browse" class="bg-white/10 text-white border border-white/20 backdrop-blur-md px-8 py-3 rounded-full font-bold hover:bg-white/20 transition-all">Quick Links</a>
            </div>
        </div>
    </div>

    <button class="slide-arrow prev" onclick="changeSlide(-1)">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
    </button>
    <button class="slide-arrow next" onclick="changeSlide(1)">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
    </button>

    <div class="slide-nav">
        @if(isset($slideshows) && $slideshows->count() > 0)
            @foreach($slideshows as $index => $slide)
                <span class="slide-dot {{ $index === 0 ? 'active' : '' }}" onclick="goToSlide({{ $index }})"></span>
            @endforeach
        @else
            <span class="slide-dot active" onclick="goToSlide(0)"></span>
            <span class="slide-dot" onclick="goToSlide(1)"></span>
        @endif
    </div>
</section>

<!-- Quick Access -->
<section id="browse" class="py-20 bg-lib-light" x-data="{ filterModalOpen: false }">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Search Bar -->
        <div class="flex flex-col md:flex-row gap-4 mb-12">
            <div class="flex-grow relative group">
                <form action="{{ route('admin.assets.all') }}" method="GET" class="relative">
                    <input type="text" name="search" placeholder="Search all resources..." value="{{ request('search') }}"
                           class="w-full pl-14 {{ request('search') ? 'pr-52' : 'pr-32' }} py-5 bg-white border-none rounded-3xl focus:ring-4 focus:ring-lib-sky/20 transition-all font-bold text-lib-navy shadow-lg group-hover:shadow-xl">
                    
                    <div class="absolute left-5 top-1/2 -translate-y-1/2 text-lib-sky opacity-40 group-focus-within:opacity-100 transition-opacity">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    
                    @if(request('search'))
                        <a href="{{ route('admin.assets.all') }}" 
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
                    class="relative px-7 py-3 bg-white text-lib-navy border-2 border-slate-100 rounded-2xl font-bold flex items-center justify-center gap-3 hover:bg-lib-light transition-all shadow-lg shadow-slate-200/50 hover:shadow-xl text-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Refine
            </button>
        </div>

        <!-- Filter Modal -->
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
                                @forelse($categories as $category)
                                    <label class="cursor-pointer group">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                               class="hidden peer">
                                        <div class="px-4 py-3 text-center rounded-2xl border-2 border-slate-50 text-xs font-bold text-slate-400 peer-checked:bg-lib-sky peer-checked:border-lib-sky peer-checked:text-white transition-all group-hover:border-slate-200">
                                            {{ $category->name }}
                                        </div>
                                    </label>
                                @empty
                                    <p class="col-span-2 text-center text-slate-400 text-xs">No categories available</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-black text-slate-300 uppercase tracking-widest mb-4">By Timeline</h3>
                            <input type="number" name="year" value="{{ request('year') }}" placeholder="Enter year (YYYY)..."
                                   class="w-full bg-slate-50 border-none rounded-2xl py-5 focus:ring-2 focus:ring-lib-sky transition-all font-black text-center text-lg text-lib-navy">
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button type="submit" class="flex-1 bg-lib-blue text-white py-5 rounded-3xl font-black hover:bg-lib-navy transition-all shadow-2xl shadow-lib-blue/30 text-lg uppercase tracking-widest">Apply Filters</button>
                            @if(request()->hasAny(['categories', 'year']))
                                <a href="{{ route('admin.assets.all', request()->only('search')) }}" 
                                   class="flex-1 bg-red-500 hover:bg-red-600 text-white py-5 rounded-3xl font-black text-center transition-all shadow-2xl shadow-red-500/30 text-lg uppercase tracking-widest leading-[3.5rem]">
                                    Clear Filters
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </template>
        
        <!-- Most Viewed Section -->
        <div class="mb-20">
            <div class="text-center mb-10">
                <span class="text-[10px] font-black text-lib-sky uppercase tracking-[0.3em] mb-2 block">Top Trending</span>
                <h2 class="text-3xl font-black text-lib-navy mb-2">Most Viewed</h2>
                <div class="h-1 w-16 bg-lib-sky mx-auto rounded-full opacity-50"></div>
            </div>
            
            @if(isset($mostViewed) && $mostViewed->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    @foreach($mostViewed as $asset)
                        <a href="{{ route('admin.assets.show', $asset->id) }}" class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col relative font-fredoka">
                            <div class="absolute top-2 right-2 z-20">
                                <span class="bg-lib-navy/90 backdrop-blur-md text-white px-2 py-0.5 rounded-lg text-[9px] font-black flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    {{ $asset->views_count }}
                                </span>
                            </div>
                            <div class="aspect-[4/3] relative overflow-hidden bg-slate-100">
                                @if($asset->type === 'photo')
                                    <img src="{{ asset('storage/' . $asset->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $asset->title }}">
                                @elseif($asset->type === 'video')
                                    @if($asset->thumbnail_path)
                                        <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $asset->title }}">
                                    @else
                                        {{-- Video Placeholder --}}
                                        <div class="w-full h-full bg-slate-900 flex items-center justify-center">
                                            <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/20">
                                                <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" /></svg>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/5 group-hover:bg-black/20 transition-colors pointer-events-none">
                                        <div class="w-10 h-10 bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" /></svg>
                                        </div>
                                    </div>
                                @elseif($asset->type === 'ebook')
                                    <div class="w-full h-full flex flex-col items-center justify-center overflow-hidden">
                                        @if($asset->thumbnail_path)
                                            <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            {{-- Premium e-book placeholder --}}
                                            <div class="w-full h-full bg-gradient-to-br from-lib-navy to-lib-sky flex flex-col items-center justify-center text-white p-4 text-center">
                                                <div class="w-10 h-14 bg-white/10 rounded-lg flex items-center justify-center border border-white/20 mb-2 group-hover:scale-110 transition-transform">
                                                    <span class="text-xl">📚</span>
                                                </div>
                                                <span class="text-[7px] font-black uppercase tracking-widest opacity-60">Resource</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex-grow flex flex-col justify-center text-center">
                                <h4 class="text-sm font-bold text-lib-navy line-clamp-2 leading-tight group-hover:text-lib-sky transition-colors">{{ $asset->title }}</h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-white/50 backdrop-blur-sm border-2 border-dashed border-slate-200 rounded-[3rem] py-16 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">🔥</div>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">No trending items yet</p>
                    <p class="text-slate-300 text-xs mt-1">Popular media will appear here automatically.</p>
                </div>
            @endif
        </div>

        <!-- Recently Viewed Section -->
        @if(isset($recentlyViewed) && $recentlyViewed->count() > 0)
            <div class="mb-20">
                <div class="text-center mb-10">
                    <span class="text-[10px] font-black text-lib-sky uppercase tracking-[0.3em] mb-2 block">Quick Access</span>
                    <h2 class="text-3xl font-black text-lib-navy mb-2">Recently Viewed</h2>
                    <div class="h-1 w-16 bg-lib-sky mx-auto rounded-full opacity-50"></div>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    @foreach($recentlyViewed as $asset)
                        <a href="{{ route('admin.assets.show', $asset->id) }}" class="group bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-slate-100 flex flex-col relative font-fredoka">
                            <div class="aspect-[4/3] relative overflow-hidden bg-slate-100">
                                @if($asset->type === 'photo')
                                    <img src="{{ asset('storage/' . $asset->file_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $asset->title }}">
                                @elseif($asset->type === 'video')
                                    @if($asset->thumbnail_path)
                                        <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $asset->title }}">
                                    @else
                                        {{-- Video Placeholder --}}
                                        <div class="w-full h-full bg-slate-900 flex items-center justify-center">
                                            <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/20">
                                                <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" /></svg>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 flex items-center justify-center bg-black/5 group-hover:bg-black/20 transition-colors pointer-events-none">
                                        <div class="w-10 h-10 bg-white/30 backdrop-blur-md rounded-full flex items-center justify-center text-white border border-white/20 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" /></svg>
                                        </div>
                                    </div>
                                @elseif($asset->type === 'ebook')
                                    <div class="w-full h-full flex flex-col items-center justify-center overflow-hidden">
                                        @if($asset->thumbnail_path)
                                            <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            {{-- Premium e-book placeholder --}}
                                            <div class="w-full h-full bg-gradient-to-br from-lib-navy to-lib-sky flex flex-col items-center justify-center text-white p-4 text-center">
                                                <div class="w-10 h-14 bg-white/10 rounded-lg flex items-center justify-center border border-white/20 mb-2 group-hover:scale-110 transition-transform">
                                                    <span class="text-xl">📚</span>
                                                </div>
                                                <span class="text-[7px] font-black uppercase tracking-widest opacity-60">Resource</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex-grow flex flex-col justify-center text-center">
                                <h4 class="text-sm font-bold text-lib-navy line-clamp-2 leading-tight group-hover:text-lib-sky transition-colors">{{ $asset->title }}</h4>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif


        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-black text-lib-navy mb-4">Browse Our Collection</h2>
            <div class="h-1.5 w-24 bg-lib-sky mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- All Media -->
            <a href="{{ route('admin.assets.all') }}" class="access-card group p-8 rounded-[2rem] text-white">
                <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 text-3xl group-hover:scale-110 transition-transform">📂</div>
                <h3 class="text-xl font-bold mb-2">All Media</h3>
                <p class="text-white/70 text-sm leading-relaxed">Comprehensive repository of all digital assets and records.</p>
            </a>

            <!-- Photos -->
            <a href="{{ route('admin.assets.photos') }}" class="access-card group p-8 rounded-[2rem] text-white">
                <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 text-3xl group-hover:scale-110 transition-transform">📸</div>
                <h3 class="text-xl font-bold mb-2">Photography</h3>
                <p class="text-white/70 text-sm leading-relaxed">Capturing history through high-resolution visual archives.</p>
            </a>

            <!-- Videos -->
            <a href="{{ route('admin.assets.videos') }}" class="access-card group p-8 rounded-[2rem] text-white">
                <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 text-3xl group-hover:scale-110 transition-transform">🎬</div>
                <h3 class="text-xl font-bold mb-2">Video Gallery</h3>
                <p class="text-white/70 text-sm leading-relaxed">Multimedia presentations and institutional documentaries.</p>
            </a>

            <!-- Ebooks -->
            <a href="{{ route('admin.assets.ebooks') }}" class="access-card group p-8 rounded-[2rem] text-white">
                <div class="bg-white/10 w-16 h-16 rounded-2xl flex items-center justify-center mb-6 text-3xl group-hover:scale-110 transition-transform">📚</div>
                <h3 class="text-xl font-bold mb-2">Digital Books</h3>
                <p class="text-white/70 text-sm leading-relaxed">Access e-books, journals, and academic publications.</p>
            </a>
        </div>
    </div>
</section>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const dots = document.querySelectorAll('.slide-dot');

    function showSlide(n) {
        if (n >= slides.length) currentSlide = 0;
        if (n < 0) currentSlide = slides.length - 1;
        
        slides.forEach(s => s.classList.remove('active'));
        dots.forEach(d => d.classList.remove('active'));
        
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function changeSlide(n) {
        currentSlide += n;
        showSlide(currentSlide);
    }

    function goToSlide(n) {
        currentSlide = n;
        showSlide(currentSlide);
    }

    // Auto-advance slideshow every 5 seconds
    setInterval(() => changeSlide(1), 5000);
    
    document.addEventListener('DOMContentLoaded', function () {
        showSlide(currentSlide);
    });
</script>
@endsection