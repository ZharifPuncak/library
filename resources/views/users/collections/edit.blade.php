@extends('layouts.app')

@section('title', 'Edit Collection')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    <a href="{{ route('collections.show', $collection) }}"
       class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        Back to collection
    </a>

    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-sm border border-slate-100 p-8">
        <h1 class="text-xl font-bold text-lib-navy mb-1">Edit Collection</h1>
        <p class="text-sm text-slate-500 mb-6">Update collection metadata. Files and book locations are managed from the collection detail page.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 text-sm mb-5">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @php
            $floatInput  = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
            $floatLabel  = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
            $staticLabel = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky pointer-events-none';
        @endphp

        <form method="POST" action="{{ route('collections.update', $collection) }}" enctype="multipart/form-data" class="space-y-7">
            @csrf
            @method('PUT')

            <div class="relative">
                <input id="name" type="text" name="name" value="{{ old('name', $collection->name) }}" required placeholder=" "
                       class="{{ $floatInput }}">
                <label for="name" class="{{ $floatLabel }}">Collection name</label>
            </div>

            <div class="relative">
                <textarea id="description" name="description" rows="3" maxlength="5000" placeholder=" "
                          class="{{ $floatInput }} min-h-24 resize-y">{{ old('description', $collection->description) }}</textarea>
                <label for="description" class="{{ $floatLabel }}">
                    Description <span class="font-normal text-slate-400">(optional)</span>
                </label>
            </div>

            <div class="relative">
                <input id="date" type="date" name="date" value="{{ old('date', $sharedDate) }}" placeholder=" "
                       class="{{ $floatInput }}">
                <label for="date" class="{{ $staticLabel }}">Date (applied to all)</label>
            </div>

            <div class="rounded-lg border border-slate-200 p-5">
                <label for="thumbnail" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
                    Replace thumbnail <span class="font-normal normal-case text-slate-400">(optional - applies to all items)</span>
                </label>
                @if($collection->thumbnail_path)
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ asset('storage/' . $collection->thumbnail_path) }}" alt="Current thumbnail"
                             class="w-14 h-14 rounded-xl object-cover border border-slate-200">
                        <span class="text-xs text-slate-500">Current thumbnail</span>
                    </div>
                @endif
                <input id="thumbnail" type="file" name="thumbnail" accept="image/*"
                       class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200 transition-colors">
                <p class="text-xs text-slate-400 mt-1.5">Leave empty to keep the existing thumbnail. Max 1 MB.</p>
            </div>

            <div class="rounded-lg border border-slate-200 p-5" x-data="{ selected: @js(array_map('intval', old('categories', $sharedCatIds))) }">
                <div class="flex items-center justify-between mb-3 gap-3 flex-wrap">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">
                        Categories <span class="font-normal normal-case text-slate-400">(applied to all items)</span>
                    </label>
                    <span class="text-[10px] font-bold text-slate-400">
                        <span x-text="selected.length"></span> selected
                    </span>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                   x-model.number="selected" class="peer hidden">
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200 peer-checked:bg-lib-navy peer-checked:text-white peer-checked:border-lib-navy hover:border-lib-sky transition-colors">
                                <svg x-show="selected.includes({{ $cat->id }})" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                {{ $cat->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            @if($tags->isNotEmpty())
                <div class="rounded-lg border border-slate-200 p-5" x-data="{ selected: @js(array_map('intval', old('tags', $sharedTagIds))) }">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">
                            Tags <span class="font-normal normal-case text-slate-400">(applied to all items)</span>
                        </label>
                        <span class="text-[10px] font-bold text-slate-400">
                            <span x-text="selected.length"></span> selected
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                       x-model.number="selected" class="peer hidden">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-50 text-slate-600 border border-slate-200 peer-checked:bg-lib-sky peer-checked:text-white peer-checked:border-lib-sky hover:border-lib-sky transition-colors">
                                    <svg x-show="selected.includes({{ $tag->id }})" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    #{{ $tag->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <a href="{{ route('collections.show', $collection) }}"
                   class="px-4 py-2 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit"
                        class="px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
