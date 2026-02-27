@extends('layouts.admin')

@section('title', 'Manage Slideshow')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{ editModalOpen: false, editTitle: '', editId: null }">



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

    {{-- Header --}}
    <div class="mb-10">
        <h1 class="text-4xl font-black text-lib-navy uppercase tracking-tighter mb-2">Manage Slideshow</h1>
        <p class="text-slate-500 font-medium text-lg">Curate the featured images on the homepage.</p>
    </div>

    {{-- Add New Slide Form --}}
    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100 mb-10 relative overflow-hidden">
        <h2 class="text-xl font-black text-lib-navy mb-6 flex items-center gap-2 uppercase tracking-wide relative z-10">
             Add New Slide
        </h2>
        
        <form action="{{ route('admin.slideshow.store') }}" method="POST" enctype="multipart/form-data" class="relative z-10" novalidate onsubmit="return validateForm(event)">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-5">
                    <label for="title" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2 ml-1">Title <span class="text-red-400">*</span></label>
                    <input type="text" id="title" name="title" required placeholder="e.g. Library Hall"
                           class="w-full bg-slate-50 border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all">
                </div>

                <div class="md:col-span-5">
                    <label for="slideshow_pic" class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2 ml-1">Image <span class="text-red-400">*</span></label>
                    <input type="file" id="slideshow_pic" name="slideshow_pic" required accept="image/*"
                           class="w-full bg-slate-50 border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-slate-500 py-3.5 px-5 shadow-sm hover:shadow-md transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-lib-light file:text-lib-navy hover:file:bg-lib-sky hover:file:text-white cursor-pointer">
                </div>

                <div class="md:col-span-2">
                    <button type="submit" 
                            class="w-full bg-lib-navy hover:bg-lib-sky text-white py-4 rounded-2xl font-black shadow-lg shadow-lib-navy/20 hover:shadow-lib-sky/30 hover:-translate-y-1 active:translate-y-0 transition-all flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Slideshow Grid --}}
    <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-100">
            <h2 class="text-xl font-black text-lib-navy flex items-center gap-2 uppercase tracking-wide">
                <span>🖼️</span> Current Slides <span class="bg-lib-light text-lib-sky px-3 py-1 rounded-full text-xs ml-2">{{ count($slideshows) }}</span>
            </h2>
        </div>
        
        @if(count($slideshows) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 p-8">
                @foreach($slideshows as $slide)
                    <div class="group relative bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-slate-100">
                        {{-- Image --}}
                        <div class="aspect-video w-full bg-slate-100 relative overflow-hidden">
                            @if($slide->slideshow_pic)
                                <img src="{{ asset('storage/' . $slide->slideshow_pic) }}" 
                                     alt="{{ $slide->title }}"
                                     class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-4xl text-slate-300">
                                    🖼️
                                </div>
                            @endif

                            {{-- Overlay Actions --}}
                            <div class="absolute inset-x-0 top-0 p-4 flex justify-end gap-2 transition-all duration-300">
                                <button @click="editId = {{ $slide->id }}; editTitle = {{ json_encode($slide->title) }}; editModalOpen = true"
                                        class="w-10 h-10 rounded-full bg-white/90 backdrop-blur text-blue-600 shadow-lg flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all transform hover:scale-110 border-0 p-0"
                                        title="Edit Slide">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                
                                <form action="{{ route('admin.slideshow.destroy', $slide->id) }}" method="POST" class="inline" onsubmit="return confirmDelete(event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-10 h-10 rounded-full bg-white/90 backdrop-blur text-red-500 shadow-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all transform hover:scale-110 border-0 p-0"
                                            title="Delete Slide">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Title Badge --}}
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6 pt-12">
                                <h3 class="text-white font-bold text-lg leading-tight drop-shadow-md">
                                    {{ $slide->title }}
                                </h3>
                                <p class="text-white/70 text-xs font-medium mt-1">
                                    Added {{ $slide->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center text-4xl mx-auto mb-6 text-slate-300">🖼️</div>
                <h3 class="text-xl font-bold text-slate-600 mb-2">No slides found</h3>
                <p class="text-slate-400 font-medium">Add your first slide above to get started.</p>
            </div>
        @endif
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModalOpen" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-8 shadow-2xl transform transition-all"
             @click.away="editModalOpen = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4">
            
            <h3 class="text-2xl font-black text-lib-navy mb-6 uppercase tracking-tight">Edit Slide</h3>
            
            <form :action="`{{ url('admin/slideshow') }}/${editId}`" method="POST" enctype="multipart/form-data" novalidate onsubmit="return validateForm(event)">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <div>
                        <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2 ml-1">Title</label>
                        <input type="text" name="title" x-model="editTitle" required 
                               class="w-full bg-slate-50 border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-lib-navy py-4 px-5 shadow-sm hover:shadow-md focus:shadow-lg transition-all">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-400 text-xs uppercase tracking-wider mb-2 ml-1">New Image <span class="text-slate-300 lowercase font-normal">(optional)</span></label>
                        <input type="file" name="slideshow_pic" accept="image/*"
                               class="w-full bg-slate-50 border-transparent focus:border-lib-sky focus:ring-0 rounded-2xl font-bold text-slate-500 py-3.5 px-5 shadow-sm hover:shadow-md transition-all file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-lib-light file:text-lib-navy hover:file:bg-lib-sky hover:file:text-white cursor-pointer">
                    </div>
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="button" @click="editModalOpen = false" 
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-600 py-3.5 rounded-2xl font-bold text-sm uppercase tracking-widest transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-lib-navy hover:bg-lib-sky text-white py-3.5 rounded-2xl font-black text-sm uppercase tracking-widest shadow-lg shadow-lib-navy/20 hover:shadow-lib-sky/30 transition-all">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>


        function confirmDelete(event) {
            event.preventDefault();
            const form = event.target;
            
            Swal.fire({
                title: 'Delete Slide?',
                text: "Are you sure you want to remove this slide?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                background: '#fff',
                customClass: {
                    title: 'font-black text-slate-800',
                    popup: 'rounded-3xl shadow-2xl',
                    confirmButton: 'rounded-xl font-bold px-6 py-3',
                    cancelButton: 'rounded-xl font-bold px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

</div>
@endsection
