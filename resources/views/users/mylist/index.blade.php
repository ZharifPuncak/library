@extends('layouts.app')

@section('title', 'My List')

@section('content')
@php
    $defaultThumb = asset('images/logo.png');
    $typeLabels   = ['photo' => 'Photo', 'video' => 'Video', 'ebook' => 'Book'];
@endphp

<div class="bg-slate-50 min-h-[calc(100vh-5rem)]">
    <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(session('status'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl text-sm font-medium">
                {{ session('status') }}
            </div>
        @endif

        <a href="{{ route('media.index') }}"
           class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Back to media
        </a>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">

            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-5">
                <div>
                    <h1 class="text-xl font-bold text-lib-navy">My List</h1>
                    <p class="text-sm text-slate-500">Your personal collection of saved media and collections.</p>
                </div>

                <a href="{{ route('mylist.add') }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add to My List
                </a>
            </div>

            {{-- Saved media --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-lib-navy">Saved media</h2>
                    <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">{{ $items->total() }} {{ Str::plural('item', $items->total()) }}</span>
                </div>

                @if($items->isEmpty())
                    <div class="py-10 text-center text-slate-400 text-sm border border-dashed border-slate-200 rounded-2xl">
                        No saved media yet.
                    </div>
                @else
                    <div class="flex flex-col divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                        @foreach($items as $asset)
                            @php
                                $type      = strtolower($asset->type);
                                $typeLabel = $typeLabels[$type] ?? ucfirst($asset->type);
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
                            <div class="group flex items-center gap-4 p-3 hover:bg-slate-50 transition-colors">
                                <a href="{{ route('media.show', $asset) }}" class="flex-shrink-0">
                                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-slate-100">
                                        <img src="{{ $thumbUrl }}" alt="{{ $asset->title }}"
                                             class="w-full h-full {{ $hasRealThumb ? 'object-cover' : 'object-contain p-1 bg-white' }}"
                                             loading="lazy"
                                             onerror="this.onerror=null; this.src='{{ $defaultThumb }}'; this.classList.remove('object-cover'); this.classList.add('object-contain','p-1','bg-white');">
                                    </div>
                                </a>
                                <a href="{{ route('media.show', $asset) }}" class="flex-grow min-w-0">
                                    <h3 class="text-sm font-bold text-slate-800 truncate hover:text-lib-sky transition-colors">{{ $asset->title }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        {{ $typeLabel }} &middot; saved {{ $asset->pivot->created_at?->diffForHumans() }}
                                    </p>
                                </a>
                                <form method="POST" action="{{ route('mylist.destroy', $asset) }}" class="flex-shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-red-50 hover:bg-red-500 text-red-600 hover:text-white transition-colors"
                                            title="Remove from My List">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    @if($items->hasPages())
                        <div class="mt-3">{{ $items->links() }}</div>
                    @endif
                @endif
            </div>

            {{-- Saved collections --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-bold text-lib-navy">Saved collections</h2>
                    <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full">{{ $collections->total() }} {{ Str::plural('collection', $collections->total()) }}</span>
                </div>

                @if($collections->isEmpty())
                    <div class="py-10 text-center text-slate-400 text-sm border border-dashed border-slate-200 rounded-2xl">
                        No saved collections yet.
                    </div>
                @else
                    <div class="flex flex-col divide-y divide-slate-100 border border-slate-100 rounded-2xl overflow-hidden">
                        @foreach($collections as $col)
                            <div class="group flex items-center gap-4 p-3 hover:bg-slate-50 transition-colors">
                                <a href="{{ route('collections.show', $col) }}" class="flex-shrink-0">
                                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-lib-light flex items-center justify-center text-lib-sky">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                </a>
                                <a href="{{ route('collections.show', $col) }}" class="flex-grow min-w-0">
                                    <h3 class="text-sm font-bold text-slate-800 truncate hover:text-lib-sky transition-colors">{{ $col->name }}</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Collection &middot; saved {{ $col->pivot->created_at?->diffForHumans() }}</p>
                                </a>
                                <form method="POST" action="{{ route('mylist.collections.destroy', $col) }}" class="flex-shrink-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-red-50 hover:bg-red-500 text-red-600 hover:text-white transition-colors"
                                            title="Remove from My List">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    @if($collections->hasPages())
                        <div class="mt-3">{{ $collections->links() }}</div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
