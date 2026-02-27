@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-10">
    @php
        $backRoute = route('admin.assets.all');
        if (strtolower($asset->type) === 'photo') $backRoute = route('admin.assets.photos');
        if (strtolower($asset->type) === 'video') $backRoute = route('admin.assets.videos');
        if (strtolower($asset->type) === 'ebook') $backRoute = route('admin.assets.ebooks');
    @endphp

    <a href="{{ $backRoute }}" 
        class="inline-flex items-center gap-3 text-slate-500 hover:text-lib-navy font-black mb-8 transition-all group active:scale-95"
        style="text-decoration: none; font-family: 'Fredoka', sans-serif;">
        <div class="bg-white p-2.5 rounded-xl shadow-sm group-hover:shadow-md transition-all border border-slate-100 flex items-center justify-center">
            <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
        </div>
        <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Back</span>
    </a>

    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl relative overflow-hidden">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50 rounded-full blur-3xl -mr-32 -mt-32 pointer-events-none"></div>

        <div class="relative flex items-center justify-between mb-10">
            <h2 class="text-3xl font-black text-lib-navy flex items-center gap-3">
                <span class="text-4xl">✏️</span>
                <span>Edit Asset: <span class="text-lib-blue">{{ $asset->title }}</span></span>
            </h2>
            <span class="bg-slate-100 text-slate-500 px-4 py-2 rounded-xl font-bold text-sm uppercase tracking-wider border border-slate-200">
                {{ ucfirst($asset->type) }}
            </span>
        </div>

    <form action="{{ route('admin.assets.update', $asset->id) }}" method="POST" enctype="multipart/form-data" id="assetForm" class="space-y-8">
        @csrf
        @method('PUT')

        <input type="hidden" name="type" value="{{ $asset->type }}">

        {{-- COMMON FIELDS --}}
        <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100">
            <h3 class="text-lib-navy font-black text-lg mb-6 flex items-center gap-2 uppercase tracking-wide">
                <span>📋</span> Basic Information
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="group">
                    <label for="title" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                        Title / Rename
                    </label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title', $asset->title) }}" 
                           required 
                           class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all text-lg placeholder-slate-300"
                           oninput="updatePreview(this.value)">
                </div>
                <div class="group">
                    <label for="file" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                        Update {{ ucfirst($asset->type) }} File
                        <span class="normal-case opacity-50 ml-1 font-normal">(Leave blank to keep existing)</span>
                    </label>
                    <div class="relative">
                        <input type="file" 
                               name="file" 
                               id="file" 
                               @if(strtolower($asset->type) == 'photo') accept="image/*"
                               @elseif(strtolower($asset->type) == 'video') accept="video/*"
                               @elseif(strtolower($asset->type) == 'ebook') accept=".pdf,.epub,.mobi"
                               @endif
                               class="peer w-full bg-white border-2 border-dashed border-slate-200 rounded-2xl p-3 text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-lib-blue file:text-white hover:file:bg-lib-navy hover:border-lib-sky transition-all cursor-pointer"
                               onchange="handleFileChange(this)">
                    </div>
                    <div id="filePreview" class="mt-3 hidden">
                        <span class="inline-flex items-center gap-2 bg-green-50 text-green-600 px-3 py-1 rounded-lg text-xs font-bold border border-green-100">
                            📎 <span id="fileName"></span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Date --}}
            <div class="mt-6 mb-2">
                <label for="date" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                    📅 Date
                </label>
                <input 
                    type="date" 
                    name="date"
                    id="date"
                    value="{{ old('date', $asset->date ? $asset->date->format('Y-m-d') : date('Y-m-d')) }}"
                    required
                    class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all text-lg cursor-pointer">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Category --}}
                <div>
                    <label for="category_id" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                        🗂 Category
                    </label>
                    <div class="relative">
                        <select 
                            id="category_id" 
                            name="category_id" 
                            required
                            class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all appearance-none cursor-pointer">
                            
                            <option value="">Select Category</option>

                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    @if($asset->categories->contains($category->id)) selected @endif>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                        </div>
                    </div>
                </div>

                {{-- Tags --}}
                <div>
                    <label for="tags" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                        🏷 Tags
                    </label>

                    <select 
                        id="tags" 
                        name="tags[]" 
                        multiple
                        class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all h-[58px]">
                        
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}"
                                @if($asset->tags->contains($tag->id)) selected @endif>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>

                    <small class="block mt-2 text-slate-400 text-xs font-bold pl-1">
                        Hold Ctrl / Cmd to select multiple
                    </small>
                </div>
            </div>

            {{-- Collection --}}
            <div class="mt-6">
                <label for="collection" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                    📁 Collection (Album Name)
                </label>
                <input 
                    type="text" 
                    name="collection"
                    id="collection"
                    list="collection_list"
                    value="{{ old('collection', $asset->getDetail('collection')) }}"
                    placeholder="Enter collection name..."
                    class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all">
                <datalist id="collection_list">
                    @foreach(\App\Models\AssetDetail::where('key', 'collection')->distinct()->pluck('value') as $coll)
                        <option value="{{ $coll }}">
                    @endforeach
                </datalist>
                <small class="block mt-2 text-slate-400 text-xs font-bold pl-1">
                    Enter a new name or select from existing collections.
                </small>
            </div>
        </div>

        {{-- Live Preview Badge --}}
        <div id="titlePreview" class="bg-gradient-to-r from-lib-blue to-lib-navy text-white p-5 rounded-3xl text-center shadow-lg transform scale-95 opacity-0 transition-all duration-300 hidden">
            <div class="text-xs font-bold opacity-75 mb-1 uppercase tracking-widest">LIVE PREVIEW</div>
            <div class="text-xl font-black" id="previewTitle"></div>
        </div>

        {{-- SPECIFIC FIELDS --}}
        <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100">
            <h3 class="text-lib-navy font-black text-lg mb-6 flex items-center gap-2 uppercase tracking-wide">
                <span>⚙️</span> Specific Metadata
            </h3>

            @if(strtolower($asset->type) == 'photo')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="dateTaken" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            📅 Date Taken
                        </label>
                        <input type="date" 
                               name="metadata[dateTaken]" 
                               id="dateTaken"
                               value="{{ old('metadata.dateTaken', $asset->getDetail('dateTaken')) }}" 
                               class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all cursor-pointer">
                    </div>
                    <div>
                        <label for="resolution" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            📐 Resolution
                        </label>
                        <input type="text" 
                               name="metadata[resolution]" 
                               id="resolution"
                               value="{{ old('metadata.resolution', $asset->getDetail('resolution')) }}" 
                               placeholder="e.g., 1920x1080"
                               class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all placeholder-slate-300">
                    </div>
                </div>
            @elseif(strtolower($asset->type) == 'video')
                {{-- Video Thumbnail Update --}}
                <div class="mb-8">
                    <label for="thumbnail" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                        🖼️ Video Thumbnail
                        <span class="normal-case opacity-50 ml-1 font-normal">(Optional, replaces existing)</span>
                    </label>
                    <input type="file" 
                           name="thumbnail" 
                           id="thumbnail" 
                           accept="image/*"
                           class="peer w-full bg-white border-2 border-dashed border-slate-200 rounded-2xl p-3 text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-lib-blue file:text-white hover:file:bg-lib-navy hover:border-lib-sky transition-all cursor-pointer">
                    @if($asset->thumbnail_path)
                        <div class="mt-4 flex flex-col items-start bg-white p-3 rounded-2xl inline-block shadow-sm border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">Current Thumbnail</p>
                            <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" 
                                 alt="Current Thumbnail" 
                                 class="h-24 w-auto rounded-xl border border-slate-200 shadow-sm">
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="duration" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            ⏱️ Duration (seconds) <span class="text-[10px] bg-slate-100 text-slate-400 px-2 py-0.5 rounded-full ml-2">AUTO-CALCULATED</span>
                        </label>
                        <input type="number" 
                               name="metadata[duration]" 
                               id="duration"
                               value="{{ old('metadata.duration', $asset->getDetail('duration')) }}" 
                               readonly
                               class="w-full bg-slate-50 border-transparent focus:border-slate-200 focus:ring-0 rounded-2xl font-bold text-slate-500 py-4 px-5 shadow-none cursor-not-allowed">
                        <div id="durationDisplay" class="mt-2 text-xs font-bold text-lib-blue pl-1"></div>
                    </div>
                    <div>
                        <label for="resolution" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            📐 Resolution
                        </label>
                        <input type="text" 
                               name="metadata[resolution]" 
                               id="videoResolution"
                               value="{{ old('metadata.resolution', $asset->getDetail('resolution')) }}" 
                               placeholder="e.g., 1920x1080"
                               class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all placeholder-slate-300">
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="dateRecoded" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            📹 Date Recorded
                        </label>
                        <input type="date" 
                               name="metadata[dateRecorded]" 
                               id="dateRecoded"
                               value="{{ old('metadata.dateRecorded', $asset->getDetail('dateRecorded')) }}" 
                               class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all cursor-pointer">
                    </div>
                </div>
            @elseif(strtolower($asset->type) == 'ebook')
                {{-- E-book Cover Image Update --}}
                <div class="mb-8">
                    <label for="thumbnail" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                        🖼️ Cover Image
                        <span class="normal-case opacity-50 ml-1 font-normal">(Optional, replaces existing)</span>
                    </label>
                    <input type="file" 
                           name="thumbnail" 
                           id="thumbnail" 
                           accept="image/*"
                           class="peer w-full bg-white border-2 border-dashed border-slate-200 rounded-2xl p-3 text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-lib-blue file:text-white hover:file:bg-lib-navy hover:border-lib-sky transition-all cursor-pointer">
                    @if($asset->thumbnail_path)
                        <div class="mt-4 flex flex-col items-start bg-white p-3 rounded-2xl inline-block shadow-sm border border-slate-100">
                            <p class="text-xs font-bold text-slate-400 mb-2 uppercase tracking-wide">Current Cover</p>
                            <img src="{{ asset('storage/' . $asset->thumbnail_path) }}" 
                                 alt="Current Cover" 
                                 class="h-32 w-auto rounded-xl border border-slate-200 shadow-sm">
                        </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="author" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            ✍️ Author
                        </label>
                        <input type="text" 
                               name="metadata[author]" 
                               id="author"
                               value="{{ old('metadata.author', $asset->getDetail('author')) }}" 
                               required 
                               class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all">
                    </div>
                    <div>
                        <label for="isbn" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            🔢 ISBN
                        </label>
                        <input type="text" 
                               name="metadata[isbn]" 
                               id="isbn"
                               value="{{ old('metadata.isbn', $asset->getDetail('isbn')) }}" 
                               class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all placeholder-slate-300">
                    </div>
                    <div>
                        <label for="pageCount" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            📄 Page Count
                        </label>
                        <input type="number" 
                               name="metadata[pageCount]" 
                               id="pageCount"
                               value="{{ old('metadata.pageCount', $asset->getDetail('pageCount')) }}" 
                               min="0"
                               class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all">
                    </div>
                    <div>
                        <label for="format" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-3 ml-1">
                            📚 Format
                        </label>
                        <div class="relative">
                            <select name="metadata[format]" 
                                    id="format"
                                    required 
                                    class="w-full bg-white border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all appearance-none cursor-pointer">
                                <option value="PDF" @selected($asset->getDetail('format') == 'PDF')>PDF</option>
                                <option value="EPUB" @selected($asset->getDetail('format') == 'EPUB')>EPUB</option>
                                <option value="MOBI" @selected($asset->getDetail('format') == 'MOBI')>MOBI</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Progress Bar --}}
        <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6 mt-8 mb-6">
            <div class="flex justify-between mb-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Form Completion</span>
                <span id="progressPercent" class="text-xs font-black text-lib-blue">0%</span>
            </div>
            <div class="bg-slate-200 rounded-full h-3 overflow-hidden">
                <div id="progressBar" class="bg-gradient-to-r from-lib-blue to-lib-navy h-full w-0 transition-all duration-500 ease-out"></div>
            </div>
        </div>

        <button type="submit" 
                id="submitBtn"
                class="w-full bg-lib-blue text-white py-5 rounded-3xl font-black text-xl shadow-2xl shadow-lib-blue/20 hover:bg-lib-navy hover:shadow-lib-navy/30 hover:-translate-y-1 active:translate-y-0 transition-all flex items-center justify-center gap-3">
            <span>💾</span> Update Asset
        </button>
    </form>
