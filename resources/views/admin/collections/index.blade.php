@extends('layouts.admin')

@section('title', 'Manage Collections')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
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

    {{-- Header with Action Button --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-lib-navy uppercase tracking-tighter mb-2">Manage Collections</h1>
            <p class="text-slate-500 font-medium text-lg">Grouped media albums and events. Organize and bulk upload your corporate assets.</p>
        </div>
        
        {{-- Action Button --}}
        <div class="flex-shrink-0">
            <a href="{{ route('admin.collections.create') }}" 
               class="inline-flex items-center gap-2 bg-lib-navy text-white hover:bg-lib-sky px-6 py-3 rounded-xl font-black transition-all shadow-lg hover:shadow-xl">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                NEW BULK UPLOAD
            </a>
        </div>
    </div>

        {{-- Collections Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            @forelse($collections as $col)
                <a href="{{ route('admin.collections.show', $col->name) }}" class="group block relative">
                    <div class="relative bg-white rounded-[3rem] p-4 shadow-xl shadow-slate-200/50 border border-slate-100 transition-all duration-700 hover:shadow-2xl hover:scale-[1.02] font-fredoka">
                        {{-- Multi-layered Album Stack Effect --}}
                        <div class="absolute -top-3 left-10 right-10 h-10 bg-white/40 rounded-[2rem] -z-10 group-hover:-top-6 transition-all duration-700 shadow-sm border border-slate-100"></div>
                        <div class="absolute -top-1.5 left-6 right-6 h-6 bg-white/60 rounded-[2rem] -z-10 group-hover:-top-3 transition-all duration-700 shadow-sm border border-slate-100"></div>

                        {{-- Card Content --}}
                        <div class="relative h-72 rounded-[2.5rem] overflow-hidden bg-slate-100 mb-8">
                            @if($col->thumbnail_path)
                                @if(strtolower($col->type) === 'video')
                                    <video src="{{ asset('storage/' . $col->thumbnail_path) }}" class="w-full h-full object-cover"></video>
                                    <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
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
                                <div class="w-full h-full bg-gradient-to-br from-lib-sky to-lib-blue flex items-center justify-center text-5xl">🖼️</div>
                            @endif

                            {{-- Overlay info --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-700 flex flex-col justify-end p-10">
                                <p class="text-white font-black text-sm uppercase tracking-widest translate-y-4 group-hover:translate-y-0 transition-transform duration-700">Manage Album</p>
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
                                    {{ \Carbon\Carbon::parse($col->date)->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-32 text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2 2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <h3 class="text-2xl font-black text-lib-navy mb-2">No collections yet</h3>
                    <p class="text-slate-400 font-medium mb-8">Start by bulk uploading media groups.</p>
                    <a href="{{ route('admin.collections.create') }}" class="inline-flex items-center gap-2 bg-lib-navy text-white px-6 py-3 rounded-xl font-bold uppercase text-xs tracking-widest shadow-lg">
                        Create Your First Album
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
