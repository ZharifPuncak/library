@extends('layouts.app')

@section('title', 'Add to My List')

@section('content')
@php $defaultThumb = asset('images/logo.png'); @endphp

<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('mylist.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Back to My List
    </a>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h1 class="text-xl font-bold text-lib-navy mb-1">Add to My List</h1>
        <p class="text-sm text-slate-500 mb-6">Pick the media and collections you want saved. Untick to remove from your list.</p>

        <form method="POST" action="{{ route('mylist.sync') }}" class="space-y-6"
              x-data="{
                  tab: 'media',
                  selectedMedia: @js(array_map('intval', $assignedMedia)),
                  selectedCollections: @js(array_map('intval', $assignedCollections)),
                  mediaFilter: '',
                  colFilter: '',
                  toggleMedia(id) {
                      const i = this.selectedMedia.indexOf(id);
                      if (i === -1) this.selectedMedia.push(id); else this.selectedMedia.splice(i, 1);
                  },
                  toggleCollection(id) {
                      const i = this.selectedCollections.indexOf(id);
                      if (i === -1) this.selectedCollections.push(id); else this.selectedCollections.splice(i, 1);
                  }
              }">
            @csrf

            {{-- Tabs --}}
            <nav class="flex items-center gap-1 border-b border-slate-200">
                <button type="button" @click="tab = 'media'"
                        :class="tab === 'media' ? 'border-lib-sky text-lib-navy' : 'border-transparent text-slate-500 hover:text-lib-navy hover:border-slate-300'"
                        class="inline-flex items-center gap-2 px-4 py-2.5 -mb-px text-sm font-bold border-b-2 transition-colors">
                    Media
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                          :class="tab === 'media' ? 'bg-lib-sky text-white' : 'bg-slate-100 text-slate-500'"
                          x-text="selectedMedia.length"></span>
                </button>
                <button type="button" @click="tab = 'collections'"
                        :class="tab === 'collections' ? 'border-lib-sky text-lib-navy' : 'border-transparent text-slate-500 hover:text-lib-navy hover:border-slate-300'"
                        class="inline-flex items-center gap-2 px-4 py-2.5 -mb-px text-sm font-bold border-b-2 transition-colors">
                    Collections
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                          :class="tab === 'collections' ? 'bg-lib-sky text-white' : 'bg-slate-100 text-slate-500'"
                          x-text="selectedCollections.length"></span>
                </button>
            </nav>

            {{-- Media picker --}}
            <div x-show="tab === 'media'">
                <div class="relative mb-4">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="search" x-model="mediaFilter" placeholder="Filter media by title…"
                           autocomplete="off"
                           class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/30 transition-colors">
                </div>

                @if($media->isEmpty())
                    <div class="py-12 text-center text-slate-400 text-sm">No media available.</div>
                @else
                    <div class="max-h-[480px] overflow-y-auto pr-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($media as $m)
                            @php
                                $thumb = $m->thumbnail_path
                                    ? asset('storage/' . $m->thumbnail_path)
                                    : ($m->type === 'photo' && $m->file_path ? asset('storage/' . $m->file_path) : null);
                                $hasThumb = !empty($thumb);
                            @endphp
                            <label class="cursor-pointer block"
                                   x-show="mediaFilter === '' || @js(strtolower($m->title)).includes(mediaFilter.toLowerCase())">
                                <input type="checkbox" name="media[]" value="{{ $m->id }}"
                                       @change="toggleMedia({{ $m->id }})"
                                       {{ in_array($m->id, $assignedMedia, true) ? 'checked' : '' }}
                                       class="peer hidden">
                                <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-lib-sky peer-checked:bg-lib-light hover:border-slate-300 transition-colors">
                                    <div class="w-12 h-12 flex-shrink-0 rounded-lg overflow-hidden bg-slate-100">
                                        <img src="{{ $thumb ?? $defaultThumb }}" alt=""
                                             class="w-full h-full {{ $hasThumb ? 'object-cover' : 'object-contain p-1 bg-white' }}">
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ $m->title }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $m->type }}</p>
                                    </div>
                                    <span class="w-5 h-5 rounded-md border-2 border-slate-300 flex items-center justify-center flex-shrink-0"
                                          :class="selectedMedia.includes({{ $m->id }}) ? 'border-lib-sky bg-lib-sky' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" class="h-3 w-3"
                                             x-show="selectedMedia.includes({{ $m->id }})"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Collections picker --}}
            <div x-show="tab === 'collections'" x-cloak>
                <div class="relative mb-4">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="search" x-model="colFilter" placeholder="Filter collections by name…"
                           autocomplete="off"
                           class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-lib-sky focus:ring-2 focus:ring-lib-sky/30 transition-colors">
                </div>

                @if($collections->isEmpty())
                    <div class="py-12 text-center text-slate-400 text-sm">No collections available.</div>
                @else
                    <div class="max-h-[480px] overflow-y-auto pr-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($collections as $col)
                            <label class="cursor-pointer block"
                                   x-show="colFilter === '' || @js(strtolower($col->name)).includes(colFilter.toLowerCase())">
                                <input type="checkbox" name="collections[]" value="{{ $col->id }}"
                                       @change="toggleCollection({{ $col->id }})"
                                       {{ in_array($col->id, $assignedCollections, true) ? 'checked' : '' }}
                                       class="peer hidden">
                                <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-lib-sky peer-checked:bg-lib-light hover:border-slate-300 transition-colors">
                                    <div class="w-12 h-12 flex-shrink-0 rounded-lg bg-lib-light flex items-center justify-center text-lib-sky">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                    <div class="min-w-0 flex-grow">
                                        <p class="text-sm font-bold text-slate-800 truncate">{{ $col->name }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">Collection</p>
                                    </div>
                                    <span class="w-5 h-5 rounded-md border-2 border-slate-300 flex items-center justify-center flex-shrink-0"
                                          :class="selectedCollections.includes({{ $col->id }}) ? 'border-lib-sky bg-lib-sky' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" class="h-3 w-3"
                                             x-show="selectedCollections.includes({{ $col->id }})"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('mylist.index') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-sm font-bold transition-colors shadow-md">
                    Save list
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
