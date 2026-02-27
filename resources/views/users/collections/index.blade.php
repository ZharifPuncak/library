@extends('layouts.app')

@section('title', 'Media Collections')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    {{-- Header --}}
    <div class="mb-10">
        <h1 class="text-4xl font-black text-lib-navy uppercase tracking-tighter mb-2">Media Collections</h1>
        <p class="text-slate-500 font-medium text-lg">Grouped media albums and events. Explore our curated collections of photos, videos, and e-books.</p>
    </div>

        {{-- Collections Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($collections as $col)
                <a href="{{ route('collections.show', $col->name) }}" class="group block relative">
                    <div class="relative bg-white rounded-[3rem] p-4 shadow-xl shadow-slate-200/50 border border-slate-100 transition-all duration-700 hover:shadow-2xl hover:scale-[1.02] font-fredoka">
                        {{-- Multi-layered Album Stack Effect --}}
                        <div class="absolute -top-3 left-10 right-10 h-10 bg-white/40 rounded-[2rem] -z-10 group-hover:-top-6 transition-all duration-700 shadow-sm border border-slate-100"></div>
                        <div class="absolute -top-1.5 left-6 right-6 h-6 bg-white/60 rounded-[2rem] -z-10 group-hover:-top-3 transition-all duration-700 shadow-sm border border-slate-100"></div>

                        {{-- Card Content --}}
                        <div class="relative h-72 rounded-[2.5rem] overflow-hidden bg-slate-100 mb-8">
                            @if($col->thumbnail_path)
                                @if(strtolower($col->type) === 'video')
                                    <video src="{{ asset('storage/' . $col->thumbnail_path) }}" class="w-full h-full object-cover" muted playsinline></video>
                                    <div class="absolute inset-0 bg-black/10 flex items-center justify-center opacity-100 group-hover:opacity-0 transition-opacity">
                                        <div class="w-16 h-16 bg-white/20 backdrop-blur-xl rounded-full flex items-center justify-center text-white border border-white/20">
                                            <svg class="h-8 w-8 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                @else
                                    <img src="{{ asset('storage/' . $col->thumbnail_path) }}" 
                                         class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" 
                                         alt="{{ $col->name }}">
                                @endif
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-lib-navy to-lib-sky flex flex-col items-center justify-center text-white">
                                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 mb-3 group-hover:scale-110 transition-transform">
                                        <span class="text-4xl">📂</span>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Empty Collection</span>
                                </div>
                            @endif

                            {{-- Overlay info --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700 flex flex-col justify-end p-10">
                                <p class="text-white font-black text-sm uppercase tracking-widest translate-y-4 group-hover:translate-y-0 transition-transform duration-700">Open Album</p>
                            </div>
                        </div>

                        <div class="px-6 pb-6 text-center">
                            <h3 class="text-2xl font-black text-lib-navy mb-3 group-hover:text-lib-sky transition-colors leading-tight">
                                {{ $col->name }}
                            </h3>
                            <div class="flex items-center justify-center gap-3">
                                <span class="bg-lib-light text-lib-sky px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">
                                    {{ $col->count }} Items
                                </span>
                                <span class="text-slate-300">•</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                                    {{ \Carbon\Carbon::parse($col->date)->format('Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-32 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h2 class="text-3xl font-black text-lib-navy mb-4">No collections found</h2>
                    <p class="text-slate-400 font-medium">Please check back later for new albums.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
