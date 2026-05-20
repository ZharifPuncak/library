@extends('layouts.app')

@section('title', 'Slider')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-2 rounded-lg text-xs font-medium">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-[11px] font-bold uppercase text-lib-sky mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-lib-sky"></span>
                Home hero
            </div>
            <h1 class="text-2xl font-bold text-lib-navy leading-tight">Slider</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-2xl">Manage the images that rotate across the home page hero area.</p>
        </div>

        <div class="flex items-center gap-3">
            <div class="bg-white border border-slate-100 rounded-lg px-4 py-3 shadow-sm">
                <p class="text-[10px] font-bold uppercase text-slate-400">Total slides</p>
                <p class="text-2xl font-bold text-lib-navy leading-none mt-1">{{ $slides->count() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[380px_minmax(0,1fr)] gap-6 items-start">
        <section class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-base font-bold text-lib-navy">Add slide</h2>
                <p class="text-sm text-slate-500 mt-1">Use a wide image for the cleanest home page crop.</p>
            </div>

            <form method="POST" action="{{ route('slider.store') }}" enctype="multipart/form-data" class="p-6 space-y-5" x-data="{ fileName: '' }">
                @csrf

                @php
                    $floatInput = 'peer w-full px-4 pt-4 pb-2 rounded-xl border-2 border-slate-200 focus:border-lib-sky focus:outline-none text-slate-800 bg-white placeholder-transparent transition-colors';
                    $floatLabel = 'absolute left-3 -top-2.5 px-1.5 bg-white text-xs font-semibold text-slate-500 peer-focus:text-lib-sky peer-focus:-top-2.5 peer-focus:left-3 peer-focus:text-xs peer-placeholder-shown:top-3.5 peer-placeholder-shown:left-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-normal transition-all pointer-events-none';
                @endphp

                <div class="relative">
                    <input id="title" type="text" name="title" value="{{ old('title') }}" required placeholder=" "
                           class="{{ $floatInput }}">
                    <label for="title" class="{{ $floatLabel }}">Slide title</label>
                </div>

                <div>
                    <label for="slider_pic" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">
                        Image
                    </label>
                    <label for="slider_pic" class="group flex min-h-36 cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center transition-colors hover:border-lib-sky hover:bg-lib-light">
                        <span class="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-full bg-white text-lib-sky shadow-sm group-hover:bg-lib-sky group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </span>
                        <span class="text-sm font-bold text-slate-700">Choose an image</span>
                        <span class="text-xs text-slate-400 mt-1" x-text="fileName || 'PNG, JPG, or WEBP up to 25 MB'"></span>
                    </label>
                    <input id="slider_pic" type="file" name="slider_pic" accept="image/*" required @change="fileName = $event.target.files[0]?.name || ''"
                           class="sr-only">
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-lib-navy hover:bg-lib-sky text-white text-xs font-bold transition-colors shadow-md shadow-lib-navy/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Slide
                </button>
            </form>
        </section>

        <section class="bg-white rounded-lg shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-lib-navy">Current slides</h2>
                    <p class="text-sm text-slate-500">Newest slides appear first in this admin view.</p>
                </div>
                <span class="inline-flex w-fit items-center rounded-full bg-slate-50 px-3 py-1 text-[10px] font-bold uppercase text-slate-500">
                    {{ $slides->count() }} {{ Str::plural('slide', $slides->count()) }}
                </span>
            </div>

            <div class="p-6">
                @if($slides->isEmpty())
                    <div class="flex min-h-72 flex-col items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center">
                        <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-white text-lib-sky shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-7 w-7"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 3v5h5M8 15l2-2 2 2 3-4 3 4"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-lib-navy">No slides yet</h3>
                        <p class="text-sm text-slate-500 mt-1">Add the first slider image to start filling the home page hero.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 2xl:grid-cols-3 gap-5">
                        @foreach($slides as $slide)
                            <article class="group overflow-hidden rounded-lg border border-slate-100 bg-slate-50 transition-all hover:-translate-y-0.5 hover:border-lib-sky/40 hover:shadow-lg hover:shadow-lib-navy/5">
                                <div class="relative aspect-[16/9] bg-slate-100">
                                    <img src="{{ asset('storage/' . $slide->slider_pic) }}" alt="{{ $slide->title }}"
                                         class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.03]">
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-lib-navy/80 to-transparent p-4">
                                        <p class="text-sm font-bold text-white truncate">{{ $slide->title }}</p>
                                    </div>
                                </div>
                                <div class="p-4 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[10px] font-bold uppercase text-slate-400">Uploaded</p>
                                        <p class="text-sm font-semibold text-slate-700">{{ $slide->created_at?->format('M j, Y') }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('slider.destroy', $slide) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="confirmDeleteSlide(event)"
                                                class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-red-100 text-red-600 hover:bg-red-500 hover:border-red-500 hover:text-white transition-colors"
                                                title="Delete slide">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-4 w-4"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            title: 'Upload failed',
            html: `<ul class="text-left text-sm space-y-1 list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>`,
            icon: 'error',
            confirmButtonColor: '#0369a1',
            confirmButtonText: 'OK',
            customClass: { popup: 'rounded-lg shadow-lg' },
        });
    });
</script>
@endif

<script>
    function confirmDeleteSlide(event) {
        event.preventDefault();
        const form = event.target.closest('form');
        Swal.fire({
            title: 'Delete this slide?',
            text: 'This will permanently remove the slide from the slider.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: true,
            customClass: { popup: 'rounded-lg shadow-lg' },
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    }
</script>
@endsection