</div>

<script>
// Update live preview
function updatePreview(value) {
    const preview = document.getElementById('titlePreview');
    const previewTitle = document.getElementById('previewTitle');
    
    if (value.trim()) {
        preview.classList.remove('hidden', 'scale-95', 'opacity-0');
        preview.classList.add('scale-100', 'opacity-100');
        previewTitle.textContent = value;
    } else {
        preview.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            if (!value.trim()) preview.classList.add('hidden');
        }, 300);
    }
    
    updateProgress();
}

// Handle file change
function handleFileChange(input) {
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    
    if (input.files && input.files[0]) {
        filePreview.classList.remove('hidden');
        fileName.textContent = input.files[0].name;
    } else {
        filePreview.classList.add('hidden');
    }
    
    updateProgress();
}

// Update duration display for videos
function updateDurationDisplay(seconds) {
    const display = document.getElementById('durationDisplay');
    if (seconds && seconds > 0) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        display.textContent = `⏰ ${mins} minute${mins !== 1 ? 's' : ''} ${secs} second${secs !== 1 ? 's' : ''}`;
    } else {
        display.textContent = '';
    }
    updateProgress();
}

// Update progress bar
function updateProgress() {
    const form = document.getElementById('assetForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let filled = 0;
    
    inputs.forEach(input => {
        if (input.value.trim() !== '') filled++;
    });
    
    const percent = Math.round((filled / inputs.length) * 100);
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressPercent').textContent = percent + '%';
}

// Add input listeners to all form fields
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('assetForm');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', updateProgress);
        input.addEventListener('change', updateProgress);
    });
    
    // Final progress calculation
    updateProgress();
    
    // Initial duration display if exists
    const durationInput = document.getElementById('duration');
    if (durationInput && durationInput.value) {
        updateDurationDisplay(durationInput.value);
    }

    // SweetAlert2 Confirmation on Submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Ready to Update?',
            text: "Are you sure you want to save these changes?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1d6a99',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Update it!',
            cancelButtonText: 'Review again',
            reverseButtons: true,
            background: '#fff',
            customClass: {
                title: 'font-black text-lib-navy',
                popup: 'rounded-3xl shadow-2xl',
                confirmButton: 'rounded-xl font-bold px-6 py-3',
                cancelButton: 'rounded-xl font-bold px-6 py-3'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <svg class="animate-spin h-5 w-5 mr-3 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating Asset...
                `;
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                
                // Submit form
                form.submit();
            }
        });
    });
});
</script>
</div>
@endsection