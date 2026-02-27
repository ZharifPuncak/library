@extends('layouts.admin')

@section('title', 'Bulk Upload Collection')

@section('content')
<div class="px-6 py-12 bg-[#f8fafc] min-h-screen font-fredoka">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-lib-navy tracking-tight mb-2">New Collection</h1>
                <p class="text-slate-500 font-medium">Bulk upload multiple items into a single grouped album.</p>
            </div>
            <a href="{{ route('admin.collections.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-lib-navy font-bold transition-colors">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Collections
            </a>
        </div>

        <form action="{{ route('admin.collections.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200/50 p-10 border border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Collection Identity --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Collection Name (e.g. Singapore Awards 2025)</label>
                            <input type="text" name="collection_name" required placeholder="Enter album name..."
                                   class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-lib-sky/10 focus:border-lib-sky focus:bg-white transition-all text-lib-navy font-bold">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Media Type</label>
                            <select name="type" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-lib-sky/10 focus:border-lib-sky focus:bg-white transition-all text-lib-navy font-bold appearance-none">
                                <option value="mixed">🔄 Mixed Media (Auto-detect)</option>
                                <option value="photo">📸 Photos</option>
                                <option value="video">🎬 Videos</option>
                                <option value="ebook">📚 e-books</option>
                            </select>
                        </div>
                    </div>

                    {{-- Classification --}}
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Shared Category</label>
                            <select name="category_id" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-lib-sky/10 focus:border-lib-sky focus:bg-white transition-all text-lib-navy font-bold appearance-none">
                                <option value="">Select Category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Shared Tags</label>
                            <select name="tags[]" multiple class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-lib-sky/10 focus:border-lib-sky focus:bg-white transition-all text-lib-navy font-bold h-32">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">#{{ $tag->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-[10px] text-slate-400 font-medium italic">Hold Ctrl/Cmd to select multiple tags</p>
                        </div>
                    </div>
                </div>

                <hr class="my-10 border-slate-100">

                {{-- Bulk Upload Section --}}
                <div x-data="{ files: [] }">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 ml-1">Upload Media Files</label>
                    
                    <div class="relative group">
                        <input type="file" name="files[]" id="bulk-files" multiple required
                               @change="files = Array.from($event.target.files)"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div class="border-4 border-dashed border-slate-100 rounded-[2rem] p-12 text-center group-hover:border-lib-sky/30 group-hover:bg-lib-light/30 transition-all duration-500">
                            <div class="w-20 h-20 bg-lib-light rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-500">
                                <svg class="h-10 w-10 text-lib-sky" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <h3 class="text-xl font-black text-lib-navy mb-2">Drop files here or click to browse</h3>
                            <p class="text-slate-400 font-medium">Select multiple photos, videos, or ebooks. You can even mix them!</p>
                        </div>
                    </div>

                    {{-- Preview List --}}
                    <div x-show="files.length > 0" x-cloak class="mt-8 space-y-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-4">Files to upload (<span x-text="files.length"></span>):</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <template x-for="file in files" :key="file.name">
                                <div class="flex items-center gap-4 bg-slate-50 p-3 rounded-2xl border border-slate-100">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-lg shadow-sm">📄</div>
                                    <div class="min-w-0 flex-grow">
                                        <p class="text-xs font-bold text-lib-navy truncate" x-text="file.name"></p>
                                        <p class="text-[9px] text-slate-400 font-black" x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="mt-12">
                    <button type="submit" class="w-full bg-lib-navy hover:bg-lib-blue text-white py-6 rounded-2xl font-black text-lg uppercase tracking-widest shadow-xl shadow-lib-navy/20 hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        Publish Collection
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
