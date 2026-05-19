@extends('layouts.app')

@section('title', 'Edit Collection')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('collections.show', $collection) }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Back to collection
    </a>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h1 class="text-xl font-bold text-lib-navy mb-1">Edit Collection</h1>
        <p class="text-sm text-slate-500 mb-6">Rename or change which media belongs to this collection.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm mb-5">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @php
            $floatInput = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
            $floatLabel = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
            $defaultThumb = asset('images/logo.png');
        @endphp

        <form method="POST" action="{{ route('collections.update', $collection) }}" class="space-y-6"
              x-data="{
                  selected: @js(array_map('intval', old('media', $assigned))),
                  filter: '',
                  toggle(id) {
                      const i = this.selected.indexOf(id);
                      if (i === -1) this.selected.push(id);
                      else this.selected.splice(i, 1);
                  }
              }">
            @csrf
            @method('PUT')

            <div class="relative">
                <input id="name" type="text" name="name" value="{{ old('name', $collection->name) }}" required placeholder=" "
                       class="{{ $floatInput }}">
                <label for="name" class="{{ $floatLabel }}">Collection name</label>
            </div>

            <div class="rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Media in this collection</label>
                        <p class="text-xs text-slate-400 mt-0.5">Tick items to keep them, untick to remove from the collection.</p>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-3 py-1 rounded-full whitespace-nowrap">
                        <span x-text="selected.length"></span> selected
                    </span>
                </div>

                <div class="relative mb-4">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="search" x-model="filter" placeholder="Filter media by title…"
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
                                   x-show="filter === '' || @js(strtolower($m->title)).includes(filter.toLowerCase())">
                                <input type="checkbox" name="media[]" value="{{ $m->id }}"
                                       @change="toggle({{ $m->id }})"
                                       {{ in_array($m->id, old('media', $assigned), true) ? 'checked' : '' }}
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
                                          :class="selected.includes({{ $m->id }}) ? 'border-lib-sky bg-lib-sky' : ''">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="white" class="h-3 w-3"
                                             x-show="selected.includes({{ $m->id }})"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <a href="{{ route('collections.show', $collection) }}"
                   class="px-5 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-sm font-bold transition-colors shadow-md">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
