@extends('layouts.app')

@section('title', 'Add Media')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-6">
        <a href="{{ route('media.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-lib-navy transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Back to media
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
        <h1 class="text-xl font-bold text-lib-navy mb-1">Add Media</h1>
        <p class="text-sm text-slate-500 mb-6">Upload a photo, video, or book to the library.</p>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm mb-5">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @php
            $floatInput    = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
            $floatLabel    = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
            $staticLabel   = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky pointer-events-none';
            $floatSelect   = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white transition-colors appearance-none';
        @endphp

        <form method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data" class="space-y-7"
              x-data="{ source: '{{ old('source', 'upload') }}', type: '{{ old('type', 'photo') }}' }">
            @csrf

            <div class="relative">
                <input id="title" type="text" name="title" value="{{ old('title') }}" required placeholder=" "
                       class="{{ $floatInput }}">
                <label for="title" class="{{ $floatLabel }}">Title</label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="relative">
                    <select id="type" name="type" required x-model="type" class="{{ $floatSelect }}">
                        <option value="photo">Photo</option>
                        <option value="video">Video</option>
                        <option value="ebook">Book</option>
                    </select>
                    <label for="type" class="{{ $staticLabel }}">Type</label>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>
                <div class="relative">
                    @php $status = old('status', 'draft'); @endphp
                    <select id="status" name="status" required class="{{ $floatSelect }}">
                        <option value="draft"     {{ $status === 'draft'     ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $status === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived"  {{ $status === 'archived'  ? 'selected' : '' }}>Archived</option>
                    </select>
                    <label for="status" class="{{ $staticLabel }}">Status</label>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </span>
                </div>
                <div class="relative">
                    <input id="date" type="date" name="date" value="{{ old('date') }}" placeholder=" "
                           class="{{ $floatInput }}">
                    <label for="date" class="{{ $staticLabel }}">Date</label>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 p-5">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">Source</label>

                {{-- Source toggle --}}
                <div class="inline-flex rounded-full bg-slate-100 p-1 mb-4">
                    <button type="button" @click="source = 'upload'"
                            :class="source === 'upload' ? 'bg-white text-lib-navy shadow' : 'text-slate-500 hover:text-lib-navy'"
                            class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors">
                        Upload file
                    </button>
                    <button type="button" @click="source = 'link'"
                            :class="source === 'link' ? 'bg-white text-lib-navy shadow' : 'text-slate-500 hover:text-lib-navy'"
                            class="px-4 py-1.5 rounded-full text-xs font-bold transition-colors">
                        Use link
                    </button>
                </div>
                <input type="hidden" name="source" x-model="source">

                {{-- Upload mode --}}
                <div x-show="source === 'upload'" x-cloak>
                    <input id="file" type="file" name="file" :required="source === 'upload'"
                           :accept="type === 'photo' ? 'image/*' : (type === 'video' ? 'video/*' : '.pdf,.doc,.docx,.xls,.xlsx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')"
                           class="w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-lib-light file:text-lib-navy hover:file:bg-lib-sky hover:file:text-white transition-colors">
                    <p class="text-xs text-slate-400 mt-1.5">
                        Max 50 MB.
                        <span x-show="type === 'photo'">Accepted: JPG, PNG, GIF, WEBP, BMP, SVG.</span>
                        <span x-show="type === 'video'" x-cloak>Accepted: MP4, WEBM, MOV, AVI, MKV.</span>
                        <span x-show="type === 'ebook'" x-cloak>Accepted: PDF, DOC, DOCX, XLS, XLSX.</span>
                    </p>
                </div>

                {{-- Link mode --}}
                <div x-show="source === 'link'" x-cloak>
                    <div class="relative">
                        <input id="file_url" type="url" name="file_url" value="{{ old('file_url') }}"
                               :required="source === 'link'"
                               placeholder=" "
                               autocomplete="off"
                               autocorrect="off"
                               autocapitalize="off"
                               spellcheck="false"
                               class="{{ $floatInput }}">
                        <label for="file_url" class="{{ $floatLabel }}">File URL</label>
                    </div>
                    <p class="text-xs text-slate-400 mt-1.5">Paste a direct URL to a hosted file (image, video, or PDF).</p>
                </div>
            </div>

            {{-- Book-only: physical/shelf location --}}
            <div x-show="type === 'ebook'" x-cloak>
                <div class="relative">
                    <input id="location" type="text" name="location" value="{{ old('location') }}" maxlength="255"
                           placeholder=" "
                           class="{{ $floatInput }}">
                    <label for="location" class="{{ $floatLabel }}">
                        Location <span class="font-normal text-slate-400">(optional)</span>
                    </label>
                </div>
                <p class="text-xs text-slate-400 mt-1.5">Where the physical copy is shelved, if applicable.</p>
            </div>

            {{-- Collection --}}
            <div class="relative">
                @php $oldCollection = (int) old('collection_id'); @endphp
                <select id="collection_id" name="collection_id" class="{{ $floatSelect }}">
                    <option value="">— None —</option>
                    @foreach($collections as $col)
                        <option value="{{ $col->id }}" {{ $oldCollection === $col->id ? 'selected' : '' }}>{{ $col->name }}</option>
                    @endforeach
                </select>
                <label for="collection_id" class="{{ $staticLabel }}">
                    Collection <span class="font-normal text-slate-400">(optional)</span>
                </label>
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </span>
            </div>

            <div class="rounded-2xl border border-slate-200 p-5">
                <label for="thumbnail" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-3">
                    Thumbnail <span class="font-normal normal-case text-slate-400">(optional)</span>
                </label>
                <input id="thumbnail" type="file" name="thumbnail" accept="image/*"
                       class="w-full text-sm text-slate-600 file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-600 hover:file:bg-slate-200 transition-colors">
                <p class="text-xs text-slate-400 mt-1.5">Cover image shown in listings. Max 1 MB.</p>
            </div>

            <div class="rounded-2xl border border-slate-200 p-5" x-data="{ selected: @js(array_map('intval', old('categories', []))) }">
                <div class="flex items-center justify-between mb-3 gap-3 flex-wrap">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">
                        Categories <span class="font-normal normal-case text-slate-400">(select one or more)</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-slate-400">
                            <span x-text="selected.length"></span> selected
                        </span>
                        <a href="{{ route('categories.index') }}" target="_blank"
                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-lib-light text-lib-sky hover:bg-lib-sky hover:text-white text-[10px] font-bold transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Manage
                        </a>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($categories as $cat)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                   x-model.number="selected"
                                   class="peer hidden">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-slate-50 text-slate-600 border border-slate-200 peer-checked:bg-lib-navy peer-checked:text-white peer-checked:border-lib-navy hover:border-lib-sky transition-colors">
                                <svg x-show="selected.includes({{ $cat->id }})" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-3 w-3"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                {{ $cat->name }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            @if($tags->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 p-5" x-data="{ selected: @js(array_map('intval', old('tags', []))) }">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider">
                            Tags <span class="font-normal normal-case text-slate-400">(select one or more)</span>
                        </label>
                        <span class="text-[10px] font-bold text-slate-400">
                            <span x-text="selected.length"></span> selected
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                       x-model.number="selected"
                                       class="peer hidden">
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
                <a href="{{ route('media.index') }}"
                   class="px-5 py-2.5 rounded-full text-sm font-bold text-slate-600 hover:bg-slate-100 transition-colors">Cancel</a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-sm font-bold transition-colors shadow-md">
                    Upload media
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
